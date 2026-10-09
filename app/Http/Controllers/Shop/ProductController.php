<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Public product detail page.
     */
    public function show(string $slug): View
    {
        // Load product with relations. visible() ensures draft/disabled/deleted
        // products 404 — they never leak their existence via URL.
        $product = Product::visible()
            ->where('slug', $slug)
            ->with([
                'category',
                'brand',
                'source',
                'reviews' => function ($q) {
                    $q->where('is_approved', true)
                        ->with('user')
                        ->orderByDesc('created_at')
                        ->limit(20);
                },
            ])
            ->firstOrFail();

        // Increment view counter (quietly — no updated_at churn)
        $product->increment('views_count');

        // Attribute definitions for the product's category — used to
        // format the specs table with units and human labels.
        $attributeDefinitions = Attribute::active()
            ->forCategory($product->category_id)
            ->ordered()
            ->get();

        // Related products — same category, different product
        $related = Product::visible()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(8)
            ->get();

        // If fewer than 4 related products, top up with same brand
        if ($related->count() < 4 && $product->brand_id) {
            $extra = Product::visible()
                ->where('brand_id', $product->brand_id)
                ->where('id', '!=', $product->id)
                ->whereNotIn('id', $related->pluck('id'))
                ->limit(4 - $related->count())
                ->get();

            $related = $related->merge($extra);
        }

        // Breadcrumb
        $breadcrumb = $product->category ? $product->category->breadcrumb() : [];
        $breadcrumb[] = ['id' => null, 'name' => $product->name, 'slug' => $product->slug];

        // SEO
        $title = $product->seoTitle() . ' | ' . config('app.name', 'VukaShop');
        $metaDescription = $product->seoDescription();

        return view('shop.product', compact(
            'product',
            'attributeDefinitions',
            'related',
            'breadcrumb',
            'title',
            'metaDescription'
        ));
    }
}
