{{-- resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>{{ config('app.name', 'Vuka Shop') }} · modern marketplace</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink: '#14231C',
                        paper: '#F7F5EF',
                        moss: '#20503C',
                        gold: '#E7A93B',
                        line: '#E4E0D5',
                        muted: '#6C7268',
                    },
                    fontFamily: {
                        serif: ['Fraunces', 'ui-serif', 'Georgia', 'serif'],
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    borderRadius: {
                        xl2: '20px',
                    },
                },
            },
        }
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>

    @stack('styles')

    <style>
        [x-cloak] { display: none !important; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; background: #F7F5EF; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { scrollbar-width: none; -ms-overflow-style: none; }
    </style>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('cart', { count: 0 });

            Alpine.data('carousel', () => ({
                current: 0,
                total: 3,
                touchX: 0,
                timer: null,
                start() {
                    this.stop();
                    this.timer = setInterval(() => this.next(), 5000);
                },
                stop() { clearInterval(this.timer); },
                next() { this.current = (this.current + 1) % this.total; },
                prev() { this.current = (this.current - 1 + this.total) % this.total; },
                goTo(i) { this.current = i; this.start(); },
                onTouchStart(e) { this.touchX = e.touches[0].clientX; },
                onTouchEnd(e) {
                    const dx = e.changedTouches[0].clientX - this.touchX;
                    if (Math.abs(dx) > 40) { dx < 0 ? this.next() : this.prev(); this.start(); }
                },
            }));
        });
    </script>
