{{-- Product Card Component
     Usage: <x-product-card :product="$product" />
     Optional: <x-product-card :product="$product" :show-category="false" />
--}}

@php
    // Optional second image, shown on hover (desktop only). Falls back gracefully if there isn't one.
    $mainUrl = $imageUrl();
    $hoverUrl = null;
    foreach (($product->images ?? []) as $img) {
        $path = is_array($img) ? ($img['path'] ?? null) : $img;
        if ($path && asset('storage/' . $path) !== $mainUrl) {
            $hoverUrl = asset('storage/' . $path);
            break;
        }
    }
    $lowStock = $product->inStock() && $product->track_inventory && $product->isLowStock();
@endphp

<article class="group relative flex flex-col bg-white rounded-2xl border border-line p-2.5 transition-all duration-200 hover:border-moss/40 hover:shadow-lg hover:-translate-y-0.5 focus-within:border-moss/40"
         x-data="{ fav: false, added: false }">

    {{-- IMAGE --}}
    <div class="relative rounded-xl overflow-hidden bg-white border border-line/60 aspect-square">
        <a href="{{ route('product.show', $product->slug) }}"
           class="block w-full h-full focus:outline-none focus-visible:ring-2 focus-visible:ring-moss"
           aria-label="{{ $product->name }}">

            @if ($mainUrl)
                {{-- object-contain: the whole product is always visible, never cropped --}}
                <img src="{{ $mainUrl }}"
                     alt="{{ $product->name }}"
                     loading="lazy"
                     decoding="async"
                     class="absolute inset-0 w-full h-full object-contain p-2.5 transition duration-300
                            {{ $hoverUrl ? 'md:group-hover:opacity-0' : 'group-hover:scale-105' }}"
                     onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect width=%22100%22 height=%22100%22 fill=%22%23f3f4f6%22/><text x=%2250%22 y=%2255%22 text-anchor=%22middle%22 font-size=%2210%22 fill=%22%239ca3af%22>No image</text></svg>';">

                @if ($hoverUrl)
                    <img src="{{ $hoverUrl }}"
                         alt=""
                         loading="lazy"
                         decoding="async"
                         aria-hidden="true"
                         class="hidden md:block absolute inset-0 w-full h-full object-contain p-2.5 opacity-0 transition duration-300 md:group-hover:opacity-100"
                         onerror="this.remove()">
                @endif
            @else
                <div class="w-full h-full flex flex-col items-center justify-center gap-1 bg-paper text-ink/25">
                    <i class="fas fa-image text-3xl"></i>
                    <span class="text-[10px]">No image</span>
                </div>
            @endif
        </a>

        {{-- Badges (left) --}}
        <div class="absolute top-2 left-2 flex flex-col items-start gap-1 pointer-events-none">
            @if ($product->isOnSale())
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-500 text-white shadow-sm">
                    -{{ $product->discountPercent() }}%
                </span>
            @endif
            @if ($product->is_featured)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gold text-ink shadow-sm">
                    <i class="fas fa-star text-[9px]"></i> Featured
                </span>
            @elseif ($product->is_verified)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-500 text-white shadow-sm">
                    <i class="fas fa-shield-alt text-[9px]"></i> Verified
                </span>
            @endif
        </div>

        {{-- Wishlist heart (now inside the image wrapper, so it is positioned correctly) --}}
        <button type="button"
                @click.prevent="fav = !fav"
                :aria-pressed="fav"
                aria-label="Add {{ $product->name }} to wishlist"
                class="absolute top-2 right-2 w-8 h-8 rounded-full bg-white/90 backdrop-blur shadow-sm flex items-center justify-center text-sm transition z-10 hover:scale-110 focus:outline-none focus-visible:ring-2 focus-visible:ring-moss"
                :class="fav ? 'text-red-500' : 'text-ink/40 hover:text-red-500'">
            <i class="fa-heart" :class="fav ? 'fas' : 'far'"></i>
        </button>

        {{-- Low stock hint --}}
        @if ($lowStock)
            <span class="absolute bottom-2 left-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-700 pointer-events-none">
                Only {{ $product->stock_quantity }} left
            </span>
        @endif

        {{-- Out of stock overlay --}}
        @if (!$product->inStock())
            <div class="absolute inset-0 bg-white/70 flex items-center justify-center pointer-events-none">
                <span class="px-3 py-1 rounded-full bg-ink text-white text-xs font-semibold tracking-wide">
                    Out of stock
                </span>
            </div>
        @endif
    </div>

    {{-- DETAILS --}}
    <div class="flex flex-col flex-1 px-1 pt-2.5">

        {{-- Tag / category --}}
        <a href="{{ $product->category ? route('category.show', $product->category->slug) : '#' }}"
           class="text-xs font-semibold text-moss hover:text-moss/70 transition truncate">
            {{ $tag() }}
        </a>

        {{-- Name (fixed two-line height keeps the grid aligned) --}}
        <a href="{{ route('product.show', $product->slug) }}"
           class="block font-semibold text-sm mt-0.5 leading-snug text-ink hover:text-moss transition line-clamp-2 min-h-[2.5rem]"
           title="{{ $product->name }}">
            {{ $product->name }}
        </a>

        {{-- Rating --}}
        <div class="mt-1 h-4 flex items-center gap-1 text-xs text-muted">
            @if ($product->rating_count > 0)
                <i class="fas fa-star text-gold text-[10px]"></i>
                <span class="font-medium text-ink">{{ number_format((float) $product->rating_avg, 1) }}</span>
                <span>({{ $product->rating_count }})</span>
            @endif
        </div>

        {{-- Price + add button on one row --}}
        <div class="mt-auto pt-2.5 flex items-end justify-between gap-2">
            <div class="min-w-0">
                @if ($product->isOnSale())
                    <span class="block text-xs text-muted line-through leading-none">{{ $product->formattedCompareAtPrice() }}</span>
                @endif
                <span class="block font-bold text-ink leading-tight {{ $product->isOnSale() ? 'text-red-600' : '' }}">
                    {{ $product->formattedPrice() }}
                </span>
            </div>

            @if ($product->inStock())
                <button type="button"
                        @click="added = true; setTimeout(() => added = false, 1200)"
                        :class="added ? 'bg-emerald-600 border-emerald-600 text-white' : 'border-moss text-moss hover:bg-moss hover:text-white'"
                        aria-label="Add {{ $product->name }} to cart"
                        class="shrink-0 inline-flex items-center gap-1.5 text-xs font-bold px-3.5 py-2 rounded-full border transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-moss focus-visible:ring-offset-1">
                    <i class="fas text-[10px]" :class="added ? 'fa-check' : 'fa-plus'"></i>
                    <span x-text="added ? 'Added' : 'Add'"></span>
                </button>
            @else
                <button type="button" disabled
                        class="shrink-0 inline-flex items-center gap-1.5 text-xs font-bold px-3.5 py-2 rounded-full border border-gray-200 text-gray-400 cursor-not-allowed">
                    <i class="fas fa-ban text-[10px]"></i>
                    <span>Unavailable</span>
                </button>
            @endif
        </div>
    </div>
</article>