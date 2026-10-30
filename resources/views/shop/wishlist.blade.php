@php
    $isEmpty = $items->isEmpty();
@endphp

<x-shop-layout
    title="Your Wishlist | {{ config('app.name', 'VukaShop') }}"
    meta-description="Products you've saved for later on VukaShop.">

    <nav class="flex items-center gap-2 text-xs text-muted py-4" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-moss transition-colors">
            <i class="fas fa-home"></i> Home
        </a>
        <i class="fas fa-chevron-right text-[8px] text-muted/50"></i>
        <span class="font-medium text-ink">Wishlist</span>
    </nav>

    <div class="flex items-baseline justify-between mb-5 flex-wrap gap-2">
        <h1 class="font-serif text-2xl sm:text-3xl font-semibold text-ink flex items-center gap-2">
            <i class="fas fa-heart text-red-500 text-xl sm:text-2xl"></i>
            Your Wishlist
            @if (!$isEmpty)
                <span class="text-sm font-sans font-medium text-muted">
                    ({{ $itemCount }} {{ Str::plural('item', $itemCount) }})
                </span>
            @endif
        </h1>

        @if (!$isEmpty)
            <a href="{{ route('home') }}"
               class="text-sm font-semibold text-moss hover:text-moss/70 transition">
                <i class="fas fa-arrow-left text-xs"></i> Continue shopping
            </a>
        @endif
    </div>

    @if ($isEmpty)
        <div class="bg-white border border-line rounded-2xl p-8 sm:p-12 text-center max-w-2xl mx-auto mb-12">
            <div class="w-20 h-20 rounded-full bg-paper flex items-center justify-center mx-auto mb-5">
                <i class="far fa-heart text-3xl text-red-500/60"></i>
            </div>
            <h2 class="font-serif text-xl sm:text-2xl font-semibold text-ink mb-2">
                Your wishlist is empty
            </h2>
            <p class="text-sm text-muted mb-6 max-w-md mx-auto">
                Tap the heart on any product to save it here — for later, or until the price drops.
            </p>
            <a href="{{ route('home') }}"
               class="inline-flex items-center gap-2 bg-moss text-white font-semibold px-6 py-3 rounded-full hover:bg-moss/90 transition shadow-[0_10px_24px_-8px_rgba(32,80,60,0.55)]">
                <i class="fas fa-th-large text-xs"></i>
                Browse products
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3.5 sm:gap-4 mb-12">
            @foreach ($items as $item)
                @php
                    /** @var \App\Models\WishlistItem $item */
                    $product = $item->product;
                    $isLowStock = $product->track_inventory
                        && $product->stock_quantity > 0
                        && $product->isLowStock();
                @endphp

                <article class="group relative flex flex-col h-full min-w-0 bg-white rounded-xl sm:rounded-2xl border border-line p-1.5 sm:p-2.5 transition duration-200 hover:border-moss/40 hover:shadow-lg hover:-translate-y-0.5"
                         data-wishlist-row
                         x-data="{
                             removing: false,
                             moving: false,
                             async removeItem() {
                                 if (this.removing) return;
                                 this.removing = true;
                                 try {
                                     const res = await fetch('{{ route('wishlist.destroy', $item) }}', {
                                         method: 'DELETE',
                                         headers: {
                                             'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                             'X-Requested-With': 'XMLHttpRequest',
                                             'Accept': 'application/json',
                                         }
                                     });
                                     const data = await res.json();
                                     if (!res.ok || !data.success) {
                                         if (window.showToast) window.showToast(data.message || 'Could not remove item.', 'error');
                                         this.removing = false;
                                         return;
                                     }
                                     if (window.Alpine) {
                                         if (Alpine.store('wishlist')) Alpine.store('wishlist').count = data.wishlist_count;
                                     }
                                     const rowEl = this.$el.closest('[data-wishlist-row]');
                                     if (rowEl) rowEl.remove();
                                     const remaining = document.querySelectorAll('[data-wishlist-row]').length;
                                     if (remaining === 0) window.location.reload();
                                 } catch (e) {
                                     if (window.showToast) window.showToast('Network error. Please try again.', 'error');
                                     this.removing = false;
                                 }
                             },
                             async moveToCart() {
                                 if (this.moving) return;
                                 this.moving = true;
                                 try {
                                     const res = await fetch('{{ route('wishlist.moveToCart', $item) }}', {
                                         method: 'POST',
                                         headers: {
                                             'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                             'X-Requested-With': 'XMLHttpRequest',
                                             'Accept': 'application/json',
                                         }
                                     });
                                     const data = await res.json();
                                     if (!res.ok || !data.success) {
                                         if (window.showToast) window.showToast(data.message || 'Could not move to cart.', 'error');
                                         this.moving = false;
                                         return;
                                     }
                                     if (window.Alpine) {
                                         if (Alpine.store('cart')) Alpine.store('cart').count = data.cart_count;
                                         if (Alpine.store('wishlist')) Alpine.store('wishlist').count = data.wishlist_count;
                                     }
                                     if (window.showToast) window.showToast(data.message, 'success');
                                     const rowEl = this.$el.closest('[data-wishlist-row]');
                                     if (rowEl) rowEl.remove();
                                     const remaining = document.querySelectorAll('[data-wishlist-row]').length;
                                     if (remaining === 0) window.location.reload();
                                 } catch (e) {
                                     if (window.showToast) window.showToast('Network error. Please try again.', 'error');
                                     this.moving = false;
                                 }
                             }
                         }"
                         x-bind:class="(removing || moving) ? 'opacity-50 pointer-events-none' : ''">

                    <div class="relative rounded-lg sm:rounded-xl overflow-hidden bg-white border border-line/60 aspect-square">
                        <a href="{{ route('product.show', $product->slug) }}"
                           class="block w-full h-full focus:outline-none focus-visible:ring-2 focus-visible:ring-moss"
                           aria-label="{{ $product->name }}">
                            @if ($product->primary_image)
                                <img src="{{ asset('storage/' . $product->primary_image) }}"
                                     alt="{{ $product->name }}"
                                     loading="lazy"
                                     class="absolute inset-0 w-full h-full object-contain p-2 sm:p-3"
                                     onerror="this.onerror=null; this.src='/images/placeholder.png';">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center gap-1 bg-paper text-ink/25">
                                    <i class="fas fa-image text-2xl sm:text-3xl"></i>
                                    <span class="text-[10px]">No image</span>
                                </div>
                            @endif
                        </a>

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
                            @endif
                        </div>

                        <button type="button"
                                @click="removeItem()"
                                :disabled="removing || moving"
                                aria-label="Remove {{ $product->name }} from wishlist"
                                title="Remove from wishlist"
                                class="absolute top-1.5 right-1.5 sm:top-2 sm:right-2 z-10 w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-white/95 backdrop-blur shadow-sm flex items-center justify-center text-red-500 hover:bg-red-50 transition active:scale-90 disabled:opacity-50">
                            <i class="fas fa-heart text-[13px] sm:text-sm"></i>
                        </button>

                        @if (!$product->inStock())
                            <div class="absolute inset-0 bg-white/70 flex items-center justify-center pointer-events-none">
                                <span class="px-2.5 sm:px-3 py-1 rounded-full bg-ink text-white text-[10px] sm:text-xs font-semibold tracking-wide">
                                    Out of stock
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-col flex-1 min-w-0 px-0.5 sm:px-1 pt-2 sm:pt-2.5 pb-0.5">
                        @if ($product->category)
                            <a href="{{ route('category.show', $product->category->slug) }}"
                               class="text-[11px] sm:text-xs font-semibold text-moss hover:text-moss/70 transition truncate">
                                {{ $product->category->name }}
                            </a>
                        @endif

                        <a href="{{ route('product.show', $product->slug) }}"
                           class="block font-semibold text-[13px] sm:text-sm mt-0.5 leading-snug text-ink hover:text-moss transition line-clamp-2 min-h-[2.25rem] sm:min-h-[2.5rem]"
                           title="{{ $product->name }}">
                            {{ $product->name }}
                        </a>

                        <div class="mt-1 flex items-center gap-2 text-[11px] sm:text-xs flex-wrap">
                            @if ($product->rating_count > 0)
                                <span class="flex items-center gap-1 text-muted">
                                    <i class="fas fa-star text-gold text-[10px]"></i>
                                    <span class="font-medium text-ink">{{ number_format((float) $product->rating_avg, 1) }}</span>
                                    <span>({{ $product->rating_count }})</span>
                                </span>
                            @endif
                            @if ($isLowStock)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    Only {{ $product->stock_quantity }} left
                                </span>
                            @endif
                        </div>

                        <div class="mt-auto pt-2 sm:pt-2.5">
                            <div class="flex items-end justify-between gap-2 mb-2">
                                <div class="min-w-0 flex-1">
                                    @if ($product->isOnSale())
                                        <span class="block text-[11px] sm:text-xs text-muted line-through leading-none truncate">
                                            {{ $product->formattedCompareAtPrice() }}
                                        </span>
                                    @endif
                                    <span class="block font-bold text-[15px] sm:text-base leading-tight truncate {{ $product->isOnSale() ? 'text-red-600' : 'text-ink' }}">
                                        {{ $product->formattedPrice() }}
                                    </span>
                                </div>
                            </div>

                            @if ($product->inStock())
                                <button type="button"
                                        @click="moveToCart()"
                                        :disabled="moving || removing"
                                        aria-label="Move {{ $product->name }} to cart"
                                        class="w-full inline-flex items-center justify-center gap-1.5 text-xs font-bold px-3.5 py-2 rounded-full bg-moss text-white hover:bg-moss/90 transition active:scale-95 disabled:opacity-70">
                                    <i class="fas" :class="moving ? 'fa-spinner fa-spin' : 'fa-shopping-cart'"></i>
                                    <span x-text="moving ? 'Moving…' : 'Move to cart'">Move to cart</span>
                                </button>
                            @else
                                <button type="button" disabled
                                        aria-label="{{ $product->name }} is unavailable"
                                        class="w-full inline-flex items-center justify-center gap-1.5 text-xs font-bold px-3.5 py-2 rounded-full border border-gray-200 text-gray-400 cursor-not-allowed">
                                    <i class="fas fa-ban text-[10px]"></i>
                                    Unavailable
                                </button>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    <script>
        document.addEventListener('alpine:init', () => {
            if (! Alpine.store('wishlist')) {
                Alpine.store('wishlist', {
                    count: {{ (int) $itemCount }},
                });
            }
        });
    </script>

</x-shop-layout>