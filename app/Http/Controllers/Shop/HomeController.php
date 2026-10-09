<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Public homepage — loads real products from the database.
     */
    public function index(): View
    {
        // Featured products (explicitly flagged + active)
        $featured = Product::visible()
            ->where('is_featured', true)
            ->latest('published_at')
            ->limit(8)
            ->get();

        // Best sellers — most sold first
        $bestsellers = Product::visible()
            ->orderByDesc('sales_count')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        // Popular — most viewed first
        $popular = Product::visible()
            ->orderByDesc('views_count')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        // Fallback: if nothing has views yet, show newest
        if ($popular->isEmpty()) {
            $popular = Product::visible()
                ->latest()
                ->limit(8)
                ->get();
        }

        // Top-level categories for category chips
        $categories = Category::active()
            ->topLevel()
            ->ordered()
            ->limit(10)
            ->get();

        return view('welcome', compact(
            'featured',
            'bestsellers',
            'popular',
            'categories'
        ));
    }
}
