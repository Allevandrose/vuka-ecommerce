{{-- Product Card Component
     Usage: <x-product-card :product="$product" />
     Optional: <x-product-card :product="$product" :show-category="false" />
--}}

<div class="group bg-white rounded-2xl border border-line p-2.5 pb-4 transition-all hover:border-moss/40 hover:shadow-md"
     x-data="{ fav: false, added: false }">

    {{-- Image --}}
    <a href="{{ route('product.show', $product->slug) }}"
       class="block relative rounded-xl overflow-hidden bg-paper aspect-square">

        @if ($imageUrl())
            <img src="{{ $imageUrl() }}"
                 alt="{{ $product->name }}"
                 loading="lazy"
                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                 onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22 fill=%22%23e5e7eb%22><rect width=%22100%22 height=%22100%22/><text x=%2250%22 y=%2255%22 text-anchor=%22middle%22 font-size=%2212%22 fill=%22%239ca3af%22>No image</text></svg>';">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <i class="fas fa-image text-4xl text-ink/20"></i>
            </div>
        @endif

        {{-- Badge: Featured / Verified / Certified --}}
        @if ($product->is_featured)
            <span class="absolute top-2 left-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gold text-ink shadow-sm">
                <i class="fas fa-star text-[9px]"></i> Featured
            </span>
        @elseif ($product->is_verified)
            <span class="absolute top-2 left-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-500 text-white shadow-sm">
                <i class="fas fa-shield-alt text-[9px]"></i> Verified
            </span>
        @endif

        {{-- Sale badge --}}
        @if ($product->isOnSale())
            <span class="absolute top-2 right-12 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-500 text-white shadow-sm">
                -{{ $product->discountPercent() }}%
            </span>
        @endif

        {{-- Out of stock overlay --}}
        @if (!$product->inStock())
            <div class="absolute inset-0 bg-ink/60 flex items-center justify-center">
                <span class="px-3 py-1 rounded-full bg-white text-ink text-xs font-semibold uppercase tracking-wide">
                    Out of stock
                </span>
            </div>
        @endif
    </a>

    {{-- Wishlist heart --}}
    <button type="button"
            @click.prevent="fav = !fav"
            class="absolute top-4 right-4 w-8 h-8 rounded-full bg-white/90 backdrop-blur flex items-center justify-center text-sm transition-colors z-10"
            :class="fav ? 'text-gold' : 'text-ink/30 hover:text-gold'"
            aria-label="Add to wishlist">
        <i class="fa-heart" :class="fav ? 'fas' : 'far'"></i>
    </button>

    {{-- Tag pill --}}
    <a href="{{ $product->category ? route('category.show', $product->category->slug) : '#' }}"
       class="inline-block text-xs font-semibold text-moss mt-2.5 hover:text-moss/70 transition">
        {{ $tag() }}
    </a>

    {{-- Name --}}
    <a href="{{ route('product.show', $product->slug) }}"
       class="block font-semibold text-sm mt-0.5 mb-1 leading-snug text-ink hover:text-moss transition line-clamp-2">
        {{ $product->name }}
    </a>

    {{-- Price --}}
    <div class="flex items-baseline gap-1.5">
        <span class="font-bold text-ink">{{ $product->formattedPrice() }}</span>
        @if ($product->isOnSale())
            <span class="text-xs text-muted line-through">{{ $product->formattedCompareAtPrice() }}</span>
        @endif
    </div>

    {{-- Rating (only if there are reviews) --}}
    @if ($product->rating_count > 0)
        <div class="flex items-center gap-1 mt-1 text-xs text-muted">
            <i class="fas fa-star text-gold text-[10px]"></i>
            <span class="font-medium text-ink">{{ number_format((float) $product->rating_avg, 1) }}</span>
            <span>({{ $product->rating_count }})</span>
        </div>
    @endif

    {{-- Add to cart button --}}
    <div class="flex justify-end mt-2.5">
        @if ($product->inStock())
            <button type="button"
                    @click="added = true; setTimeout(() => added = false, 1200)"
                    :class="added ? 'bg-emerald-600 border-emerald-600 text-white' : 'border-moss text-moss hover:bg-moss hover:text-white'"
                    class="inline-flex items-center gap-1.5 text-xs font-bold px-3.5 py-1.5 rounded-full border transition-colors">
                <i class="fas text-[10px]" :class="added ? 'fa-check' : 'fa-plus'"></i>
                <span x-text="added ? 'Added' : 'Add'"></span>
            </button>
        @else
            <button type="button" disabled
                    class="inline-flex items-center gap-1.5 text-xs font-bold px-3.5 py-1.5 rounded-full border border-gray-200 text-gray-400 cursor-not-allowed">
                <i class="fas fa-ban text-[10px]"></i>
                <span>Unavailable</span>
            </button>
        @endif
    </div>
</div>