</head>
<body class="text-ink pb-24 sm:pb-0 antialiased" x-data="{ mobileSearchOpen: false }">

    <!-- HEADER -->
    <header class="sticky top-0 z-30 bg-paper/90 backdrop-blur-md border-b border-line">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between gap-3">

            <button class="sm:hidden relative h-10 w-10 rounded-full bg-white flex items-center justify-center text-ink shadow-sm" aria-label="Notifications">
                <i class="fas fa-bell text-sm"></i>
                <span class="absolute top-2 right-2.5 w-2 h-2 rounded-full bg-gold ring-2 ring-white"></span>
            </button>

            <a href="{{ route('home') }}" class="font-serif text-2xl font-semibold tracking-tight text-ink shrink-0">
                Vuka<span class="text-moss">Shop</span>
            </a>

            <button @click="mobileSearchOpen = true" class="sm:hidden h-10 w-10 rounded-full bg-white flex items-center justify-center text-ink shadow-sm" aria-label="Search">
                <i class="fas fa-search text-sm"></i>
            </button>

            <div class="hidden sm:flex items-center gap-2.5 bg-white border border-line rounded-full px-4 py-2.5 flex-1 max-w-sm mx-3">
                <i class="fas fa-search text-muted text-sm"></i>
                <input type="text" placeholder="Search products, brands…" class="bg-transparent outline-none w-full text-sm placeholder:text-muted/70">
            </div>

            <nav class="hidden sm:flex items-center gap-7 text-[15px] font-medium text-ink/85">
                <a href="#" class="hover:text-moss transition-colors">Electronics</a>
                <a href="#" class="hover:text-moss transition-colors">Fashion</a>
                <a href="#" class="hover:text-moss transition-colors">Home &amp; Living</a>
                <a href="#" class="hover:text-moss transition-colors">Beauty</a>
                <a href="#" class="text-gold hover:text-gold/80 transition-colors">Deals</a>
            </nav>

            <div class="hidden sm:flex items-center gap-5">
                <div class="flex items-center gap-4 text-lg text-ink/70">
                    <button class="hover:text-gold transition-colors" aria-label="Wishlist"><i class="far fa-heart"></i></button>
                    <button class="relative hover:text-gold transition-colors" aria-label="Cart">
                        <i class="fas fa-shopping-bag"></i>
                        <span x-show="$store.cart.count > 0" x-text="$store.cart.count"
                              class="absolute -top-2 -right-2 bg-gold text-ink text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center"></span>
                    </button>
                </div>

                @auth
                <a href="{{ route('dashboard') }}" class="text-sm font-semibold px-5 py-2.5 rounded-full text-ink hover:bg-white transition-colors">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm font-semibold px-5 py-2.5 rounded-full bg-white border border-line hover:bg-line/60 transition-colors">Logout</button>
                </form>
                @else
                <a href="{{ route('login') }}" class="text-sm font-semibold px-5 py-2.5 rounded-full text-ink hover:bg-white transition-colors">Log in</a>
                <a href="{{ route('register') }}" class="text-sm font-semibold px-5 py-2.5 rounded-full bg-moss text-white hover:bg-moss/90 transition-colors shadow-[0_8px_18px_-6px_rgba(32,80,60,0.55)]">Register</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- MOBILE SEARCH OVERLAY -->
    <div x-show="mobileSearchOpen" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @keydown.escape.window="mobileSearchOpen = false"
         @click.self="mobileSearchOpen = false"
         x-init="$watch('mobileSearchOpen', v => v && $nextTick(() => $refs.mobileSearchInput.focus()))"
         class="fixed inset-0 z-50 bg-ink/40 backdrop-blur-sm flex items-start justify-center pt-24 px-4">
        <div x-show="mobileSearchOpen"
             x-transition:enter="transition ease-out duration-200 delay-75" x-transition:enter-start="opacity-0 -translate-y-3" x-transition:enter-end="opacity-100 translate-y-0"
             class="bg-white rounded-2xl p-5 w-full max-w-md shadow-2xl">
            <div class="flex items-center gap-3 bg-paper rounded-full px-4 py-3">
                <i class="fas fa-search text-muted"></i>
                <input x-ref="mobileSearchInput" type="text" placeholder="Search products, brands…" class="bg-transparent outline-none w-full text-base">
            </div>
            <button @click="mobileSearchOpen = false" class="mt-3 w-full py-2.5 rounded-full bg-paper text-ink/70 font-semibold text-sm hover:bg-line transition-colors">
                <i class="fas fa-times mr-1"></i> Cancel
            </button>
        </div>
    </div>

    <!-- MAIN -->
    <main class="max-w-7xl mx-auto px-4">

        <!-- HERO CAROUSEL -->
        <div class="relative rounded-xl2 overflow-hidden shadow-lg my-4" x-data="carousel" x-init="start()" @mouseenter="stop()" @mouseleave="start()">
            <div class="flex transition-transform duration-500 ease-out"
                 :style="`transform: translateX(-${current * 100}%)`"
                 @touchstart="onTouchStart($event)" @touchend="onTouchEnd($event)">
                @php
                $slides = [
                ['eyebrow' => 'Weekend flash sale', 'title' => 'Save up to 30%', 'sub' => 'Limited-time offers on selected electronics.', 'cta' => 'Shop now', 'icon' => 'fa-bolt', 'grad' => 'linear-gradient(135deg, #14231C 0%, #2E4A38 100%)'],
                ['eyebrow' => 'Just landed', 'title' => 'New arrivals', 'sub' => 'Fresh drops in fashion, premium quality.', 'cta' => 'Explore', 'icon' => 'fa-star', 'grad' => 'linear-gradient(135deg, #1F2E24 0%, #3B4A2E 100%)'],
                ['eyebrow' => 'Nationwide', 'title' => 'Pay with M-Pesa', 'sub' => 'Fast, secure checkout, delivered to your door.', 'cta' => 'Pay now', 'icon' => 'fa-mobile-alt', 'grad' => 'linear-gradient(135deg, #14231C 0%, #204034 100%)'],
                ];
                @endphp
                @foreach($slides as $slide)
                <div class="min-w-full px-6 py-10 sm:px-12 sm:py-16 relative flex flex-col justify-center text-white" style="background-image: {{ $slide['grad'] }};">
                    <div class="absolute inset-0 bg-gradient-to-br from-black/55 via-black/15 to-transparent"></div>
                    <div class="relative z-10 max-w-xs sm:max-w-md">
                        <span class="inline-flex items-center gap-2 text-sm text-white/75 mb-2">
                            <i class="fas {{ $slide['icon'] }} text-gold"></i>{{ $slide['eyebrow'] }}
                        </span>
                        <h2 class="font-serif text-3xl sm:text-5xl font-semibold tracking-tight leading-[1.05]">{{ $slide['title'] }}</h2>
                        <p class="text-white/80 mt-3 mb-5 text-sm sm:text-base">{{ $slide['sub'] }}</p>
                        <button class="inline-flex items-center gap-2 bg-gold text-ink font-bold px-6 py-2.5 rounded-full hover:bg-gold/90 transition-colors text-sm">
                            {{ $slide['cta'] }} <i class="fas fa-arrow-right text-xs"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="absolute bottom-4 right-5 flex gap-2">
                <template x-for="i in 3" :key="i">
                    <button @click="goTo(i - 1)" :class="current === i - 1 ? 'w-6 bg-white' : 'w-2 bg-white/40'" class="h-2 rounded-full transition-all" :aria-label="`Slide ${i}`"></button>
                </template>
            </div>
        </div>

        <!-- CATEGORY CHIPS -->
        <div class="flex gap-2.5 overflow-x-auto no-scrollbar py-1 pb-5" x-data="{ active: 'All' }">
            <a href="#"
               class="flex items-center gap-2 whitespace-nowrap border border-ink bg-ink text-white rounded-full px-4 py-2 text-sm font-medium shadow-sm transition-colors">
                <i class="fas fa-fire text-xs text-gold"></i>
                All
            </a>
            @foreach ($categories as $category)
                <a href="{{ route('category.show', $category->slug) }}"
                   class="flex items-center gap-2 whitespace-nowrap border border-line bg-white text-ink rounded-full px-4 py-2 text-sm font-medium shadow-sm transition-colors hover:border-moss/40">
                    @if ($category->icon)
                        <i class="fas {{ $category->icon }} text-xs text-moss"></i>
                    @endif
                    {{ $category->name }}
                </a>
            @endforeach
        </div>

        {{-- FEATURED SECTION (only if there are featured products) --}}
        @if ($featured->isNotEmpty())
            <div class="flex items-baseline justify-between mt-2 mb-3">
                <h3 class="font-serif text-xl font-semibold flex items-center gap-2">
                    <i class="fas fa-crown text-gold text-base"></i> Featured
                </h3>
                <a href="#" class="text-moss font-semibold text-sm flex items-center gap-1.5 hover:text-moss/70 transition-colors">
                    See all <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3.5 mb-8">
                @foreach ($featured as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        @endif

        <!-- POPULAR PRODUCTS -->
        <div class="flex items-baseline justify-between mt-2 mb-3">
            <h3 class="font-serif text-xl font-semibold flex items-center gap-2">
                <i class="fas fa-fire text-gold text-base"></i> Popular right now
            </h3>
            <a href="#" class="text-moss font-semibold text-sm flex items-center gap-1.5 hover:text-moss/70 transition-colors">
                See all <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>

        @if ($popular->isEmpty())
            <div class="bg-white rounded-2xl border border-line p-8 text-center mb-8">
                <i class="fas fa-box-open text-3xl text-muted/40 mb-3"></i>
                <p class="text-sm text-muted">No products yet. Check back soon!</p>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3.5 mb-8">
                @foreach ($popular as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        @endif

        <!-- PROMO BANNER -->
        <div class="relative overflow-hidden rounded-xl2 bg-ink text-white px-6 py-8 sm:px-10 sm:py-10 mt-8 flex flex-col gap-2">
            <i class="fas fa-mobile-alt absolute -right-2 bottom-0 text-[7rem] text-white/[0.06] rotate-[-8deg]"></i>
            <h4 class="font-serif text-xl sm:text-2xl font-semibold flex items-center gap-2 relative z-10">
                <i class="fas fa-truck text-gold"></i> Free delivery over KSh 5,000
            </h4>
            <p class="text-white/70 text-sm relative z-10">Nationwide delivery, with M-Pesa and card accepted at checkout.</p>
            <button class="relative z-10 self-start mt-2 inline-flex items-center gap-2 bg-gold text-ink font-bold px-6 py-2.5 rounded-full hover:bg-gold/90 transition-colors text-sm">
                <i class="fas fa-shopping-cart"></i> Shop now
            </button>
        </div>

        <!-- BEST SELLERS -->
        <div class="flex items-baseline justify-between mt-9 mb-4">
            <h3 class="font-serif text-xl font-semibold flex items-center gap-2">
                <i class="fas fa-star text-gold text-base"></i> Best sellers
            </h3>
            <a href="#" class="text-moss font-semibold text-sm flex items-center gap-1.5 hover:text-moss/70 transition-colors">
                View all <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>

        @if ($bestsellers->isEmpty())
            <div class="bg-white rounded-2xl border border-line p-8 text-center">
                <i class="fas fa-box-open text-3xl text-muted/40 mb-3"></i>
                <p class="text-sm text-muted">No best sellers yet.</p>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3.5">
                @foreach ($bestsellers as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        @endif

        <!-- VENDOR CTA -->
        <div class="relative overflow-hidden rounded-xl2 mt-9 text-white"
             style="background-image: linear-gradient(135deg, #14231C 0%, #20503C 100%);">
            <i class="fas fa-store absolute -right-3 -bottom-3 text-[9rem] text-white/[0.05] rotate-[-6deg] pointer-events-none"></i>

            <div class="relative z-10 px-6 py-10 sm:px-12 sm:py-14 max-w-2xl">
                <span class="inline-flex items-center gap-2 text-sm text-white/75 mb-2">
                    <i class="fas fa-handshake text-gold"></i> Partner with us
                </span>

                <h3 class="font-serif text-2xl sm:text-4xl font-semibold tracking-tight leading-[1.1]">
                    Sell on VukaShop.<br class="hidden sm:inline"> Reach thousands of buyers.
                </h3>

                <p class="text-white/75 mt-3 mb-6 text-sm sm:text-base max-w-lg">
                    Own a shop? Partner with VukaShop and list your products to customers
                    across the country. Verified vendors, secure payouts, and logistics
                    handled end-to-end.
                </p>

                <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-5">
                    <a href="{{ route('vendor.apply') }}"
                       class="inline-flex items-center justify-center gap-2 bg-gold text-ink font-bold px-7 py-3 rounded-full hover:bg-gold/90 transition-colors text-sm shadow-[0_10px_24px_-8px_rgba(231,169,59,0.65)]">
                        Become a Vendor <i class="fas fa-arrow-right text-xs"></i>
                    </a>

                    <a href="{{ route('login') }}"
                       class="text-xs sm:text-sm text-white/60 hover:text-white/90 transition-colors">
                        Already a vendor? Log in to your dashboard <i class="fas fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- NEWSLETTER -->
        <div class="bg-white border border-line rounded-xl2 px-5 py-8 sm:py-10 mt-9" x-data="{ email: '', sent: false }">
            <div class="text-center max-w-md mx-auto">
                <div class="w-11 h-11 rounded-full bg-paper flex items-center justify-center mx-auto mb-3 text-moss">
                    <i class="fas fa-envelope"></i>
                </div>
                <h4 class="font-serif text-lg font-semibold">Stay in the know</h4>
                <p class="text-ink/60 text-sm mt-1.5 mb-5">Get deals, new arrivals and exclusives, straight to your inbox.</p>
                <form class="flex gap-2 flex-wrap justify-center" @submit.prevent="sent = true">
                    <input type="email" x-model="email" placeholder="Your email" required
                           class="flex-1 min-w-[160px] px-5 py-2.5 rounded-full border border-line bg-paper text-sm focus:outline-none focus:ring-2 focus:ring-moss/40 focus:border-transparent">
                    <button type="submit" class="inline-flex items-center gap-2 bg-moss text-white font-bold px-6 py-2.5 rounded-full hover:bg-moss/90 transition-colors text-sm">
                        <i class="fas" :class="sent ? 'fa-check' : 'fa-paper-plane'"></i>
                        <span x-text="sent ? 'Subscribed' : 'Subscribe'"></span>
                    </button>
                </form>
            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="mt-10 bg-white border-t border-line pt-8 pb-6">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div>
                    <h5 class="font-serif text-lg font-semibold">Vuka<span class="text-moss">Shop</span></h5>
                    <p class="text-ink/60 text-sm mt-1.5">Kenya's trusted marketplace.</p>
                </div>
                <div>
                    <h5 class="font-semibold text-sm mb-2">Shop</h5>
                    <ul class="space-y-1.5">
                        <li><a href="#" class="text-ink/60 text-sm hover:text-moss transition-colors">Electronics</a></li>
                        <li><a href="#" class="text-ink/60 text-sm hover:text-moss transition-colors">Fashion</a></li>
                        <li><a href="#" class="text-ink/60 text-sm hover:text-moss transition-colors">Home</a></li>
                        <li><a href="#" class="text-ink/60 text-sm hover:text-moss transition-colors">Beauty</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-semibold text-sm mb-2">Support</h5>
                    <ul class="space-y-1.5">
                        <li><a href="#" class="text-ink/60 text-sm hover:text-moss transition-colors">Contact</a></li>
                        <li><a href="#" class="text-ink/60 text-sm hover:text-moss transition-colors">Delivery</a></li>
                        <li><a href="#" class="text-ink/60 text-sm hover:text-moss transition-colors">Returns</a></li>
                        <li><a href="#" class="text-ink/60 text-sm hover:text-moss transition-colors">M-Pesa FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-semibold text-sm mb-2">Company</h5>
                    <ul class="space-y-1.5">
                        <li><a href="#" class="text-ink/60 text-sm hover:text-moss transition-colors">About</a></li>
                        <li><a href="{{ route('vendor.apply') }}" class="text-ink/60 text-sm hover:text-moss transition-colors">Become a Vendor</a></li>
                        <li><a href="#" class="text-ink/60 text-sm hover:text-moss transition-colors">Careers</a></li>
                        <li><a href="#" class="text-ink/60 text-sm hover:text-moss transition-colors">Privacy</a></li>
                        <li><a href="#" class="text-ink/60 text-sm hover:text-moss transition-colors">Terms</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-7 border-t border-line pt-4 flex flex-wrap items-center justify-between gap-3 text-sm text-muted">
                <span>© 2026 Vuka Shop. All rights reserved.</span>
                <span class="flex gap-4 text-base">
                    <i class="fab fa-twitter hover:text-moss transition-colors cursor-pointer"></i>
                    <i class="fab fa-instagram hover:text-moss transition-colors cursor-pointer"></i>
                    <i class="fab fa-youtube hover:text-moss transition-colors cursor-pointer"></i>
                </span>
            </div>
        </div>
    </footer>

    <!-- BOTTOM APP NAV (mobile) -->
    <nav class="sm:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-line flex justify-around px-1"
         style="padding-bottom: calc(6px + env(safe-area-inset-bottom));">
        <a href="{{ route('home') }}" class="flex-1 flex flex-col items-center gap-1 py-2.5 text-[11px] font-semibold text-moss">
            <i class="fas fa-home text-base"></i> Home
        </a>
        <a href="#" class="flex-1 flex flex-col items-center gap-1 py-2.5 text-[11px] font-semibold text-muted">
            <i class="fas fa-th-large text-base"></i> Categories
        </a>
        <a href="#" class="relative flex-1 flex flex-col items-center gap-1 py-2.5 text-[11px] font-semibold text-muted">
            <i class="fas fa-shopping-bag text-base"></i> Cart
            <span x-data x-show="$store.cart.count > 0" x-text="$store.cart.count"
                  class="absolute top-1 right-1/3 w-4 h-4 rounded-full bg-gold text-ink text-[10px] font-bold flex items-center justify-center"></span>
        </a>
        @auth
        <a href="{{ route('dashboard') }}" class="flex-1 flex flex-col items-center gap-1 py-2.5 text-[11px] font-semibold text-muted">
            <i class="fas fa-user text-base"></i> Account
        </a>
        @else
        <a href="{{ route('login') }}" class="flex-1 flex flex-col items-center gap-1 py-2.5 text-[11px] font-semibold text-muted">
            <i class="fas fa-user text-base"></i> Account
        </a>
        @endauth
    </nav>

    @stack('scripts')
</body>
</html>