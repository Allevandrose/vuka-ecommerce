<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SearchController extends Controller
{
    /**
     * Public product search.
     * Endpoint: /search?q=...
     */
    public function index(Request $request): View
    {
        $query = trim((string) $request->input('q', ''));
        $sort = $request->input('sort', 'relevance');

        // Empty query → show prompt
        if ($query === '') {
            $categories = Category::active()->topLevel()->ordered()->limit(8)->get();

            return view('shop.search', [
                'query' => '',
                'products' => null,
                'categories' => $categories,
                'sort' => $sort,
                'title' => 'Search | ' . config('app.name', 'VukaShop'),
                'metaDescription' => 'Search products on VukaShop.',
            ]);
        }

        // Sanitise for FULLTEXT: MySQL treats certain chars as operators.
        // We wrap the phrase in double quotes to force a literal phrase match.
        // This kills "+", "-", "*" operator behaviour that breaks the index.
        $searchTerm = '"' . str_replace('"', '', $query) . '"';

        $builder = Product::visible()
            ->where(function ($q) use ($searchTerm, $query) {
                // FULLTEXT on name, short_description, description
                $q->whereRaw(
                    'MATCH (name, short_description, description) AGAINST (? IN BOOLEAN MODE)',
                    [$searchTerm]
                )
                    // Fallback LIKE — catches substrings FULLTEXT misses at <50% word freq
                    ->orWhere('name', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%");
            })
            ->with(['category', 'brand']);

        // Sort
        $this->applySort($builder, $sort, $searchTerm);

        $products = $builder->paginate(24)->withQueryString();

        // SEO
        $title = "\"{$query}\" — Search Results | " . config('app.name', 'VukaShop');
        $metaDescription = "Search results for \"{$query}\" on VukaShop. {$products->total()} products found.";

        return view('shop.search', compact(
            'query',
            'products',
            'sort',
            'title',
            'metaDescription'
        ));
    }

    // ============================================
    // HELPERS
    // ============================================

    /**
     * Apply sort order. Supports relevance via FULLTEXT score, plus the
     * standard sorts (price, rating, newest).
     */
    protected function applySort($query, string $sort, string $searchTerm): void
    {
        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'rating' => $query->orderByDesc('rating_avg')->orderByDesc('rating_count'),
            'newest' => $query->orderByDesc('published_at')->orderByDesc('created_at'),
            default => $query->orderByRaw(
                'MATCH (name, short_description, description) AGAINST (? IN BOOLEAN MODE) DESC',
                [$searchTerm]
            ),
        };
    }
}
