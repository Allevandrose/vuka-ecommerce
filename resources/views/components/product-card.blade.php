{{-- Product Card Component
     Usage: <x-product-card :product="$product" />
     Optional: <x-product-card :product="$product" :show-category="false" />

     Mobile-first: compact padding, smaller type, icon-only add button on phones,
     44px-ish touch targets, hover effects only on devices that can hover.
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

<article class="group relative flex flex-col h-full min-w-0 bg-white rounded-xl sm:rounded-2xl border border-line p-1.5 sm:p-2.5 transition duration-200 focus-within:border-moss/40 md:hover:border-moss/40 md:hover:shadow-lg md:hover:-translate-y-0.5"
         x-data="{ fav: false, added: false }">

    {{-- IMAGE --}}
    <div class="relative rounded-lg sm:rounded-xl overflow-hidden bg-white border border-line/60 aspect-square">
        <a href="{{ route('product.show', $product->slug) }}"
           class="block w-full h-full focus:outline-none focus-visible:ring-2 focus-visible:ring-moss"
           aria-label="{{ $product->name }}">

            @if ($mainUrl)
                {{-- object-contain: the whole product is always visible, never cropped --}}
                <img src="{{ $mainUrl }}"
                     alt="{{ $product->name }}"
                     loading="lazy"
                     decoding="async"
                     class="absolute inset-0 w-full h-full object-contain p-2 sm:p-3 transition duration-300
                            {{ $hoverUrl ? 'md:group-hover:opacity-0' : 'md:group-hover:scale-105' }}"
                     onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect width=%22100%22 height=%22100%22 fill=%22%23f3f4f6%22/><text x=%2250%22 y=%2255%22 text-anchor=%22middle%22 font-size=%2210%22 fill=%22%239ca3af%22>No image</text></svg>';">

                @if ($hoverUrl)
                    <img src="{{ $hoverUrl }}"
                         alt=""
                         loading="lazy"
                         decoding="async"
                         aria-hidden="true"
                         class="hidden md:block absolute inset-0 w-full h-full object-contain p-3 opacity-0 transition duration-300 md:group-hover:opacity-100"
                         onerror="this.remove()">
                @endif
            @else
                <div class="w-full h-full flex flex-col items-center justify-center gap-1 bg-paper text-ink/25">
                    <i class="fas fa-image text-2xl sm:text-3xl"></i>
                    <span class="text-[10px]">No image</span>
                </div>
            @endif
        </a>

        {{-- Badges (left) --}}
        <div class="absolute top-1.5 left-1.5 sm:top-2 sm:left-2 flex flex-col items-start gap-1 pointer-events-none max-w-[65%]">
            @if ($product->isOnSale())
                <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold bg-red-500 text-white shadow-sm">
                    -{{ $product->discountPercent() }}%
                </span>
            @endif
            @if ($product->is_featured)
                <span class="inline-flex items-center gap-1 px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-semibold bg-gold text-ink shadow-sm">
                    <i class="fas fa-star text-[8px] sm:text-[9px]"></i>
                    <span class="truncate">Featured</span>
                </span>
            @elseif ($product->is_verified)
                <span class="inline-flex items-center gap-1 px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-semibold bg-blue-500 text-white shadow-sm">
                    <i class="fas fa-shield-alt text-[8px] sm:text-[9px]"></i>
                    <span class="truncate">Verified</span>
                </span>
            @endif
        </div>

        {{-- Wishlist heart --}}
        <button type="button"
                @click.prevent="fav = !fav"
                :aria-pressed="fav"
                aria-label="Add {{ $product->name }} to wishlist"
                class="absolute top-1.5 right-1.5 sm:top-2 sm:right-2 z-10 w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-white/90 backdrop-blur shadow-sm flex items-center justify-center text-[13px] sm:text-sm transition active:scale-90 md:hover:scale-110 focus:outline-none focus-visible:ring-2 focus-visible:ring-moss"
                :class="fav ? 'text-red-500' : 'text-ink/40 md:hover:text-red-500'">
            <i class="fa-heart" :class="fav ? 'fas' : 'far'"></i>
        </button>

        {{-- Low stock hint --}}
        @if ($lowStock)
            <span class="absolute bottom-1.5 left-1.5 sm:bottom-2 sm:left-2 inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-semibold bg-amber-100 text-amber-700 pointer-events-none">
                Only {{ $product->stock_quantity }} left
            </span>
        @endif

        {{-- Out of stock overlay --}}
        @if (!$product->inStock())
            <div class="absolute inset-0 bg-white/70 flex items-center justify-center pointer-events-none">
                <span class="px-2.5 sm:px-3 py-1 rounded-full bg-ink text-white text-[10px] sm:text-xs font-semibold tracking-wide">
                    Out of stock
                </span>
            </div>
        @endif
    </div>

    {{-- DETAILS --}}
    <div class="flex flex-col flex-1 min-w-0 px-0.5 sm:px-1 pt-2 sm:pt-2.5 pb-0.5">

        {{-- Tag / category --}}
        <a href="{{ $product->category ? route('category.show', $product->category->slug) : '#' }}"
           class="text-[11px] sm:text-xs font-semibold text-moss md:hover:text-moss/70 transition truncate">
            {{ $tag() }}
        </a>

        {{-- Name (fixed two-line height keeps the grid aligned) --}}
        <a href="{{ route('product.show', $product->slug) }}"
           class="block font-semibold text-[13px] sm:text-sm mt-0.5 leading-snug text-ink md:hover:text-moss transition line-clamp-2 min-h-[2.25rem] sm:min-h-[2.5rem]"
           title="{{ $product->name }}">
            {{ $product->name }}
        </a>

        {{-- Rating --}}
        <div class="mt-1 h-4 flex items-center gap-1 text-[11px] sm:text-xs text-muted">
            @if ($product->rating_count > 0)
                <i class="fas fa-star text-gold text-[10px]"></i>
                <span class="font-medium text-ink">{{ number_format((float) $product->rating_avg, 1) }}</span>
                <span>({{ $product->rating_count }})</span>
            @endif
        </div>

        {{-- Price + add button on one row --}}
        <div class="mt-auto pt-2 sm:pt-2.5 flex items-end justify-between gap-2">
            <div class="min-w-0 flex-1">
                @if ($product->isOnSale())
                    <span class="block text-[11px] sm:text-xs text-muted line-through leading-none truncate">{{ $product->formattedCompareAtPrice() }}</span>
                @endif
                <span class="block font-bold text-[15px] sm:text-base text-ink leading-tight truncate {{ $product->isOnSale() ? 'text-red-600' : '' }}">
                    {{ $product->formattedPrice() }}
                </span>
            </div>

            @if ($product->inStock())
                {{-- Phones: round icon button. sm and up: pill with label. --}}
                <button type="button"
                        @click="added = true; setTimeout(() => added = false, 1200)"
                        :class="added ? 'bg-emerald-600 border-emerald-600 text-white' : 'border-moss text-moss md:hover:bg-moss md:hover:text-white active:bg-moss active:text-white'"
                        aria-label="Add {{ $product->name }} to cart"
                        class="shrink-0 inline-flex items-center justify-center gap-1.5 w-10 h-10 sm:w-auto sm:h-auto text-xs font-bold sm:px-3.5 sm:py-2 rounded-full border transition active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-moss focus-visible:ring-offset-1">
                    <i class="fas text-xs sm:text-[10px]" :class="added ? 'fa-check' : 'fa-plus'"></i>
                    <span class="hidden sm:inline" x-text="added ? 'Added' : 'Add'"></span>
                </button>
            @else
                <button type="button" disabled
                        aria-label="{{ $product->name }} is unavailable"
                        class="shrink-0 inline-flex items-center justify-center gap-1.5 w-10 h-10 sm:w-auto sm:h-auto text-xs font-bold sm:px-3.5 sm:py-2 rounded-full border border-gray-200 text-gray-400 cursor-not-allowed">
                    <i class="fas fa-ban text-xs sm:text-[10px]"></i>
                    <span class="hidden sm:inline">Unavailable</span>
                </button>
            @endif
        </div>
    </div>
</article>