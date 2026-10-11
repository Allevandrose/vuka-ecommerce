@php
    // ---- Active filter helpers -------------------------------------------------
    $filterKeys = ['brand', 'min_price', 'max_price', 'verified', 'in_stock', 'on_sale', 'q'];
    $hasFilters = request()->hasAny($filterKeys);

    $activeChips = [];
    foreach ($filterKeys as $key) {
        if (!request()->filled($key)) { continue; }
        $value = request($key);

        if ($key === 'brand') {
            foreach ((array) $value as $b) {
                $rest = array_values(array_diff((array) $value, [$b]));
                $query = request()->except('page', 'brand');
                if ($rest) { $query['brand'] = $rest; }
                $activeChips[] = ['label' => $b, 'url' => url()->current() . ($query ? '?' . http_build_query($query) : '')];
            }
            continue;
        }

        $label = match ($key) {
            'min_price' => 'From ' . $value,
            'max_price' => 'Up to ' . $value,
            'verified'  => 'Verified',
            'in_stock'  => 'In stock',
            'on_sale'   => 'On sale',
            'q'         => '“' . $value . '”',
            default     => $value,
        };
        $query = request()->except('page', $key);
        $activeChips[] = ['label' => $label, 'url' => url()->current() . ($query ? '?' . http_build_query($query) : '')];
    }
    $activeCount = count($activeChips);

    // ---- Theme color taken from the category banner background ------------------
    // heroBackground() can return a hex, rgb(), or a gradient. We pull the first
    // color out of it and use that to theme the whole page.
    $heroBg = (string) $category->heroBackground();
    $r = 63; $g = 93; $b = 58; // fallback if no color can be found
    if (preg_match('/#([0-9a-f]{6}|[0-9a-f]{3})\b/i', $heroBg, $m)) {
        $hex = $m[1];
        if (strlen($hex) === 3) { $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2]; }
        [$r, $g, $b] = [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
    } elseif (preg_match('/rgba?\(\s*(\d{1,3})[\s,]+(\d{1,3})[\s,]+(\d{1,3})/i', $heroBg, $m)) {
        [$r, $g, $b] = [min(255, (int) $m[1]), min(255, (int) $m[2]), min(255, (int) $m[3])];
    }
    $themePrimary = sprintf('#%02x%02x%02x', $r, $g, $b);
    // Pick readable text color for buttons/banner depending on how light the color is
    $themeOn = ((0.299 * $r + 0.587 * $g + 0.114 * $b) / 255) > 0.62 ? '#1a1a1a' : '#ffffff';
@endphp

