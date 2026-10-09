<x-shop-layout :title="$title" :meta-description="$metaDescription">

    {{-- BREADCRUMB --}}
    <nav class="flex items-center gap-2 text-xs text-muted py-4 overflow-x-auto no-scrollbar">
        <a href="{{ route('home') }}" class="hover:text-moss transition-colors shrink-0">
            <i class="fas fa-home"></i> Home
        </a>
        @foreach ($breadcrumb as $crumb)
            <i class="fas fa-chevron-right text-[8px] text-muted/50 shrink-0"></i>
            @if ($loop->last)
                <span class="font-medium text-ink whitespace-nowrap">{{ $crumb['name'] }}</span>
            @else
                <a href="{{ route('category.show', $crumb['slug']) }}" class="hover:text-moss transition-colors whitespace-nowrap">
                    {{ $crumb['name'] }}
                </a>
            @endif
        @endforeach
    </nav>

    {{-- CATEGORY BANNER --}}
    <section class="relative rounded-xl2 overflow-hidden mb-6"
             style="background: {{ $category->heroBackground() }};">

        @if ($category->hero_image)
            <img src="{{ asset('storage/' . $category->hero_image) }}"
                 alt="{{ $category->name }}"
                 class="absolute inset-0 w-full h-full object-cover opacity-30">
        @endif

        <div class="relative z-10 px-6 py-8 sm:px-10 sm:py-12 text-white max-w-3xl">
            <h1 class="font-serif text-3xl sm:text-5xl font-semibold tracking-tight leading-[1.05]">
                {{ $category->name }}
            </h1>
            @if ($category->description)
                <p class="text-white/80 text-sm sm:text-base mt-3 max-w-xl">
                    {{ $category->description }}
                </p>
            @endif

            <div class="mt-4 flex items-center gap-3 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/15 backdrop-blur border border-white/20">
                    <i class="fas fa-box text-[10px]"></i>
                    {{ $products->total() }} {{ Str::plural('product', $products->total()) }}
                </span>
                @if ($category->isTopLevel() && $category->children->isNotEmpty())
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/15 backdrop-blur border border-white/20">
                        <i class="fas fa-sitemap text-[10px]"></i>
                        {{ $category->children->count() }} subcategories
                    </span>
                @endif
            </div>
        </div>
    </section>

    {{-- SUBCATEGORY CHIPS (only for parent categories) --}}
    @if ($category->children->isNotEmpty())
        <div class="flex gap-2.5 overflow-x-auto no-scrollbar pb-5">
            @foreach ($category->children->where('is_active', true) as $child)
                <a href="{{ route('category.show', $child->slug) }}"
                   class="flex items-center gap-2 whitespace-nowrap border border-line bg-white text-ink rounded-full px-4 py-2 text-sm font-medium shadow-sm transition-colors hover:border-moss/40">
                    @if ($child->icon)
                        <i class="fas {{ $child->icon }} text-xs text-moss"></i>
                    @endif
                    {{ $child->name }}
                </a>
            @endforeach
        </div>
    @endif

    {{-- MAIN GRID + SIDEBAR --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 pb-12"
         x-data="{ mobileFiltersOpen: false }">

        {{-- ============================================ --}}
        {{-- FILTERS SIDEBAR (desktop) / DRAWER (mobile) --}}
        {{-- ============================================ --}}
        <aside class="hidden lg:block lg:col-span-1">
            <div class="bg-white rounded-2xl border border-line p-5 sticky top-24">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-serif text-lg font-semibold">Filters</h2>
                    @if (request()->hasAny(['brand', 'min_price', 'max_price', 'verified', 'in_stock', 'on_sale', 'q']))
                        <a href="{{ route('category.show', $category->slug) }}"
                           class="text-xs text-moss hover:text-moss/70 font-semibold">
                            Clear all
                        </a>
                    @endif
                </div>

                @include('shop.partials.category-filters', [
                    'category' => $category,
                    'availableBrands' => $availableBrands,
                    'priceRange' => $priceRange,
                ])
            </div>
        </aside>

        {{-- ============================================ --}}
        {{-- PRODUCT GRID --}}
        {{-- ============================================ --}}
        <div class="lg:col-span-3">

            {{-- Sort bar --}}
            <div class="bg-white rounded-2xl border border-line px-4 py-3 mb-5 flex items-center justify-between gap-3 flex-wrap">
                <div class="flex items-center gap-3">
                    <button type="button"
                            @click="mobileFiltersOpen = true"
                            class="lg:hidden inline-flex items-center gap-2 text-sm font-semibold text-ink bg-paper px-3 py-1.5 rounded-full">
                        <i class="fas fa-sliders-h text-xs"></i>
                        Filters
                        @if (request()->hasAny(['brand', 'min_price', 'max_price', 'verified', 'in_stock', 'on_sale', 'q']))
                            <span class="w-1.5 h-1.5 rounded-full bg-gold"></span>
                        @endif
                    </button>

                    <p class="text-sm text-muted hidden sm:block">
                        <strong class="text-ink">{{ $products->total() }}</strong>
                        {{ Str::plural('result', $products->total()) }}
                    </p>
                </div>

                {{-- Sort dropdown --}}
                <form method="GET" class="flex items-center gap-2">
                    @foreach (request()->except('sort', 'page') as $k => $v)
                        @if (is_array($v))
                            @foreach ($v as $vv)
                                <input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endif
                    @endforeach

                    <label for="sort" class="text-xs text-muted whitespace-nowrap">Sort by</label>
                    <select id="sort" name="sort" onchange="this.form.submit()"
                            class="text-sm rounded-lg border-gray-300 shadow-sm focus:border-moss focus:ring-moss bg-paper py-1.5 pl-3 pr-8">
                        <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Newest</option>
                        <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Price: low to high</option>
                        <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Price: high to low</option>
                        <option value="rating" {{ $sort === 'rating' ? 'selected' : '' }}>Top rated</option>
                        <option value="name" {{ $sort === 'name' ? 'selected' : '' }}>Name A–Z</option>
                    </select>
                </form>
            </div>

            @if ($products->isEmpty())
                {{-- Empty state --}}
                <div class="bg-white rounded-2xl border border-line p-12 text-center">
                    <div class="w-16 h-16 rounded-full bg-paper flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-box-open text-2xl text-muted"></i>
                    </div>
                    <h3 class="font-serif text-lg font-semibold text-ink mb-2">No products found</h3>
                    @if (request()->hasAny(['brand', 'min_price', 'max_price', 'verified', 'in_stock', 'on_sale', 'q']))
                        <p class="text-sm text-muted mb-4">Try removing some filters.</p>
                        <a href="{{ route('category.show', $category->slug) }}"
                           class="inline-flex items-center gap-2 bg-moss text-white px-5 py-2.5 rounded-full text-sm font-semibold hover:bg-moss/90 transition">
                            <i class="fas fa-times text-xs"></i> Clear filters
                        </a>
                    @else
                        <p class="text-sm text-muted">Check back soon — new stock coming.</p>
                    @endif
                </div>
            @else
                {{-- Product grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 gap-3.5">
                    @foreach ($products as $product)
                        <x-product-card :product="$product" :show-category="false" />
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if ($products->hasPages())
                    <div class="mt-6">
                        {{ $products->links() }}
                    </div>
                @endif
            @endif
        </div>

        {{-- ============================================ --}}
        {{-- MOBILE FILTER DRAWER --}}
        {{-- ============================================ --}}
        <div x-show="mobileFiltersOpen" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @keydown.escape.window="mobileFiltersOpen = false"
             class="lg:hidden fixed inset-0 z-50 bg-ink/40 backdrop-blur-sm flex items-end">

            <div @click.self="mobileFiltersOpen = false"
                 class="absolute inset-0"></div>

            <div x-show="mobileFiltersOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="translate-y-full"
                 x-transition:enter-end="translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="translate-y-0"
                 x-transition:leave-end="translate-y-full"
                 class="relative bg-white rounded-t-2xl w-full max-h-[85vh] overflow-y-auto">

                <div class="sticky top-0 bg-white border-b border-line px-5 py-4 flex items-center justify-between rounded-t-2xl">
                    <h2 class="font-serif text-lg font-semibold">Filters</h2>
                    <button @click="mobileFiltersOpen = false"
                            class="w-9 h-9 rounded-full bg-paper flex items-center justify-center text-ink">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="px-5 py-5">
                    @include('shop.partials.category-filters', [
                        'category' => $category,
                        'availableBrands' => $availableBrands,
                        'priceRange' => $priceRange,
                    ])
                </div>
            </div>
        </div>
    </div>

</x-shop-layout>