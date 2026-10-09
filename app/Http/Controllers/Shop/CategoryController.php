<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Public category listing — shows all visible products in this category
     * and all its descendant categories.
     */
    public function show(Request $request, Category $category): View
    {
        // Inactive category — 404 (don't reveal it exists)
        if (!$category->is_active) {
            abort(404);
        }

        // Collect category IDs: this one + all descendants
        $categoryIds = $this->collectDescendantIds($category);
        $categoryIds[] = $category->id;

        // Base query
        $query = Product::visible()
            ->whereIn('category_id', $categoryIds)
            ->with(['category', 'brand']);

        // Filters
        $this->applyFilters($query, $request);

        // Sort
        $sort = $request->input('sort', 'newest');
        $this->applySort($query, $sort);

        $products = $query->paginate(24)->withQueryString();

        // Available brands within this category subtree (for the filter sidebar)
        $availableBrands = Brand::active()
            ->whereIn('id', function ($q) use ($categoryIds) {
                $q->select('brand_id')
                    ->from('products')
                    ->whereIn('category_id', $categoryIds)
                    ->where('status', 'active')
                    ->whereNotNull('brand_id')
                    ->distinct();
            })
            ->orderBy('name')
            ->get();

        // Price range within this category
        $priceRange = Product::visible()
            ->whereIn('category_id', $categoryIds)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        // Breadcrumb
        $breadcrumb = $category->breadcrumb();

        // SEO
        $title = $category->meta_title
            ?: ($category->name . ' — Shop Online | ' . config('app.name', 'VukaShop'));
        $metaDescription = $category->meta_description
            ?: ('Browse ' . $category->name . ' products on VukaShop. ' . ($category->description ?: 'Fast nationwide delivery.'));

        return view('shop.category', compact(
            'category',
            'products',
            'availableBrands',
            'priceRange',
            'breadcrumb',
            'title',
            'metaDescription',
            'sort'
        ));
    }

    // ============================================
    // HELPERS
    // ============================================

    /**
     * Recursively collect IDs of all descendant categories.
     * Two-level tree — one query is enough; but this handles arbitrary depth.
     */
    protected function collectDescendantIds(Category $category): array
    {
        $ids = [];

        foreach ($category->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->collectDescendantIds($child));
        }

        return $ids;
    }

    /**
     * Apply request filters to the query.
     */
    protected function applyFilters($query, Request $request): void
    {
        // Brand filter
        if ($brandIds = $request->input('brand')) {
            $brandIds = is_array($brandIds) ? $brandIds : [$brandIds];
            $query->whereIn('brand_id', $brandIds);
        }

        // Price range
        if ($min = $request->input('min_price')) {
            if (is_numeric($min)) {
                $query->where('price', '>=', (float) $min);
            }
        }
        if ($max = $request->input('max_price')) {
            if (is_numeric($max)) {
                $query->where('price', '<=', (float) $max);
            }
        }

        // Trust flag
        if ($request->boolean('verified')) {
            $query->where('is_verified', true);
        }
        if ($request->boolean('in_stock')) {
            $query->where(function ($q) {
                $q->where('track_inventory', false)
                    ->orWhere('stock_quantity', '>', 0);
            });
        }
        if ($request->boolean('on_sale')) {
            $query->whereNotNull('compare_at_price')
                ->whereColumn('compare_at_price', '>', 'price');
        }

        // Search within category
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            });
        }
    }

    /**
     * Apply sort order to the query.
     */
    protected function applySort($query, string $sort): void
    {
        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'rating' => $query->orderByDesc('rating_avg')->orderByDesc('rating_count'),
            'name' => $query->orderBy('name', 'asc'),
            default => $query->orderByDesc('published_at')->orderByDesc('created_at'),
        };
    }
}