<x-shop-layout :title="$title" :meta-description="$metaDescription">

    {{-- Page theme is derived from the category banner color (set per category by the admin). --}}
    <style>
        .cat-page {
            --cat-accent: color-mix(in srgb, var(--cat-primary) 40%, #ffffff);
            --cat-primary-soft: color-mix(in srgb, var(--cat-primary) 10%, #ffffff);
            --cat-primary-softer: color-mix(in srgb, var(--cat-primary) 5%, #ffffff);
            --cat-primary-line: color-mix(in srgb, var(--cat-primary) 22%, #ffffff);
            --cat-primary-deep: color-mix(in srgb, var(--cat-primary) 70%, #000000);
        }
        .cat-page .cat-chip-scroll { scrollbar-width: none; -webkit-overflow-scrolling: touch; }
        .cat-page .cat-chip-scroll::-webkit-scrollbar { display: none; }
        .cat-page .cat-chip-scroll { scroll-snap-type: x proximity; }
        .cat-page .cat-chip-scroll > * { scroll-snap-align: start; }
        .cat-page :focus-visible { outline: 2px solid var(--cat-accent); outline-offset: 2px; }
        @media (prefers-reduced-motion: reduce) {
            .cat-page * { transition-duration: 0.01ms !important; animation-duration: 0.01ms !important; }
        }
    </style>

    <div class="cat-page"
         style="--cat-primary: {{ $themePrimary }}; --cat-on-primary: {{ $themeOn }};">

        {{-- BREADCRUMB --}}
        <nav aria-label="Breadcrumb"
             class="cat-chip-scroll flex items-center gap-2 text-xs py-3 sm:py-4 overflow-x-auto whitespace-nowrap"
             style="color: color-mix(in srgb, var(--cat-primary) 55%, #6b7280);">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 shrink-0 hover:underline">
                <i class="fas fa-home text-[11px]"></i> Home
            </a>
            @foreach ($breadcrumb as $crumb)
                <i class="fas fa-chevron-right text-[8px] opacity-50 shrink-0"></i>
                @if ($loop->last)
                    <span class="font-semibold text-ink" aria-current="page">{{ $crumb['name'] }}</span>
                @else
                    <a href="{{ route('category.show', $crumb['slug']) }}" class="hover:underline">{{ $crumb['name'] }}</a>
                @endif
            @endforeach
        </nav>

        {{-- CATEGORY BANNER --}}
        <section class="relative overflow-hidden rounded-2xl sm:rounded-3xl mb-5 sm:mb-6 isolate"
                 style="background: {{ $category->heroBackground() }}; color: var(--cat-on-primary);">

            @if ($category->hero_image)
                <img src="{{ asset('storage/' . $category->hero_image) }}"
                     alt=""
                     loading="eager"
                     class="absolute inset-0 -z-10 w-full h-full object-cover opacity-30">
            @endif

            {{-- soft accent glow --}}
            <div aria-hidden="true"
                 class="absolute -right-16 -top-16 w-56 h-56 sm:w-80 sm:h-80 rounded-full blur-3xl opacity-30 -z-10"
                 style="background: var(--cat-accent);"></div>

            <div class="px-5 py-6 sm:px-10 sm:py-12 max-w-3xl">
                <h1 class="font-serif text-[1.75rem] sm:text-5xl font-semibold tracking-tight leading-[1.1]">
                    {{ $category->name }}
                </h1>

                @if ($category->description)
                    <p class="mt-2.5 sm:mt-3 text-sm sm:text-base max-w-xl line-clamp-3 sm:line-clamp-none"
                       style="color: color-mix(in srgb, var(--cat-on-primary) 82%, transparent);">
                        {{ $category->description }}
                    </p>
                @endif

                <div class="mt-4 flex items-center gap-2 flex-wrap">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold backdrop-blur"
                          style="background: color-mix(in srgb, var(--cat-on-primary) 16%, transparent); border: 1px solid color-mix(in srgb, var(--cat-on-primary) 25%, transparent);">
                        <i class="fas fa-box text-[10px]" style="color: var(--cat-accent);"></i>
                        {{ $products->total() }} {{ Str::plural('product', $products->total()) }}
                    </span>
                    @if ($category->isTopLevel() && $category->children->isNotEmpty())
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold backdrop-blur"
                              style="background: color-mix(in srgb, var(--cat-on-primary) 16%, transparent); border: 1px solid color-mix(in srgb, var(--cat-on-primary) 25%, transparent);">
                            <i class="fas fa-sitemap text-[10px]" style="color: var(--cat-accent);"></i>
                            {{ $category->children->count() }} subcategories
                        </span>
                    @endif
                </div>
            </div>
        </section>

        {{-- SUBCATEGORY CHIPS --}}
        @php $activeChildren = $category->children->where('is_active', true); @endphp
        @if ($activeChildren->isNotEmpty())
            <div class="cat-chip-scroll -mx-4 px-4 sm:mx-0 sm:px-0 flex gap-2 overflow-x-auto pb-4 sm:pb-5" role="list" aria-label="Subcategories">
                @foreach ($activeChildren as $child)
                    <a href="{{ route('category.show', $child->slug) }}"
                       role="listitem"
                       class="shrink-0 inline-flex items-center gap-2 whitespace-nowrap rounded-full px-4 py-2 text-sm font-medium bg-white transition-colors"
                       style="border: 1px solid var(--cat-primary-line); color: var(--cat-primary-deep);"
                       onmouseover="this.style.background='var(--cat-primary-soft)'"
                       onmouseout="this.style.background='#fff'">
                        @if ($child->icon)
                            <i class="fas {{ $child->icon }} text-xs" style="color: var(--cat-primary);"></i>
                        @endif
                        {{ $child->name }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- MAIN GRID --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-5 lg:gap-6 pb-12"
             x-data="{ mobileFiltersOpen: false }"
             x-effect="document.body.classList.toggle('overflow-hidden', mobileFiltersOpen)">

            {{-- SIDEBAR (desktop) --}}
            <aside class="hidden lg:block lg:col-span-1" aria-label="Product filters">
                <div class="bg-white rounded-2xl p-5 sticky top-24 shadow-sm"
                     style="border: 1px solid var(--cat-primary-line);">
                    <div class="flex items-center justify-between mb-4 pb-3"
                         style="border-bottom: 1px solid var(--cat-primary-soft);">
                        <h2 class="font-serif text-lg font-semibold flex items-center gap-2">
                            <i class="fas fa-sliders-h text-sm" style="color: var(--cat-primary);"></i>
                            Filters
                            @if ($activeCount)
                                <span class="text-[11px] font-bold px-1.5 py-0.5 rounded-full"
                                      style="background: var(--cat-accent); color: #1a1a1a;">{{ $activeCount }}</span>
                            @endif
                        </h2>
                        @if ($hasFilters)
                            <a href="{{ route('category.show', $category->slug) }}"
                               class="text-xs font-semibold hover:underline"
                               style="color: var(--cat-primary);">Clear all</a>
                        @endif
                    </div>

                    @include('shop.partials.category-filters', [
                        'category' => $category,
                        'availableBrands' => $availableBrands,
                        'priceRange' => $priceRange,
                    ])
                </div>
            </aside>

            {{-- PRODUCTS --}}
            <div class="lg:col-span-3 min-w-0">

                {{-- Toolbar (sticks under the header on phones) --}}
                <div class="sticky z-30 lg:static -mx-4 px-4 py-2 lg:mx-0 lg:px-0 lg:py-0 mb-4 backdrop-blur lg:backdrop-blur-none"
                     style="top: var(--header-height, 0px); background: color-mix(in srgb, var(--cat-primary-softer) 92%, transparent);">
                    <div class="bg-white rounded-xl sm:rounded-2xl px-3 sm:px-4 py-2.5 flex items-center justify-between gap-2 sm:gap-3 shadow-sm"
                         style="border: 1px solid var(--cat-primary-line);">

                        <div class="flex items-center gap-3 min-w-0">
                            <button type="button"
                                    @click="mobileFiltersOpen = true"
                                    aria-haspopup="dialog"
                                    class="lg:hidden relative inline-flex items-center gap-2 text-sm font-semibold px-3.5 py-2 rounded-full transition active:scale-95"
                                    style="background: var(--cat-primary); color: var(--cat-on-primary);">
                                <i class="fas fa-sliders-h text-xs"></i>
                                Filters
                                @if ($activeCount)
                                    <span class="min-w-[18px] h-[18px] px-1 rounded-full text-[10px] font-bold flex items-center justify-center"
                                          style="background: var(--cat-accent); color: #1a1a1a;">{{ $activeCount }}</span>
                                @endif
                            </button>

                            <p class="text-sm text-muted hidden sm:block whitespace-nowrap">
                                <strong class="text-ink">{{ $products->total() }}</strong>
                                {{ Str::plural('result', $products->total()) }}
                            </p>
                        </div>

                        <form method="GET" class="flex items-center gap-2 min-w-0">
                            @foreach (request()->except('sort', 'page') as $k => $v)
                                @if (is_array($v))
                                    @foreach ($v as $vv)
                                        <input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                                @endif
                            @endforeach

                            <label for="sort" class="text-xs text-muted whitespace-nowrap hidden sm:inline">Sort by</label>
                            <select id="sort" name="sort" onchange="this.form.submit()"
                                    class="text-sm rounded-full bg-white py-2 pl-3.5 pr-8 max-w-[10.5rem] sm:max-w-none truncate focus:ring-0"
                                    style="border: 1px solid var(--cat-primary-line); color: var(--cat-primary-deep);">
                                <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Newest</option>
                                <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Price: low to high</option>
                                <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Price: high to low</option>
                                <option value="rating" {{ $sort === 'rating' ? 'selected' : '' }}>Top rated</option>
                                <option value="name" {{ $sort === 'name' ? 'selected' : '' }}>Name A–Z</option>
                            </select>
                        </form>
                    </div>
                </div>

                {{-- Active filter chips --}}
                @if ($activeCount)
                    <div class="cat-chip-scroll flex items-center gap-2 overflow-x-auto pb-3 -mx-4 px-4 sm:mx-0 sm:px-0 sm:flex-wrap" aria-label="Active filters">
                        @foreach ($activeChips as $chip)
                            <a href="{{ $chip['url'] }}"
                               class="shrink-0 inline-flex items-center gap-1.5 pl-3 pr-2.5 py-1.5 rounded-full text-xs font-semibold transition hover:opacity-80"
                               style="background: var(--cat-primary-soft); color: var(--cat-primary-deep); border: 1px solid var(--cat-primary-line);"
                               aria-label="Remove filter {{ $chip['label'] }}">
                                {{ $chip['label'] }}
                                <i class="fas fa-times text-[9px]"></i>
                            </a>
                        @endforeach
                        <a href="{{ route('category.show', $category->slug) }}"
                           class="shrink-0 text-xs font-semibold px-2 hover:underline"
                           style="color: var(--cat-primary);">Clear all</a>
                    </div>
                @endif

                @if ($products->isEmpty())
                    {{-- Empty state --}}
                    <div class="bg-white rounded-2xl p-8 sm:p-12 text-center"
                         style="border: 1px dashed var(--cat-primary-line);">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4"
                             style="background: var(--cat-primary-soft);">
                            <i class="fas fa-box-open text-2xl" style="color: var(--cat-primary);"></i>
                        </div>
                        <h3 class="font-serif text-lg font-semibold text-ink mb-2">No products found</h3>
                        @if ($hasFilters)
                            <p class="text-sm text-muted mb-5">No products match these filters. Remove a filter to see more.</p>
                            <a href="{{ route('category.show', $category->slug) }}"
                               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-semibold transition hover:opacity-90"
                               style="background: var(--cat-primary); color: var(--cat-on-primary);">
                                <i class="fas fa-times text-xs"></i> Clear filters
                            </a>
                        @else
                            <p class="text-sm text-muted">New stock is on the way. Check back soon.</p>
                        @endif
                    </div>
                @else
                    {{-- Product grid: 2 columns on phones, 3 on tablet/desktop --}}
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2.5 sm:gap-4">
                        @foreach ($products as $product)
                            <x-product-card :product="$product" :show-category="false" />
                        @endforeach
                    </div>

                    @if ($products->hasPages())
                        <div class="mt-8 overflow-x-auto">
                            {{ $products->withQueryString()->links() }}
                        </div>
                    @endif
                @endif
            </div>

            {{-- MOBILE FILTER DRAWER --}}
            <div x-show="mobileFiltersOpen" x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @keydown.escape.window="mobileFiltersOpen = false"
                 role="dialog" aria-modal="true" aria-label="Filters"
                 class="lg:hidden fixed inset-0 z-50 flex items-end backdrop-blur-sm"
                 style="background: color-mix(in srgb, var(--cat-primary-deep) 55%, transparent);">

                <div @click="mobileFiltersOpen = false" class="absolute inset-0"></div>

                <div x-show="mobileFiltersOpen"
                     x-transition:enter="transition ease-out duration-250"
                     x-transition:enter-start="translate-y-full"
                     x-transition:enter-end="translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="translate-y-0"
                     x-transition:leave-end="translate-y-full"
                     class="relative bg-white rounded-t-3xl w-full max-h-[88vh] flex flex-col shadow-2xl">

                    {{-- grab handle --}}
                    <div class="pt-2.5 pb-1 flex justify-center" aria-hidden="true">
                        <span class="w-10 h-1 rounded-full" style="background: var(--cat-primary-line);"></span>
                    </div>

                    <div class="px-5 py-3 flex items-center justify-between"
                         style="border-bottom: 1px solid var(--cat-primary-soft);">
                        <h2 class="font-serif text-lg font-semibold">
                            Filters
                            @if ($activeCount)
                                <span class="ml-1 text-xs font-bold px-1.5 py-0.5 rounded-full"
                                      style="background: var(--cat-accent); color: #1a1a1a;">{{ $activeCount }}</span>
                            @endif
                        </h2>
                        <button @click="mobileFiltersOpen = false"
                                aria-label="Close filters"
                                class="w-10 h-10 rounded-full flex items-center justify-center"
                                style="background: var(--cat-primary-soft); color: var(--cat-primary-deep);">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="px-5 py-5 overflow-y-auto flex-1 overscroll-contain">
                        @include('shop.partials.category-filters', [
                            'category' => $category,
                            'availableBrands' => $availableBrands,
                            'priceRange' => $priceRange,
                        ])
                    </div>

                    <div class="px-5 py-3 grid gap-2 sticky bottom-0 bg-white"
                         style="border-top: 1px solid var(--cat-primary-soft); padding-bottom: max(0.75rem, env(safe-area-inset-bottom));
                                grid-template-columns: {{ $hasFilters ? '1fr 2fr' : '1fr' }};">
                        @if ($hasFilters)
                            <a href="{{ route('category.show', $category->slug) }}"
                               class="text-center py-3 rounded-full text-sm font-semibold"
                               style="border: 1px solid var(--cat-primary-line); color: var(--cat-primary-deep);">
                                Clear all
                            </a>
                        @endif
                        <button type="button" @click="mobileFiltersOpen = false"
                                class="py-3 rounded-full text-sm font-semibold transition active:scale-[0.98]"
                                style="background: var(--cat-primary); color: var(--cat-on-primary);">
                            Show {{ $products->total() }} {{ Str::plural('result', $products->total()) }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-shop-layout>