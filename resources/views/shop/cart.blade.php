@php
    $isEmpty = $items->isEmpty();
@endphp

<x-shop-layout title="Your Cart | {{ config('app.name', 'VukaShop') }}"
    meta-description="Review the items in your VukaShop cart before checkout.">

    <script>
        document.addEventListener('alpine:init', () => {
            if (!Alpine.store('cartSummary')) {
                Alpine.store('cartSummary', {
                    count: {{ (int) $itemCount }},
                    subtotalText: @js($formattedSubtotal),
                });
            }
        });
    </script>

    <nav class="flex items-center gap-2 text-xs text-muted py-4" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-moss transition-colors">
            <i class="fas fa-home"></i> Home
        </a>
        <i class="fas fa-chevron-right text-[8px] text-muted/50"></i>
        <span class="font-medium text-ink">Cart</span>
    </nav>

    <div class="flex items-baseline justify-between mb-5 flex-wrap gap-2">
        <h1 class="font-serif text-2xl sm:text-3xl font-semibold text-ink flex items-center gap-2">
            <i class="fas fa-shopping-bag text-moss text-xl sm:text-2xl"></i>
            Your Cart
            @if (!$isEmpty)
                <span class="text-sm font-sans font-medium text-muted" id="cart-header-count">
                    (<span x-text="$store.cartSummary.count">{{ $itemCount }}</span>
                    <span x-text="$store.cartSummary.count === 1 ? 'item' : 'items'">items</span>)
                </span>
            @endif
        </h1>

        @if (!$isEmpty)
            <a href="{{ route('home') }}" class="text-sm font-semibold text-moss hover:text-moss/70 transition">
                <i class="fas fa-arrow-left text-xs"></i> Continue shopping
            </a>
        @endif
    </div>

    @if ($isEmpty)
        <div class="bg-white border border-line rounded-2xl p-8 sm:p-12 text-center max-w-2xl mx-auto mb-12">
            <div class="w-20 h-20 rounded-full bg-paper flex items-center justify-center mx-auto mb-5">
                <i class="fas fa-shopping-bag text-3xl text-moss/60"></i>
            </div>
            <h2 class="font-serif text-xl sm:text-2xl font-semibold text-ink mb-2">
                Your cart is empty
            </h2>
            <p class="text-sm text-muted mb-6 max-w-md mx-auto">
                Browse our catalogue and add products you love — they'll show up here.
            </p>
            <a href="{{ route('home') }}"
                class="inline-flex items-center gap-2 bg-moss text-white font-semibold px-6 py-3 rounded-full hover:bg-moss/90 transition shadow-[0_10px_24px_-8px_rgba(32,80,60,0.55)]">
                <i class="fas fa-th-large text-xs"></i>
                Start shopping
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 lg:gap-6 mb-12">

            <div class="lg:col-span-2 space-y-3" id="cart-rows">

                @foreach ($items as $item)
                    @php
                        /** @var \App\Models\CartItem $item */
                        $product = $item->product;
                        $maxQty = $product->track_inventory ? max(1, (int) $product->stock_quantity) : 999;
                        $isLow = $product->track_inventory && $product->stock_quantity > 0 && $product->isLowStock();
                        $initialQty = (int) $item->quantity;
                        $initialLineSubtotal = $item->formattedSubtotal();
                        $initialWishlisted = auth()->check() && auth()->user()->isWishlisted($product->id);
                        $isGuest = ! auth()->check();
                    @endphp

                    <div class="bg-white border border-line rounded-2xl p-3 sm:p-4 flex gap-3 sm:gap-4 relative" data-cart-row
                        data-product-name="{{ $product->name }}" data-product-id="{{ $product->id }}"
                        x-data="{
                            qty: {{ $initialQty }},
                            maxQty: {{ $maxQty }},
                            lineSubtotal: @js($initialLineSubtotal),
                            updating: false,
                            removing: false,
                            wishlisted: {{ $initialWishlisted ? 'true' : 'false' }},
                            wishlistBusy: false,
                            async changeQty(newQty) {
                                const target = Math.min(this.maxQty, Math.max(1, parseInt(newQty) || 1));
                                if (target === this.qty) { this.qty = target; return; }
                                this.updating = true;
                                try {
                                    const res = await fetch('{{ route('cart.update', $item) }}', {
                                        method: 'PATCH',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'Accept': 'application/json',
                                        },
                                        body: JSON.stringify({ quantity: target })
                                    });
                                    const data = await res.json();
                                    if (!res.ok || !data.success) {
                                        if (window.showToast) window.showToast(data.message || 'Could not update cart.', 'error');
                                    } else {
                                        this.qty = target;
                                        this.lineSubtotal = data.line_subtotal;
                                        if (window.Alpine) {
                                            if (Alpine.store('cart')) Alpine.store('cart').count = data.cart_count;
                                            if (Alpine.store('cartSummary')) {
                                                Alpine.store('cartSummary').count = data.cart_count;
                                                Alpine.store('cartSummary').subtotalText = data.cart_currency + ' ' + Number(data.cart_subtotal).toFixed(2);
                                            }
                                        }
                                    }
                                } catch (e) {
                                    if (window.showToast) window.showToast('Network error. Please try again.', 'error');
                                } finally {
                                    this.updating = false;
                                }
                            },
                            async toggleWishlist() {
                                if (this.wishlistBusy) return;
                                @if ($isGuest)
                                    window.location.href = '{{ route('login') }}';
                                    return;
                                @endif
                                this.wishlistBusy = true;
                                try {
                                    const res = await fetch('{{ route('wishlist.toggle') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'Accept': 'application/json',
                                        },
                                        body: JSON.stringify({ product_id: {{ $product->id }} })
                                    });

                                    if (res.status === 401) {
                                        window.location.href = '{{ route('login') }}';
                                        return;
                                    }

                                    const data = await res.json();

                                    if (!res.ok || !data.success) {
                                        if (window.showToast) window.showToast(data.message || 'Could not update wishlist.', 'error');
                                        return;
                                    }

                                    this.wishlisted = !!data.wishlisted;
                                    if (window.Alpine && Alpine.store('wishlist')) {
                                        Alpine.store('wishlist').count = data.wishlist_count;
                                    }
                                    if (window.showToast) window.showToast(data.message, 'success');
                                } catch (e) {
                                    if (window.showToast) window.showToast('Network error. Please try again.', 'error');
                                } finally {
                                    this.wishlistBusy = false;
                                }
                            },
                            async removeItem(rowEl) {
                                if (this.removing) return;

                                const productName = rowEl.dataset.productName || 'this item';
                                let wantsWishlist = false;

                                if (window.Swal) {
                                    const result = await window.Swal.fire({
                                        icon: 'question',
                                        title: 'Remove from cart?',
                                        html: 'Do you want to add <strong>' + productName + '</strong> to your wishlist before removing it from the cart?',
                                        showCancelButton: true,
                                        confirmButtonText: 'Yes, add to wishlist',
                                        cancelButtonText: 'No, remove only',
                                        confirmButtonColor: '#20503C',
                                        cancelButtonColor: '#9CA3AF',
                                        reverseButtons: true,
                                    });
                                    wantsWishlist = !!result.isConfirmed;
                                } else {
                                    wantsWishlist = window.confirm('Add ' + productName + ' to your wishlist before removing from the cart?');
                                }

                                this.removing = true;
                                try {
                                    if (wantsWishlist) {
                                        @if ($isGuest)
                                            window.location.href = '{{ route('login') }}';
                                            return;
                                        @endif

                                        const wishRes = await fetch('{{ route('wishlist.toggle') }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                                'X-Requested-With': 'XMLHttpRequest',
                                                'Accept': 'application/json',
                                            },
                                            body: JSON.stringify({ product_id: {{ $product->id }} })
                                        });

                                        const wishData = await wishRes.json();
                                        if (!wishRes.ok || !wishData.success) {
                                            if (window.showToast) window.showToast(wishData.message || 'Could not update wishlist — item left in cart.', 'error');
                                            this.removing = false;
                                            return;
                                        }

                                        this.wishlisted = !!wishData.wishlisted;
                                        if (window.Alpine && Alpine.store('wishlist')) {
                                            Alpine.store('wishlist').count = wishData.wishlist_count;
                                        }
                                    }

                                    const res = await fetch('{{ route('cart.destroy', $item) }}', {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'Accept': 'application/json',
                                        }
                                    });
                                    const data = await res.json();
                                    if (!res.ok || !data.success) {
                                        const msg = wantsWishlist ?
                                            'Wishlist updated, but could not remove from cart.' :
                                            (data.message || 'Could not remove item.');
                                        if (window.showToast) window.showToast(msg, 'error');
                                        this.removing = false;
                                        return;
                                    }
                                    if (window.Alpine) {
                                        if (Alpine.store('cart')) Alpine.store('cart').count = data.cart_count;
                                        if (Alpine.store('cartSummary')) {
                                            Alpine.store('cartSummary').count = data.cart_count;
                                            Alpine.store('cartSummary').subtotalText = data.cart_currency + ' ' + Number(data.cart_subtotal).toFixed(2);
                                        }
                                    }
                                    if (wantsWishlist && window.showToast) {
                                        window.showToast('Updated wishlist and removed from cart.', 'success');
                                    }
                                    const targetRow = rowEl.closest('[data-cart-row]');
                                    if (targetRow) targetRow.remove();
                                    const remaining = document.querySelectorAll('[data-cart-row]').length;
                                    if (remaining === 0) {
                                        window.location.reload();
                                    }
                                } catch (e) {
                                    if (window.showToast) window.showToast('Network error. Please try again.', 'error');
                                    this.removing = false;
                                }
                            }
                        }" x-bind:class="removing ? 'opacity-50 pointer-events-none' : ''">

                        <a href="{{ route('product.show', $product->slug) }}"
                            class="shrink-0 w-20 h-20 sm:w-24 sm:h-24 rounded-xl bg-white border border-line/60 overflow-hidden flex items-center justify-center relative">
                            @if ($product->imageUrl())
                                <img src="{{ asset('storage/' . $product->primary_image) }}"
                                    alt="{{ $product->name }}" class="w-full h-full object-contain p-1.5"
                                    onerror="this.onerror=null; this.src='/images/placeholder.png';">
                            @else
                                <i class="fas fa-image text-2xl text-ink/20"></i>
                            @endif
                        </a>

                        <div class="flex-1 min-w-0 flex flex-col">
                            <div class="flex items-start justify-between gap-2">
                                <a href="{{ route('product.show', $product->slug) }}"
                                    class="font-semibold text-sm sm:text-base text-ink hover:text-moss transition line-clamp-2 leading-snug">
                                    {{ $product->name }}
                                </a>

                                <button type="button"
                                    @click.prevent="toggleWishlist()"
                                    :disabled="wishlistBusy"
                                    :aria-pressed="wishlisted"
                                    aria-label="Toggle wishlist for {{ $product->name }}"
                                    class="shrink-0 w-8 h-8 rounded-full bg-white/90 backdrop-blur shadow-sm border border-line/60 flex items-center justify-center text-xs transition active:scale-90 md:hover:scale-110 focus:outline-none focus-visible:ring-2 focus-visible:ring-moss disabled:opacity-60"
                                    :class="wishlisted ? 'text-red-500' : 'text-ink/40 md:hover:text-red-500'">
                                    <i class="fa-heart" :class="wishlisted ? 'fas' : 'far'"></i>
                                </button>
                            </div>

                            @if ($product->category)
                                <span class="text-[11px] sm:text-xs text-muted mt-0.5 truncate">
                                    {{ $product->category->name }}
                                </span>
                            @endif

                            <div class="mt-1 flex items-center gap-2 flex-wrap text-xs">
                                <span class="font-mono text-muted">{{ $product->sku }}</span>
                                @if ($isLow)
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Only {{ $product->stock_quantity }} left
                                    </span>
                                @endif
                            </div>

                            <div class="mt-auto pt-2 flex items-end justify-between gap-3 flex-wrap">
                                <div class="flex items-center border border-line rounded-full overflow-hidden bg-white"
                                    x-bind:class="updating ? 'opacity-60' : ''">
                                    <button type="button" @click="changeQty(qty - 1)" :disabled="qty <= 1 || updating"
                                        aria-label="Decrease quantity"
                                        class="w-9 h-9 flex items-center justify-center text-ink hover:bg-paper transition disabled:opacity-40 disabled:cursor-not-allowed">
                                        <i class="fas fa-minus text-[10px]"></i>
                                    </button>
                                    <input type="number" x-model.number="qty" @change="changeQty(qty)"
                                        :disabled="updating" min="1" max="{{ $maxQty }}"
                                        aria-label="Quantity"
                                        class="w-10 text-center border-0 focus:ring-0 text-sm font-semibold bg-white p-0 [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">
                                    <button type="button" @click="changeQty(qty + 1)"
                                        :disabled="qty >= maxQty || updating" aria-label="Increase quantity"
                                        class="w-9 h-9 flex items-center justify-center text-ink hover:bg-paper transition disabled:opacity-40 disabled:cursor-not-allowed">
                                        <i class="fas fa-plus text-[10px]"></i>
                                    </button>
                                </div>

                                <div class="flex items-center gap-3">
                                    <div class="text-right">
                                        <p class="text-[10px] text-muted uppercase tracking-wide leading-tight">
                                            {{ $item->formattedUnitPrice() }} each
                                        </p>
                                        <p class="font-serif font-bold text-base sm:text-lg text-ink leading-tight"
                                            x-text="lineSubtotal">
                                            {{ $initialLineSubtotal }}
                                        </p>
                                    </div>

                                    <button type="button" @click="removeItem($el)" :disabled="removing"
                                        aria-label="Remove {{ $product->name }} from cart"
                                        class="w-9 h-9 rounded-full flex items-center justify-center text-muted hover:text-red-600 hover:bg-red-50 transition disabled:opacity-40">
                                        <i class="fas fa-trash-alt text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <aside class="lg:col-span-1 lg:sticky lg:top-24 lg:self-start">
                <div class="bg-white border border-line rounded-2xl p-5 sm:p-6">
                    <h2 class="font-serif text-lg font-semibold text-ink mb-4 flex items-center gap-2">
                        <i class="fas fa-receipt text-moss text-sm"></i> Summary
                    </h2>

                    <dl class="space-y-2.5 text-sm">
                        <div class="flex items-center justify-between">
                            <dt class="text-muted">Items</dt>
                            <dd class="font-medium text-ink" x-text="$store.cartSummary.count">{{ $itemCount }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-muted">Subtotal</dt>
                            <dd class="font-medium text-ink" x-text="$store.cartSummary.subtotalText">
                                {{ $formattedSubtotal }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-muted">Delivery</dt>
                            <dd class="text-muted text-xs">Calculated at checkout</dd>
                        </div>
                    </dl>

                    <div class="border-t border-line mt-4 pt-4 flex items-baseline justify-between">
                        <span class="text-sm font-semibold text-ink">Total</span>
                        <span class="font-serif font-bold text-2xl text-ink"
                            x-text="$store.cartSummary.subtotalText">{{ $formattedSubtotal }}</span>
                    </div>

                    <button type="button" disabled
                        class="w-full mt-5 bg-gray-200 text-gray-500 font-semibold py-3 rounded-full cursor-not-allowed flex items-center justify-center gap-2">
                        <i class="fas fa-lock text-xs"></i>
                        Checkout (coming soon)
                    </button>

                    <a href="{{ route('home') }}"
                        class="block text-center mt-3 text-sm font-semibold text-moss hover:text-moss/70 transition">
                        Continue shopping
                    </a>
                </div>

                <div class="mt-4 bg-paper rounded-2xl p-4 flex items-start gap-3">
                    <div class="w-9 h-9 rounded-full bg-white flex items-center justify-center text-moss shrink-0">
                        <i class="fas fa-truck text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-ink">Fast nationwide delivery</p>
                        <p class="text-xs text-muted mt-0.5">
                            1–3 days across Kenya</p>
                    </div>
                </div>
            </aside>
        </div>
    @endif

</x-shop-layout>