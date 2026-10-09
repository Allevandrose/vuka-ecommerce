@php
    $ogImage = null;
@endphp

<x-shop-layout :title="$title" :meta-description="$metaDescription">

    {{-- BREADCRUMB --}}
    <nav class="flex items-center gap-2 text-xs text-muted py-4">
        <a href="{{ route('home') }}" class="hover:text-moss transition-colors">
            <i class="fas fa-home"></i> Home
        </a>
        <i class="fas fa-chevron-right text-[8px] text-muted/50"></i>
        <span class="font-medium text-ink">Search</span>
    </nav>

    {{-- EMPTY QUERY STATE --}}
    @if ($query === '')
        <section class="py-12 text-center max-w-xl mx-auto">
            <div class="w-16 h-16 rounded-full bg-paper flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-search text-2xl text-moss"></i>
            </div>
            <h1 class="font-serif text-2xl sm:text-3xl font-semibold text-ink mb-2">
                Search VukaShop
            </h1>
            <p class="text-sm text-muted mb-8">
                Find products by name, brand, or description.
            </p>

            {{-- Inline search form --}}
            <form method="GET" action="{{ route('search') }}" class="max-w-md mx-auto mb-10">
                <div class="flex items-center gap-2 bg-white border border-line rounded-full px-5 py-3 shadow-sm">
                    <i class="fas fa-search text-muted"></i>
                    <input type="text" name="q" value="{{ old('q') }}"
                           placeholder="What are you looking for?"
                           autofocus
                           class="bg-transparent outline-none w-full text-base placeholder:text-muted/70">
                    <button type="submit"
                            class="shrink-0 bg-moss text-white px-5 py-2 rounded-full text-sm font-semibold hover:bg-moss/90 transition">
                        Search
                    </button>
                </div>
            </form>

            {{-- Popular categories --}}
            @if (isset($categories) && $categories->isNotEmpty())
                <div class="text-left">
                    <p class="text-xs text-muted uppercase tracking-wide mb-3 text-center">
                        Or browse popular categories
                    </p>
                    <div class="flex flex-wrap justify-center gap-2">
                        @foreach ($categories as $category)
                            <a href="{{ route('category.show', $category->slug) }}"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-line rounded-full text-sm font-medium hover:border-moss/40 hover:shadow-sm transition">
                                @if ($category->icon)
                                    <i class="fas {{ $category->icon }} text-xs text-moss"></i>
                                @endif
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </section>
    @else
        {{-- RESULTS HEADER --}}
        <section class="py-6">
            <h1 class="font-serif text-2xl sm:text-3xl font-semibold text-ink">
                Search results for <span class="text-moss">"{{ $query }}"</span>
            </h1>
            <p class="text-sm text-muted mt-1">
                @if ($products->total() > 0)
                    <strong class="text-ink">{{ $products->total() }}</strong> {{ Str::plural('product', $products->total()) }} found
                @else
                    No matches found
                @endif
            </p>
        </section>

        @if ($products->isEmpty())
            {{-- NO RESULTS --}}
            <div class="bg-white rounded-2xl border border-line p-12 text-center mb-12 max-w-2xl mx-auto">
                <div class="w-16 h-16 rounded-full bg-paper flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-search-minus text-2xl text-muted"></i>
                </div>
                <h2 class="font-serif text-lg font-semibold text-ink mb-2">
                    No products match "{{ $query }}"
                </h2>
                <p class="text-sm text-muted mb-6 max-w-md mx-auto">
                    Try a different search term, or browse our categories to find what you're looking for.
                </p>

                <div class="flex flex-wrap justify-center gap-2">
                    <a href="{{ route('home') }}"
                       class="inline-flex items-center gap-2 bg-moss text-white px-5 py-2.5 rounded-full text-sm font-semibold hover:bg-moss/90 transition">
                        <i class="fas fa-home text-xs"></i> Back to home
                    </a>
                </div>
            </div>
        @else
            {{-- SORT BAR --}}
            <div class="bg-white rounded-2xl border border-line px-4 py-3 mb-5 flex items-center justify-between gap-3 flex-wrap">
                <p class="text-sm text-muted hidden sm:block">
                    Showing <strong class="text-ink">{{ $products->firstItem() }}–{{ $products->lastItem() }}</strong>
                    of <strong class="text-ink">{{ $products->total() }}</strong>
                </p>

                <form method="GET" class="flex items-center gap-2">
                    <input type="hidden" name="q" value="{{ $query }}">

                    <label for="sort" class="text-xs text-muted whitespace-nowrap">Sort by</label>
                    <select id="sort" name="sort" onchange="this.form.submit()"
                            class="text-sm rounded-lg border-gray-300 shadow-sm focus:border-moss focus:ring-moss bg-paper py-1.5 pl-3 pr-8">
                        <option value="relevance" {{ $sort === 'relevance' ? 'selected' : '' }}>Relevance</option>
                        <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Newest</option>
                        <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Price: low to high</option>
                        <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Price: high to low</option>
                        <option value="rating" {{ $sort === 'rating' ? 'selected' : '' }}>Top rated</option>
                    </select>
                </form>
            </div>

            {{-- RESULTS GRID --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3.5 mb-8">
                @foreach ($products as $product)
                    <x-product-card :product="$product" :show-category="true" />
                @endforeach
            </div>

            {{-- PAGINATION --}}
            @if ($products->hasPages())
                <div class="mb-12">
                    {{ $products->links() }}
                </div>
            @endif
        @endif
    @endif

</x-shop-layout>