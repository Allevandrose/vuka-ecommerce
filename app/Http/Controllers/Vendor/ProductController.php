<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    // ============================================
    // INDEX
    // ============================================

    public function index(Request $request): View
    {
        /** @var \App\Models\User $vendor */
        $vendor = Auth::user();

        $query = Product::query()
            ->where('source_type', 'vendor')
            ->where('source_id', $vendor->id)
            ->with(['category', 'brand']);

        if ($status = $request->input('status')) {
            $allowed = ['draft', 'pending_review', 'active', 'rejected', 'disabled', 'archived'];
            if (in_array($status, $allowed, true)) {
                $query->where('status', $status);
            }
        }

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $sort = $request->input('sort', 'newest');
        $query->orderBy(match ($sort) {
            'name' => 'name',
            'price_asc' => 'price',
            'price_desc' => 'price',
            'status' => 'status',
            default => 'created_at',
        }, match ($sort) {
            'name', 'price_asc', 'status' => 'asc',
            default => 'desc',
        });

        $products = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => Product::where('source_type', 'vendor')->where('source_id', $vendor->id)->count(),
            'active' => Product::where('source_type', 'vendor')->where('source_id', $vendor->id)->where('status', 'active')->count(),
            'pending' => Product::where('source_type', 'vendor')->where('source_id', $vendor->id)->where('status', 'pending_review')->count(),
            'rejected' => Product::where('source_type', 'vendor')->where('source_id', $vendor->id)->where('status', 'rejected')->count(),
            'draft' => Product::where('source_type', 'vendor')->where('source_id', $vendor->id)->where('status', 'draft')->count(),
            'disabled' => Product::where('source_type', 'vendor')->where('source_id', $vendor->id)->where('status', 'disabled')->count(),
        ];

        $cap = $vendor->productLimit();
        $used = $stats['total'];
        $canAdd = $vendor->canCreateProduct();

        return view('vendor.products.index', compact(
            'products', 'stats', 'cap', 'used', 'canAdd', 'sort'
        ));
    }

    // ============================================
    // CREATE
    // ============================================

    public function create(): View
    {
        /** @var \App\Models\User $vendor */
        $vendor = Auth::user();

        if (!$vendor->canCreateProduct()) {
            abort(403, "You've reached your product limit ({$vendor->productLimit()}). Contact support to increase it.");
        }

        $categories = $this->flatCategories();
        $brands = Brand::active()->ordered()->get();

        $cap = $vendor->productLimit();
        $used = $vendor->productCount();

        return view('vendor.products.create', compact('categories', 'brands', 'cap', 'used'));
    }

    // ============================================
    // STORE
    // ============================================

    public function store(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $vendor */
        $vendor = Auth::user();

        if (!$vendor->canCreateProduct()) {
            return back()
                ->withInput()
                ->withErrors(['general' => "You've reached your product limit ({$vendor->productLimit()}). Contact support to increase it."]);
        }

        $validated = $this->validateProduct($request);

        try {
            DB::beginTransaction();

            $validated['source_type'] = 'vendor';
            $validated['source_id'] = $vendor->id;
            $validated['status'] = 'pending_review';
            $validated['published_at'] = null;
            $validated['is_featured'] = false;
            $validated['is_verified'] = false;
            $validated['is_certified'] = false;

            $validated['attributes'] = $this->buildAttributePayload($request, $validated['category_id']);

            $imagesData = $this->handleImageUploads($request);
            $validated['images'] = $imagesData['images'];
            $validated['primary_image'] = $imagesData['primary_image'];
            $validated['thumbnail'] = $imagesData['thumbnail'];

            unset(
                $validated['image_files'],
                $validated['primary_image_index'],
                $validated['images_submitted'],
                $validated['kept_image_indices']
            );

            $product = Product::create($validated);

            DB::commit();

            return redirect()
                ->route('vendor.products.index')
                ->with('success', "Product \"{$product->name}\" submitted for review. You'll be notified once it's approved.");
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Vendor product creation failed: ' . $e->getMessage(), [
                'vendor_id' => $vendor->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['general' => 'Failed to create product: ' . $e->getMessage()]);
        }
    }

    // ============================================
    // EDIT
    // ============================================

    public function edit(Product $product): View
    {
        $this->authorizeOwnership($product);

        $categories = $this->flatCategories();
        $brands = Brand::active()->ordered()->get();

        return view('vendor.products.edit', compact('product', 'categories', 'brands'));
    }

    // ============================================
    // UPDATE
    // ============================================

    public function update(Request $request, Product $product): RedirectResponse
    {
        $this->authorizeOwnership($product);

        $validated = $this->validateProduct($request, $product);

        try {
            DB::beginTransaction();

            // If the product was rejected and the vendor edits it, resubmit for review.
            // If it was active/disabled/archived, editing sends it back to pending_review too —
            // any edit needs admin re-approval. This is the safe default.
            if (in_array($product->status, ['rejected', 'active'], true)) {
                $validated['status'] = 'pending_review';
                $validated['rejection_reason'] = null;
                $validated['published_at'] = null;
            }

            // Vendors never self-publish, verify, or feature.
            unset(
                $validated['is_featured'],
                $validated['is_verified'],
                $validated['is_certified']
            );

            if ($validated['status'] === 'draft') {
                unset($validated['published_at']);
            }
            // (draft is the only status a vendor can keep; anything else becomes pending_review)

            $validated['is_featured'] = false;
            $validated['is_verified'] = false;
            $validated['is_certified'] = false;

            $validated['attributes'] = $this->buildAttributePayload($request, $validated['category_id']);

            $mergedImages = $this->mergeImageState($request, $product);

            if ($mergedImages !== null) {
                $validated['images'] = $mergedImages['images'];
                $validated['primary_image'] = $mergedImages['primary_image'];
                $validated['thumbnail'] = $mergedImages['thumbnail'];
            }

            unset(
                $validated['image_files'],
                $validated['primary_image_index'],
                $validated['kept_image_indices'],
                $validated['images_submitted']
            );

            $product->update($validated);

            DB::commit();

            return redirect()
                ->route('vendor.products.index')
                ->with('success', "Product \"{$product->name}\" updated. " .
                    ($validated['status'] === 'pending_review' ? 'It will be reviewed again before going live.' : '')
                );
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Vendor product update failed: ' . $e->getMessage(), [
                'product_id' => $product->id,
                'vendor_id' => Auth::id(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['general' => 'Failed to update product: ' . $e->getMessage()]);
        }
    }

    // ============================================
    // DESTROY
    // ============================================

    public function destroy(Product $product): RedirectResponse
    {
        $this->authorizeOwnership($product);

        $name = $product->name;

        try {
            // Remove image files from disk
            $this->deleteOrphanedImages($product->images, []);

            $product->delete();

            return redirect()
                ->route('vendor.products.index')
                ->with('success', "Product \"{$name}\" deleted.");
        } catch (\Throwable $e) {
            Log::error('Vendor product delete failed: ' . $e->getMessage(), [
                'product_id' => $product->id,
            ]);

            return redirect()
                ->route('vendor.products.index')
                ->with('error', 'Failed to delete product. Check logs.');
        }
    }

    // ============================================
    // HELPERS — ownership guard
    // ============================================

    /**
     * 403 if the current vendor doesn't own this product.
     * Also 403 if it's not a vendor product at all (admin products are off-limits).
     */
    protected function authorizeOwnership(Product $product): void
    {
        /** @var \App\Models\User $vendor */
        $vendor = Auth::user();

        if (!$product->isVendorProduct()) {
            abort(403, 'This product is not a vendor product.');
        }

        if ((int) $product->source_id !== (int) $vendor->id) {
            abort(403, 'You do not have permission to modify this product.');
        }
    }

    // ============================================
    // HELPERS — validation
    // ============================================

    protected function validateProduct(Request $request, ?Product $existing = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'sku' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[A-Za-z0-9_-]+$/',
                $existing
                    ? \Illuminate\Validation\Rule::unique('products', 'sku')->ignore($existing->id)
                    : 'unique:products,sku',
            ],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],

            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:20000'],

            'cost_price' => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
            'currency' => ['nullable', 'string', 'size:3'],

            'stock_quantity' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'track_inventory' => ['nullable', 'boolean'],

            'weight' => ['nullable', 'numeric', 'min:0', 'max:99999.999'],
            'length' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'width' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'height' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'is_local' => ['nullable', 'boolean'],
            'is_international' => ['nullable', 'boolean'],
            'lead_time_days' => ['nullable', 'integer', 'min:0', 'max:365'],

            'condition' => ['nullable', 'in:new,used,refurbished'],
            'status' => ['nullable', 'in:draft,pending_review'],

            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],

            'image_files' => ['nullable', 'array', 'max:8'],
            'image_files.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'primary_image_index' => ['nullable', 'integer', 'min:0', 'max:7'],
            'images_submitted' => ['nullable', 'boolean'],
            'kept_image_indices' => ['nullable', 'array'],
            'kept_image_indices.*' => ['integer', 'min:0'],

            'attributes' => ['nullable', 'array'],
        ];

        $validated = $request->validate($rules);

        foreach (['track_inventory', 'is_local', 'is_international'] as $bool) {
            $validated[$bool] = (bool) ($validated[$bool] ?? false);
        }

        foreach (
            [
                'brand_id', 'short_description', 'description',
                'cost_price', 'compare_at_price', 'weight',
                'length', 'width', 'height', 'lead_time_days',
                'meta_title', 'meta_description', 'meta_keywords',
            ] as $field
        ) {
            if (isset($validated[$field]) && $validated[$field] === '') {
                $validated[$field] = null;
            }
        }

        $validated['stock_quantity'] = (int) ($validated['stock_quantity'] ?? 0);
        $validated['currency'] = $validated['currency'] ?? 'KES';

        // Force pending_review unless explicitly draft
        $validated['status'] = ($validated['status'] ?? null) === 'draft' ? 'draft' : 'pending_review';

        return $validated;
    }

    // ============================================
    // HELPERS — attributes
    // ============================================

    protected function buildAttributePayload(Request $request, int $categoryId): array
    {
        $input = $request->input('attributes', []);
        if (!is_array($input)) {
            return [];
        }

        $validAttributes = Attribute::active()
            ->forCategory($categoryId)
            ->get()
            ->keyBy('key');

        $payload = [];

        foreach ($validAttributes as $key => $attribute) {
            $raw = $input[$key] ?? null;

            if ($raw === null || $raw === '') {
                continue;
            }

            $value = match ($attribute->type) {
                'boolean' => filter_var($raw, FILTER_VALIDATE_BOOLEAN),
                'number' => is_numeric($raw) ? (float) $raw : null,
                'multiselect' => is_array($raw) ? array_values(array_filter(array_map('trim', $raw))) : [],
                default => is_string($raw) ? trim($raw) : (string) $raw,
            };

            if ($value === null || $value === [] || $value === '') {
                continue;
            }

            $payload[$key] = $value;
        }

        return $payload;
    }

    // ============================================
    // HELPERS — image uploads (create)
    // ============================================

    protected function handleImageUploads(Request $request): array
    {
        $files = $request->file('image_files') ?? [];
        $primaryIndex = (int) ($request->input('primary_image_index') ?? 0);

        $images = [];
        $primaryPath = null;
        $seenPaths = [];

        foreach ($files as $i => $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }

            $extension = $file->getClientOriginalExtension() ?: 'jpg';
            $filename = Str::uuid() . '.' . strtolower($extension);
            $path = $file->storeAs('products', $filename, 'public');

            if (isset($seenPaths[$path])) {
                continue;
            }
            $seenPaths[$path] = true;

            $images[] = [
                'path' => $path,
                'alt' => $request->input("image_alts.{$i}") ?: null,
                'sort' => count($images),
            ];

            if ($i === $primaryIndex && !$primaryPath) {
                $primaryPath = $path;
            }
        }

        if (!$primaryPath && !empty($images)) {
            $primaryPath = $images[0]['path'];
        }

        return [
            'images' => $images,
            'primary_image' => $primaryPath,
            'thumbnail' => $primaryPath,
        ];
    }

    // ============================================
    // HELPERS — image merge (update)
    // ============================================

    /**
     * Same contract as Admin\ProductController::mergeImageState().
     * - `images_submitted=1` required, else images are left alone.
     * - `kept_image_indices[]` — indexes of OLD images to keep.
     * - `image_files[]` — new uploads.
     * - `primary_image_index` — index in the MERGED list.
     * - Dedupes by path; deletes orphaned files from disk.
     */
    protected function mergeImageState(Request $request, Product $product): ?array
    {
        if (!$request->boolean('images_submitted')) {
            return null;
        }

        $oldImages = $product->images ?? [];

        $keptIndices = collect($request->input('kept_image_indices', []))
            ->map(fn ($i) => (int) $i)
            ->filter(fn ($i) => isset($oldImages[$i]))
            ->unique()
            ->values()
            ->all();

        $kept = [];
        $seenPaths = [];
        foreach ($keptIndices as $idx) {
            $path = $oldImages[$idx]['path'] ?? null;
            if (!$path || isset($seenPaths[$path])) {
                continue;
            }
            $seenPaths[$path] = true;
            $kept[] = $oldImages[$idx];
        }

        $uploaded = [];
        $newFiles = $request->file('image_files') ?? [];

        foreach ($newFiles as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }

            $extension = $file->getClientOriginalExtension() ?: 'jpg';
            $filename = Str::uuid() . '.' . strtolower($extension);
            $path = $file->storeAs('products', $filename, 'public');

            if (isset($seenPaths[$path])) {
                continue;
            }
            $seenPaths[$path] = true;

            $uploaded[] = [
                'path' => $path,
                'alt' => null,
                'sort' => 0,
            ];
        }

        $merged = array_values(array_merge($kept, $uploaded));
        foreach ($merged as $i => $img) {
            $merged[$i]['sort'] = $i;
        }

        $primaryIndex = $request->input('primary_image_index');
        $primaryPath = null;

        if ($primaryIndex !== null && isset($merged[(int) $primaryIndex])) {
            $primaryPath = $merged[(int) $primaryIndex]['path'];
        } elseif (!empty($merged)) {
            $primaryPath = $merged[0]['path'];
        }

        $this->deleteOrphanedImages($oldImages, $merged);

        return [
            'images' => $merged,
            'primary_image' => $primaryPath,
            'thumbnail' => $primaryPath,
        ];
    }

    protected function deleteOrphanedImages(?array $oldImages, array $newImages): void
    {
        $oldPaths = collect($oldImages ?? [])->pluck('path')->filter()->all();
        $newPaths = collect($newImages)->pluck('path')->filter()->all();

        $orphans = array_diff($oldPaths, $newPaths);

        foreach ($orphans as $path) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    // ============================================
    // HELPERS — category tree
    // ============================================

    protected function flatCategories(): array
    {
        $flat = [];
        $top = Category::topLevel()->ordered()->get();

        foreach ($top as $parent) {
            $flat[] = ['id' => $parent->id, 'name' => $parent->name, 'depth' => 0];
            foreach ($parent->children()->orderBy('sort_order')->orderBy('name')->get() as $child) {
                $flat[] = ['id' => $child->id, 'name' => $child->name, 'depth' => 1];
            }
        }

        return $flat;
    }
}