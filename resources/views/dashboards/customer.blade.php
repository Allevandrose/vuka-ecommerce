@php
    $cartItems = auth()->user()->cartItems()
        ->with('product')
        ->latest()
        ->take(3)
        ->get()
        ->filter(fn ($item) => $item->product !== null);

    $cartItemCount = auth()->user()->cartCount();
    $cartSubtotal = auth()->user()->cartItems()
        ->with('product')
        ->get()
        ->filter(fn ($item) => $item->product !== null)
        ->sum(fn ($item) => $item->subtotal());
    $cartCurrency = auth()->user()->cartItems()->with('product')->first()?->currency() ?? 'KES';

    $wishlistItems = auth()->user()->wishlistItems()
        ->with('product')
        ->latest()
        ->take(3)
        ->get()
        ->filter(fn ($item) => $item->product !== null);

    $wishlistItemCount = auth()->user()->wishlistCount();
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold">Welcome back, {{ Auth::user()->name }}! 🛍️</h3>
                            <p class="mt-1 text-sm text-gray-500">Track your orders and manage your account.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                                Customer
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-indigo-500 rounded-lg p-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Orders</p>
                                <p class="text-2xl font-semibold text-gray-900">0</p>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('wishlist.index') }}"
                   data-wishlist-link
                   class="group block bg-white overflow-hidden shadow-sm sm:rounded-lg ring-1 transition
                          {{ $wishlistItemCount > 0 ? 'ring-rose-200 hover:ring-rose-400' : 'ring-transparent hover:ring-rose-200' }}">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-rose-500 rounded-lg p-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4 flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-500">Wishlist</p>
                                <p id="wishlist-stat-count" class="text-2xl font-semibold text-gray-900">{{ $wishlistItemCount }}</p>
                            </div>
                            <span class="text-xs font-semibold text-rose-600 opacity-0 group-hover:opacity-100 transition">
                                View <i class="fas fa-arrow-right text-[10px]"></i>
                            </span>
                        </div>
                    </div>
                </a>

                <a href="{{ route('cart.index') }}"
                   data-cart-link
                   class="group block bg-white overflow-hidden shadow-sm sm:rounded-lg ring-1 transition
                          {{ $cartItemCount > 0 ? 'ring-amber-200 hover:ring-amber-400' : 'ring-transparent hover:ring-amber-200' }}">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-amber-500 rounded-lg p-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4 flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-500">Cart Items</p>
                                <p id="cart-stat-count" class="text-2xl font-semibold text-gray-900">{{ $cartItemCount }}</p>
                            </div>
                            <span class="text-xs font-semibold text-amber-600 opacity-0 group-hover:opacity-100 transition">
                                View <i class="fas fa-arrow-right text-[10px]"></i>
                            </span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-shopping-bag text-amber-500"></i> Your Cart
                        </h4>
                        @if ($cartItemCount > 0)
                            <a href="{{ route('cart.index') }}"
                               class="text-sm font-semibold text-amber-600 hover:text-amber-700">
                                View cart <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        @endif
                    </div>

                    @if ($cartItems->isEmpty())
                        <p class="text-sm text-gray-500">
                            Your cart is empty.
                            <a href="{{ route('home') }}" class="font-semibold text-moss hover:text-moss/70">
                                Browse products <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        </p>
                    @else
                        <ul class="divide-y divide-gray-100">
                            @foreach ($cartItems as $item)
                                @php $p = $item->product; @endphp
                                <li class="py-3 first:pt-0 last:pb-0 flex items-center gap-3">
                                    <a href="{{ route('product.show', $p->slug) }}"
                                       class="shrink-0 w-14 h-14 rounded-lg bg-gray-50 border border-gray-100 flex items-center justify-center overflow-hidden">
                                        @if ($p->primary_image)
                                            <img src="{{ asset('storage/' . $p->primary_image) }}"
                                                 alt="{{ $p->name }}"
                                                 class="w-full h-full object-contain p-1">
                                        @else
                                            <i class="fas fa-image text-gray-300"></i>
                                        @endif
                                    </a>
                                    <div class="flex-1 min-w-0">
                                        <a href="{{ route('product.show', $p->slug) }}"
                                           class="block text-sm font-semibold text-gray-900 truncate hover:text-moss">
                                            {{ $p->name }}
                                        </a>
                                        <p class="text-xs text-gray-500">
                                            {{ $item->quantity }} × {{ $item->formattedUnitPrice() }}
                                        </p>
                                    </div>
                                    <div class="text-sm font-semibold text-gray-900 whitespace-nowrap">
                                        {{ $item->formattedSubtotal() }}
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        @if ($cartItemCount > $cartItems->sum('quantity'))
                            <p class="mt-3 text-xs text-gray-500">
                                + {{ $cartItemCount - $cartItems->sum('quantity') }} more item(s) in your cart.
                            </p>
                        @endif

                        <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                            <span class="text-sm text-gray-500">Subtotal</span>
                            <span id="cart-strip-subtotal" class="text-base font-semibold text-gray-900">
                                {{ $cartCurrency }} {{ number_format((float) $cartSubtotal, 2) }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="far fa-heart text-rose-500"></i> Your Wishlist
                        </h4>
                        @if ($wishlistItemCount > 0)
                            <a href="{{ route('wishlist.index') }}"
                               class="text-sm font-semibold text-rose-600 hover:text-rose-700">
                                View wishlist <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        @endif
                    </div>

                    @if ($wishlistItems->isEmpty())
                        <p class="text-sm text-gray-500">
                            Your wishlist is empty.
                            <a href="{{ route('home') }}" class="font-semibold text-moss hover:text-moss/70">
                                Browse products <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        </p>
                    @else
                        <ul class="divide-y divide-gray-100">
                            @foreach ($wishlistItems as $item)
                                @php $p = $item->product; @endphp
                                <li class="py-3 first:pt-0 last:pb-0 flex items-center gap-3">
                                    <a href="{{ route('product.show', $p->slug) }}"
                                       class="shrink-0 w-14 h-14 rounded-lg bg-gray-50 border border-gray-100 flex items-center justify-center overflow-hidden">
                                        @if ($p->primary_image)
                                            <img src="{{ asset('storage/' . $p->primary_image) }}"
                                                 alt="{{ $p->name }}"
                                                 class="w-full h-full object-contain p-1">
                                        @else
                                            <i class="fas fa-image text-gray-300"></i>
                                        @endif
                                    </a>
                                    <div class="flex-1 min-w-0">
                                        <a href="{{ route('product.show', $p->slug) }}"
                                           class="block text-sm font-semibold text-gray-900 truncate hover:text-moss">
                                            {{ $p->name }}
                                        </a>
                                        <p class="text-xs text-gray-500">
                                            {{ $p->formattedPrice() }}
                                        </p>
                                    </div>
                                    <a href="{{ route('product.show', $p->slug) }}"
                                       class="text-xs font-semibold text-rose-600 hover:text-rose-700 whitespace-nowrap">
                                        View <i class="fas fa-arrow-right text-[10px]"></i>
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        @if ($wishlistItemCount > $wishlistItems->count())
                            <p class="mt-3 text-xs text-gray-500">
                                + {{ $wishlistItemCount - $wishlistItems->count() }} more item(s) in your wishlist.
                            </p>
                        @endif
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">🛒 Browse Products</h4>
                        <p class="text-sm text-gray-500 mb-4">Explore our latest products and deals.</p>
                        <a href="{{ route('home') }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            Start Shopping
                        </a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">🛍️ My Cart</h4>
                        <p class="text-sm text-gray-500 mb-4">
                            Review items you've added and update quantities.
                        </p>
                        <a href="{{ route('cart.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-700">
                            View Cart
                        </a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">❤️ My Wishlist</h4>
                        <p class="text-sm text-gray-500 mb-4">
                            Products you've saved for later.
                        </p>
                        <a href="{{ route('wishlist.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-rose-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-rose-700">
                            View Wishlist
                        </a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg opacity-80">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-lg font-semibold text-gray-900">📦 My Orders</h4>
                            <span class="text-[10px] font-bold uppercase tracking-wide text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">
                                Coming soon
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mb-4">Order history and delivery tracking will appear here.</p>
                        <button type="button" disabled
                                class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-500 uppercase tracking-widest cursor-not-allowed">
                            View Orders
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const cartCountEl = document.getElementById('cart-stat-count');
            const cartSubtotalEl = document.getElementById('cart-strip-subtotal');
            const cartLinkEl = document.querySelector('[data-cart-link]');

            const wishlistCountEl = document.getElementById('wishlist-stat-count');
            const wishlistLinkEl = document.querySelector('[data-wishlist-link]');

            const cartEndpoint = @json(route('cart.count'));
            const wishlistEndpoint = @json(route('wishlist.count'));

            function toggleRing(el, activeClass, inactiveClass, on) {
                if (!el) return;
                el.classList.toggle(activeClass, !!on);
                el.classList.toggle(inactiveClass, !on);
            }

            function formatMoney(currency, amount) {
                const n = Number(amount || 0);
                return currency + ' ' + n.toLocaleString('en-KE', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                });
            }

            async function refreshCartCount() {
                if (!cartCountEl) return;
                try {
                    const res = await fetch(cartEndpoint, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin',
                        cache: 'no-store',
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    if (typeof data.count === 'number') {
                        const current = Number(cartCountEl.textContent.trim());
                        if (current !== data.count) {
                            cartCountEl.textContent = data.count;
                            toggleRing(cartLinkEl, 'ring-amber-200', 'ring-transparent', data.count > 0);
                        }
                    }
                    if (cartSubtotalEl && data.currency) {
                        cartSubtotalEl.textContent = formatMoney(data.currency, data.subtotal);
                    }
                } catch (e) { /* ignore */ }
            }

            async function refreshWishlistCount() {
                if (!wishlistCountEl) return;
                try {
                    const res = await fetch(wishlistEndpoint, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin',
                        cache: 'no-store',
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    if (typeof data.count === 'number') {
                        const current = Number(wishlistCountEl.textContent.trim());
                        if (current !== data.count) {
                            wishlistCountEl.textContent = data.count;
                            toggleRing(wishlistLinkEl, 'ring-rose-200', 'ring-transparent', data.count > 0);
                        }
                    }
                } catch (e) { /* ignore */ }
            }

            function refreshAll() {
                refreshCartCount();
                refreshWishlistCount();
            }

            document.addEventListener('DOMContentLoaded', refreshAll);
            document.addEventListener('visibilitychange', function () {
                if (document.visibilityState === 'visible') refreshAll();
            });
            window.addEventListener('focus', refreshAll);
        })();
    </script>
</x-app-layout>