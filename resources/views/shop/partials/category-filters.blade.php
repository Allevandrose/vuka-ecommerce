<form method="GET" action="{{ route('category.show', $category->slug) }}" class="space-y-5">

    {{-- Preserve sort --}}
    @if (request('sort'))
        <input type="hidden" name="sort" value="{{ request('sort') }}">
    @endif

    {{-- SEARCH WITHIN CATEGORY --}}
    <div>
        <label for="filter-q" class="block text-xs font-semibold text-ink uppercase tracking-wide mb-2">
            Search
        </label>
        <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-muted text-sm"></i>
            <input id="filter-q" name="q" type="text"
                   value="{{ request('q') }}"
                   placeholder="Search in {{ $category->name }}"
                   class="w-full pl-9 pr-3 py-2 rounded-lg border border-line bg-paper text-sm focus:border-moss focus:ring-moss">
        </div>
    </div>

    {{-- BRANDS --}}
    @if ($availableBrands->isNotEmpty())
        <div>
            <h3 class="text-xs font-semibold text-ink uppercase tracking-wide mb-3">
                Brand
            </h3>
            <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                @php
                    $selectedBrands = (array) request('brand', []);
                    $selectedBrands = array_map('strval', $selectedBrands);
                @endphp
                @foreach ($availableBrands as $brand)
                    <label class="flex items-center gap-2.5 cursor-pointer group">
                        <input type="checkbox"
                               name="brand[]"
                               value="{{ $brand->id }}"
                               {{ in_array((string) $brand->id, $selectedBrands, true) ? 'checked' : '' }}
                               class="rounded border-line text-moss shadow-sm focus:ring-moss">
                        <span class="text-sm text-ink/80 group-hover:text-ink transition-colors flex-1 flex items-center gap-1.5">
                            {{ $brand->name }}
                            @if ($brand->is_verified)
                                <i class="fas fa-circle-check text-blue-500 text-[10px]" title="Verified brand"></i>
                            @endif
                        </span>
                    </label>
                @endforeach
            </div>
        </div>
    @endif

    {{-- PRICE RANGE --}}
    @if ($priceRange && $priceRange->min_price !== null)
        <div>
            <h3 class="text-xs font-semibold text-ink uppercase tracking-wide mb-3">
                Price ({{ config('app.currency', 'KES') }})
            </h3>
            <div class="flex items-center gap-2">
                <input type="number" name="min_price"
                       value="{{ request('min_price') }}"
                       placeholder="{{ (int) floor($priceRange->min_price) }}"
                       min="0"
                       class="w-full px-2.5 py-1.5 rounded-lg border border-line bg-paper text-xs focus:border-moss focus:ring-moss">
                <span class="text-muted text-xs">–</span>
                <input type="number" name="max_price"
                       value="{{ request('max_price') }}"
                       placeholder="{{ (int) ceil($priceRange->max_price) }}"
                       min="0"
                       class="w-full px-2.5 py-1.5 rounded-lg border border-line bg-paper text-xs focus:border-moss focus:ring-moss">
            </div>
        </div>
    @endif

    {{-- QUICK FILTERS --}}
    <div>
        <h3 class="text-xs font-semibold text-ink uppercase tracking-wide mb-3">
            Quick filters
        </h3>
        <div class="space-y-2">
            <label class="flex items-center gap-2.5 cursor-pointer group">
                <input type="checkbox" name="in_stock" value="1"
                       {{ request()->boolean('in_stock') ? 'checked' : '' }}
                       class="rounded border-line text-moss shadow-sm focus:ring-moss">
                <span class="text-sm text-ink/80 group-hover:text-ink transition-colors">In stock only</span>
            </label>

            <label class="flex items-center gap-2.5 cursor-pointer group">
                <input type="checkbox" name="on_sale" value="1"
                       {{ request()->boolean('on_sale') ? 'checked' : '' }}
                       class="rounded border-line text-moss shadow-sm focus:ring-moss">
                <span class="text-sm text-ink/80 group-hover:text-ink transition-colors">On sale only</span>
            </label>

            <label class="flex items-center gap-2.5 cursor-pointer group">
                <input type="checkbox" name="verified" value="1"
                       {{ request()->boolean('verified') ? 'checked' : '' }}
                       class="rounded border-line text-moss shadow-sm focus:ring-moss">
                <span class="text-sm text-ink/80 group-hover:text-ink transition-colors">Verified products</span>
            </label>
        </div>
    </div>

    {{-- ACTIONS --}}
    <div class="pt-4 border-t border-line flex items-center gap-2">
        <button type="submit"
                class="flex-1 bg-moss text-white text-sm font-semibold py-2.5 rounded-full hover:bg-moss/90 transition">
            Apply filters
        </button>
        @if (request()->hasAny(['brand', 'min_price', 'max_price', 'verified', 'in_stock', 'on_sale', 'q']))
            <a href="{{ route('category.show', $category->slug) }}"
               class="px-4 py-2.5 text-sm font-semibold text-muted hover:text-ink transition">
                Clear
            </a>
        @endif
    </div>
</form>