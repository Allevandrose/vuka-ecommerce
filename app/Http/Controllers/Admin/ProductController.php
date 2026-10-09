<?php

namespace App\Http\Controllers\Admin;

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
        $query = Product::with(['category', 'brand', 'source']);

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($source = $request->input('source')) {
            if (in_array($source, ['admin', 'vendor'], true)) {
                $query->where('source_type', $source);
            }
        }

        if ($categoryId = $request->input('category')) {
            $query->where('category_id', $categoryId);
        }

        if ($brandId = $request->input('brand')) {
            $query->where('brand_id', $brandId);
        }

        if ($status = $request->input('status')) {
            if (in_array($status, ['draft', 'active', 'pending_review', 'rejected', 'disabled', 'archived'], true)) {
                $query->where('status', $status);
            }
        }

        $sort = $request->input('sort', 'newest');
        $query->orderBy(match ($sort) {
            'name' => 'name',
            'price_asc' => 'price',
            'price_desc' => 'price',
            'stock' => 'stock_quantity',
            'rating' => 'rating_avg',
            default => 'created_at',
        }, match ($sort) {
            'name' => 'asc',
            'price_asc' => 'asc',
            'price_desc' => 'desc',
            'stock' => 'asc',
            'rating' => 'desc',
            default => 'desc',
        });

        $products = $query->paginate(20)->withQueryString();

        $categories = Category::topLevel()->ordered()->with('children')->get();
        $brands = Brand::active()->ordered()->get();

        $stats = [
            'total' => Product::count(),
            'active' => Product::where('status', 'active')->count(),
            'pending' => Product::where('status', 'pending_review')->count(),
            'disabled' => Product::where('status', 'disabled')->count(),
            'vendor_owned' => Product::where('source_type', 'vendor')->count(),
            'admin_owned' => Product::where('source_type', 'admin')->count(),
        ];

        return view('admin.products.index', compact('products', 'categories', 'brands', 'stats'));
    }

    // ============================================
    // CREATE
    // ============================================

    public function create(): View
    {
        $categories = $this->flatCategories();
        $brands = Brand::active()->ordered()->get();

        return view('admin.products.create', compact('categories', 'brands'));
    }

    // ============================================
    // STORE
    // ============================================

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateProduct($request);

        try {
            DB::beginTransaction();

            $validated['source_type'] = 'admin';
            $validated['source_id'] = Auth::id();

            $status = $validated['status'] ?? 'draft';
            if ($status === 'active' || $status === 'pending_review') {
                $validated['status'] = 'active';
                $validated['published_at'] = $validated['published_at'] ?? now();
            }

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
                ->route('admin.products.index')
                ->with('success', "Product \"{$product->name}\" created successfully.");
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Product creation failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['general' => 'Failed to create product: ' . $e->getMessage()]);
        }
    }

    // ============================================
    // SHOW
    // ============================================

    public function show(Product $product): View
    {
        $product->load(['category', 'brand', 'source', 'reviews.user', 'complaints.user']);

        $attributeDefinitions = Attribute::active()
            ->forCategory($product->category_id)
            ->ordered()
            ->get();

        return view('admin.products.show', compact('product', 'attributeDefinitions'));
    }

    // ============================================
    // EDIT
    // ============================================

    public function edit(Product $product): View
    {
        $categories = $this->flatCategories();
        $brands = Brand::active()->ordered()->get();

        $attributeDefinitions = Attribute::active()
            ->forCategory($product->category_id)
            ->ordered()
            ->get();

        return view('admin.products.edit', compact('product', 'categories', 'brands', 'attributeDefinitions'));
    }

    // ============================================
    // UPDATE
    // ============================================

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $this->validateProduct($request, $product);

        try {
            DB::beginTransaction();

            $newStatus = $validated['status'] ?? $product->status;

            if ($newStatus === 'active' && $product->status !== 'active') {
                $validated['published_at'] = $validated['published_at'] ?? now();
            }

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
                ->route('admin.products.index')
                ->with('success', "Product \"{$product->name}\" updated successfully.");
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Product update failed: ' . $e->getMessage(), [
                'product_id' => $product->id,
                'user_id' => Auth::id(),
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
        $name = $product->name;

        try {
            $this->deleteOrphanedImages($product->images, []);

            $product->delete();

            return redirect()
                ->route('admin.products.index')
                ->with('success', "Product \"{$name}\" deleted.");
        } catch (\Throwable $e) {
            Log::error('Product delete failed: ' . $e->getMessage(), [
                'product_id' => $product->id,
            ]);

            return redirect()
                ->route('admin.products.index')
                ->with('error', 'Failed to delete product. Check logs.');
        }
    }

    // ============================================
    // WORKFLOW
    // ============================================

    public function approve(Product $product): RedirectResponse
    {
        if ($product->status !== 'pending_review') {
            return back()->with('error', 'Only products in pending review can be approved.');
        }

        $product->approve();

        return back()->with('success', "Product \"{$product->name}\" approved and is now live.");
    }

    public function reject(Request $request, Product $product): RedirectResponse
    {
        if ($product->status !== 'pending_review') {
            return back()->with('error', 'Only products in pending review can be rejected.');
        }

        $reason = $request->input('rejection_reason');
        if (!$reason || trim($reason) === '') {
            return back()->with('error', 'A rejection reason is required.');
        }

        $product->reject($reason);

        return back()->with('success', "Product \"{$product->name}\" rejected.");
    }

    public function disable(Request $request, Product $product): RedirectResponse
    {
        $reason = $request->input('disabled_reason');
        if (!$reason || trim($reason) === '') {
            return back()->with('error', 'A disable reason is required.');
        }

        $product->disable(Auth::user(), $reason);

        return back()->with('success', "Product \"{$product->name}\" has been disabled and is now invisible site-wide.");
    }

    public function enable(Product $product): RedirectResponse
    {
        if ($product->status !== 'disabled') {
            return back()->with('error', 'Only disabled products can be re-enabled.');
        }

        $product->enable();

        return back()->with('success', "Product \"{$product->name}\" has been re-enabled.");
    }

    public function toggleFeatured(Product $product): RedirectResponse
    {
        $product->update(['is_featured' => !$product->is_featured]);

        $state = $product->is_featured ? 'featured' : 'unfeatured';

        return back()->with('success', "Product \"{$product->name}\" is now {$state}.");
    }

    public function archive(Product $product): RedirectResponse
    {
        $product->archive();

        return back()->with('success', "Product \"{$product->name}\" archived.");
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

            'is_verified' => ['nullable', 'boolean'],
            'is_certified' => ['nullable', 'boolean'],
            'condition' => ['nullable', 'in:new,used,refurbished'],

            'status' => ['nullable', 'in:draft,active,pending_review,rejected,disabled,archived'],
            'is_featured' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],

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

        foreach (
            [
                'track_inventory',
                'is_local',
                'is_international',
                'is_verified',
                'is_certified',
                'is_featured',
            ] as $bool
        ) {
            $validated[$bool] = (bool) ($validated[$bool] ?? false);
        }

        foreach (
            [
                'brand_id',
                'short_description',
                'description',
                'cost_price',
                'compare_at_price',
                'weight',
                'length',
                'width',
                'height',
                'lead_time_days',
                'meta_title',
                'meta_description',
                'meta_keywords',
                'published_at',
            ] as $field
        ) {
            if (isset($validated[$field]) && $validated[$field] === '') {
                $validated[$field] = null;
            }
        }

        $validated['stock_quantity'] = (int) ($validated['stock_quantity'] ?? 0);
        $validated['currency'] = $validated['currency'] ?? 'KES';

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

            // Skip duplicates within this batch
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
     * Reconcile the product's existing images with the submitted form state.
     *
     * Contract:
     *   - `images_submitted=1` is sent ONLY by the edit form's images section.
     *     If absent, we leave the product's images untouched (return null).
     *   - `kept_image_indices[]` lists the indices (into the product's OLD
     *     images array) that the admin chose to keep.
     *   - `image_files[]` are new files to append.
     *   - `primary_image_index` is the index in the MERGED array
     *     (kept existing first, then new uploads) that should be the primary.
     *
     * Safety:
     *   - Deduplicates by path (kills any historical duplicates in the DB).
     *   - Skips duplicated uploads within the same request.
     *   - Deletes files on disk that are no longer referenced.
     */
    protected function mergeImageState(Request $request, Product $product): ?array
    {
        // Explicit "the images section was submitted" flag.
        if (!$request->boolean('images_submitted')) {
            return null;
        }

        $oldImages = $product->images ?? [];

        // Normalise kept indices: integers, deduped, validated against old array.
        $keptIndices = collect($request->input('kept_image_indices', []))
            ->map(fn($i) => (int) $i)
            ->filter(fn($i) => isset($oldImages[$i]))
            ->unique()
            ->values()
            ->all();

        // Build the kept list, deduplicating by path.
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

        // Upload new files, skipping any that collide with an already-kept path.
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
                // Collision with an already-kept path — skip.
                continue;
            }
            $seenPaths[$path] = true;

            $uploaded[] = [
                'path' => $path,
                'alt' => null,
                'sort' => 0,
            ];
        }

        // Merge and reindex sort.
        $merged = array_values(array_merge($kept, $uploaded));
        foreach ($merged as $i => $img) {
            $merged[$i]['sort'] = $i;
        }

        // Primary.
        $primaryIndex = $request->input('primary_image_index');
        $primaryPath = null;

        if ($primaryIndex !== null && isset($merged[(int) $primaryIndex])) {
            $primaryPath = $merged[(int) $primaryIndex]['path'];
        } elseif (!empty($merged)) {
            $primaryPath = $merged[0]['path'];
        }

        // Delete files no longer referenced.
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
