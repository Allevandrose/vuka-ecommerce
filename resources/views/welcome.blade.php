{{-- resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>{{ config('app.name', 'Vuka Shop') }} · modern marketplace</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
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

    {{-- Alpine plugins (focus trap + collapse) must load before Alpine core --}}
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.14.1/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.14.1/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>

    @stack('styles')

    <style>
        [x-cloak] {
            display: none !important;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #F7F5EF;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        /* Visible keyboard focus across the navbar */
        header a:focus-visible,
        header button:focus-visible,
        #mobile-drawer a:focus-visible,
        #mobile-drawer button:focus-visible {
            outline: 2px solid #20503C;
            outline-offset: 2px;
        }

        /* Remove default search input outline & focus ring */
        input[type="text"],
        input[type="email"],
        input[type="search"] {
            outline: none !important;
            box-shadow: none !important;
        }

        /* Keep a subtle custom focus style for the search wrapper */
        .search-wrapper:focus-within {
            border-color: #20503C;
            box-shadow: 0 0 0 4px rgba(32, 80, 60, 0.1);
        }

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }

            * {
                transition-duration: 0.01ms !important;
            }
        }
    </style>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('cart', {
                count: 0
            });

            Alpine.data('carousel', () => ({
                current: 0,
                total: 3,
                touchX: 0,
                timer: null,
                start() {
                    this.stop();
                    this.timer = setInterval(() => this.next(), 5000);
                },
                stop() {
                    clearInterval(this.timer);
                },
                next() {
                    this.current = (this.current + 1) % this.total;
                },
                prev() {
                    this.current = (this.current - 1 + this.total) % this.total;
                },
                goTo(i) {
                    this.current = i;
                    this.start();
                },
                onTouchStart(e) {
                    this.touchX = e.touches[0].clientX;
                },
                onTouchEnd(e) {
                    const dx = e.changedTouches[0].clientX - this.touchX;
                    if (Math.abs(dx) > 40) {
                        dx < 0 ? this.next() : this.prev();
                        this.start();
                    }
                },
            }));
        });
    </script>
</head>

<body class="text-ink pb-24 lg:pb-0 antialiased"
    x-data="{ mobileSearchOpen: false, menuOpen: false, scrolled: false }"
    x-effect="document.body.classList.toggle('overflow-hidden', menuOpen || mobileSearchOpen)"
    @scroll.window.passive="scrolled = window.scrollY > 8"
    @resize.window="if (window.innerWidth >= 1024) { menuOpen = false; mobileSearchOpen = false }"
    @keydown.escape.window="menuOpen = false; mobileSearchOpen = false">

    <!-- ANNOUNCEMENT BAR -->
    <div x-data="{ show: true }" x-show="show" x-cloak
        class="bg-moss text-white text-xs sm:text-[13px]">
        <div class="max-w-7xl mx-auto px-4 py-2 flex items-center justify-center gap-2 relative">
            <i class="fas fa-truck text-gold text-[11px]"></i>
            <p class="text-center font-medium pr-6 sm:pr-0">Free delivery over KSh 5,000. Pay with M-Pesa or card.</p>
            <button @click="show = false" aria-label="Dismiss announcement"
                class="absolute right-3 top-1/2 -translate-y-1/2 h-6 w-6 rounded-full flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 transition-colors">
                <i class="fas fa-times text-[11px]"></i>
            </button>
        </div>
    </div>

       <!-- HEADER -->
    <header class="sticky top-0 z-30 bg-paper/90 backdrop-blur-md border-b border-line transition-shadow duration-200"
        :class="scrolled ? 'shadow-[0_6px_20px_-12px_rgba(20,35,28,0.35)]' : ''">
        <div class="max-w-7xl mx-auto px-4 h-16 flex items-center gap-3 lg:gap-6">

            {{-- Mobile / tablet: menu button --}}
            <button @click="menuOpen = true"
                class="lg:hidden h-10 w-10 shrink-0 rounded-full bg-white border border-line flex items-center justify-center text-ink shadow-sm hover:bg-line/50 transition-colors"
                aria-label="Open menu" :aria-expanded="menuOpen.toString()" aria-controls="mobile-drawer">
                <i class="fas fa-bars text-sm"></i>
            </button>

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="shrink-0 max-lg:mx-auto lg:mx-0" aria-label="VukaShop home">
                <img src="{{ asset('images/vukashop-logo.svg') }}" alt="VukaShop" class="h-9 sm:h-10 w-auto">
            </a>

            {{-- Desktop search: borderless filled pill, lifts and turns white on focus --}}
            <form action="{{ route('search') }}" method="GET" role="search"
                x-data="{ q: '', focused: false }"
                @keydown.slash.window="if (!['INPUT','TEXTAREA','SELECT'].includes(document.activeElement.tagName) && window.innerWidth >= 1024) { $event.preventDefault(); $refs.q.focus(); }"
                class="hidden lg:flex flex-1 max-w-xl items-center gap-2 rounded-full pl-5 pr-1.5 h-11 transition-all duration-200"
                :class="focused
                    ? 'bg-white shadow-[0_10px_30px_-10px_rgba(32,80,60,0.35)]'
                    : 'bg-ink/[0.05] hover:bg-ink/[0.08]'">

                <input x-ref="q" x-model="q" @focus="focused = true" @blur="focused = false" type="text" name="q"
                    placeholder="Search products, brands…" autocomplete="off" enterkeyhint="search"
                    aria-label="Search products"
                    class="bg-transparent border-0 outline-none ring-0 focus:ring-0 focus:outline-none w-full text-sm placeholder:text-muted/70">

                <button type="button" x-show="q.length > 0" x-cloak @click="q = ''; $refs.q.focus()"
                    aria-label="Clear search"
                    class="h-7 w-7 shrink-0 rounded-full flex items-center justify-center text-muted hover:text-ink hover:bg-ink/5 transition-colors">
                    <i class="fas fa-times text-xs"></i>
                </button>

                <kbd x-show="q.length === 0 && !focused"
                    class="hidden xl:inline-flex items-center justify-center h-6 min-w-[24px] px-1.5 rounded-md bg-white/80 shadow-sm text-[11px] font-semibold text-muted">/</kbd>

                <button type="submit" aria-label="Search"
                    class="h-8 w-8 shrink-0 rounded-full bg-gold text-ink flex items-center justify-center hover:bg-gold/90 active:scale-95 transition-all">
                    <i class="fas fa-search text-xs"></i>
                </button>
            </form>

            {{-- Desktop nav links --}}
            <nav class="hidden lg:flex items-center gap-5 text-[14px] font-medium text-ink/85 ml-auto"
                aria-label="Primary">
                <a href="#" class="inline-flex items-center gap-1.5 text-gold hover:text-gold/80 transition-colors">
                    <i class="fas fa-bolt text-xs"></i> Deals
                </a>
                <a href="#" class="hover:text-moss transition-colors">Track Order</a>
                <a href="#" class="hover:text-moss transition-colors">Support</a>
            </nav>

            {{-- Desktop actions --}}
            <div class="hidden lg:flex items-center gap-3">
                <div class="flex items-center gap-1 text-lg text-ink/70">
                    <button
                        class="h-10 w-10 rounded-full flex items-center justify-center hover:text-gold hover:bg-white transition-colors"
                        aria-label="Wishlist"><i class="far fa-heart"></i></button>
                    <button
                        class="relative h-10 w-10 rounded-full flex items-center justify-center hover:text-gold hover:bg-white transition-colors"
                        aria-label="Cart">
                        <i class="fas fa-shopping-bag"></i>
                        <span x-show="$store.cart.count > 0" x-text="$store.cart.count"
                            class="absolute top-0.5 right-0.5 bg-gold text-ink text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center"></span>
                    </button>
                </div>

                <span class="h-6 w-px bg-line" aria-hidden="true"></span>

                @auth
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false"
                        @keydown.escape="open = false">
                        <button @click="open = !open"
                            class="h-10 pl-3 pr-3.5 rounded-full bg-white border border-line flex items-center gap-2 text-sm font-semibold hover:bg-line/40 transition-colors"
                            aria-haspopup="true" :aria-expanded="open.toString()">
                            <i class="fas fa-user-circle text-moss text-base"></i>
                            Account
                            <i class="fas fa-chevron-down text-[10px] text-muted transition-transform"
                                :class="open ? 'rotate-180' : ''"></i>
                        </button>

                        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-52 origin-top-right bg-white border border-line rounded-2xl shadow-xl p-1.5">
                            <a href="{{ route('dashboard') }}"
                                class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-paper transition-colors">
                                <i class="fas fa-gauge text-moss w-4 text-center"></i> Dashboard
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium text-left hover:bg-paper transition-colors">
                                    <i class="fas fa-arrow-right-from-bracket text-muted w-4 text-center"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                        class="text-sm font-semibold px-4 py-2.5 rounded-full text-ink hover:bg-white transition-colors whitespace-nowrap">Log
                        in</a>
                    <a href="{{ route('register') }}"
                        class="text-sm font-semibold px-5 py-2.5 rounded-full bg-moss text-white hover:bg-moss/90 transition-colors shadow-[0_8px_18px_-6px_rgba(32,80,60,0.55)]">Register</a>
                @endauth
            </div>

            {{-- Mobile / tablet: right actions --}}
            <div class="lg:hidden flex items-center gap-2 shrink-0">
                <button @click="mobileSearchOpen = true"
                    class="h-10 w-10 rounded-full bg-white border border-line flex items-center justify-center text-ink shadow-sm hover:bg-line/50 transition-colors"
                    aria-label="Search">
                    <i class="fas fa-search text-sm"></i>
                </button>
                <button
                    class="relative h-10 w-10 rounded-full bg-white border border-line flex items-center justify-center text-ink shadow-sm hover:bg-line/50 transition-colors"
                    aria-label="Notifications">
                    <i class="fas fa-bell text-sm"></i>
                    <span class="absolute top-2 right-2.5 w-2 h-2 rounded-full bg-gold ring-2 ring-white"></span>
                </button>
            </div>
        </div>
    </header>

    <!-- MOBILE DRAWER -->
    <div class="lg:hidden" x-show="menuOpen" x-cloak>
        {{-- Backdrop --}}
        <div x-show="menuOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="menuOpen = false"
            class="fixed inset-0 z-50 bg-ink/50 backdrop-blur-sm" aria-hidden="true"></div>

        {{-- Panel --}}
        <aside id="mobile-drawer" x-show="menuOpen" x-trap.noscroll="menuOpen"
            x-transition:enter="transition ease-out duration-250 transform"
            x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full" role="dialog" aria-modal="true" aria-label="Main menu"
            class="fixed inset-y-0 left-0 z-50 w-[86%] max-w-sm bg-paper shadow-2xl flex flex-col"
            style="padding-top: env(safe-area-inset-top); padding-bottom: env(safe-area-inset-bottom);">

            <div class="flex items-center justify-between px-5 h-16 border-b border-line shrink-0">
                <a href="{{ route('home') }}" @click="menuOpen = false" aria-label="VukaShop home">
                    <img src="{{ asset('images/vukashop-logo.svg') }}" alt="VukaShop" class="h-9 w-auto">
                </a>
                <button @click="menuOpen = false"
                    class="h-10 w-10 rounded-full bg-white border border-line flex items-center justify-center text-ink hover:bg-line/50 transition-colors"
                    aria-label="Close menu">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto no-scrollbar px-5 py-5 space-y-6">

                {{-- Drawer search --}}
                <form action="{{ route('search') }}" method="GET" role="search"
                    class="flex items-center gap-2.5 bg-white border border-line rounded-full pl-4 pr-1.5 h-12 focus-within:border-moss focus-within:ring-4 focus-within:ring-moss/10 transition-all search-wrapper">
                    <i class="fas fa-search text-muted text-sm"></i>
                    <input type="text" name="q" placeholder="Search products, brands…" autocomplete="off"
                        enterkeyhint="search" aria-label="Search products"
                        class="bg-transparent outline-none w-full text-base placeholder:text-muted/70 search-input">
                    <button type="submit"
                        class="h-9 w-9 shrink-0 rounded-full bg-moss text-white flex items-center justify-center hover:bg-moss/90 transition-colors"
                        aria-label="Search">
                        <i class="fas fa-arrow-right text-xs"></i>
                    </button>
                </form>

                {{-- Quick links --}}
                <nav class="space-y-1" aria-label="Quick links">
                    <a href="#" @click="menuOpen = false"
                        class="flex items-center gap-3 px-3 py-3 rounded-xl text-[15px] font-semibold text-gold hover:bg-white transition-colors">
                        <i class="fas fa-bolt w-5 text-center"></i> Deals
                    </a>
                    <a href="#" @click="menuOpen = false"
                        class="flex items-center gap-3 px-3 py-3 rounded-xl text-[15px] font-medium hover:bg-white transition-colors">
                        <i class="fas fa-truck-fast w-5 text-center text-moss"></i> Track Order
                    </a>
                    <a href="#" @click="menuOpen = false"
                        class="flex items-center gap-3 px-3 py-3 rounded-xl text-[15px] font-medium hover:bg-white transition-colors">
                        <i class="fas fa-headset w-5 text-center text-moss"></i> Support
                    </a>
                    <a href="#" @click="menuOpen = false"
                        class="flex items-center gap-3 px-3 py-3 rounded-xl text-[15px] font-medium hover:bg-white transition-colors">
                        <i class="far fa-heart w-5 text-center text-moss"></i> Wishlist
                    </a>
                </nav>

                {{-- Categories --}}
                @if ($categories->isNotEmpty())
                    <div x-data="{ open: true }">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between px-3 mb-2 text-sm font-semibold text-muted"
                            :aria-expanded="open.toString()">
                            Categories
                            <i class="fas fa-chevron-down text-[10px] transition-transform"
                                :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-collapse.duration.200ms class="space-y-1">
                            @foreach ($categories as $category)
                                <a href="{{ route('category.show', $category->slug) }}" @click="menuOpen = false"
                                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[15px] font-medium hover:bg-white transition-colors">
                                    <span
                                        class="h-8 w-8 rounded-full bg-white border border-line flex items-center justify-center shrink-0">
                                        @if ($category->icon)
                                            <i class="fas {{ $category->icon }} text-xs text-moss"></i>
                                        @else
                                            <i class="fas fa-tag text-xs text-moss"></i>
                                        @endif
                                    </span>
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Drawer footer: auth --}}
            <div class="shrink-0 border-t border-line bg-white px-5 py-4">
                @auth
                    <div class="grid grid-cols-2 gap-2.5">
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-full bg-moss text-white text-sm font-semibold py-3 hover:bg-moss/90 transition-colors">
                            <i class="fas fa-gauge text-xs"></i> Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-full bg-paper border border-line text-sm font-semibold py-3 hover:bg-line/60 transition-colors">
                                <i class="fas fa-arrow-right-from-bracket text-xs"></i> Logout
                            </button>
                        </form>
                    </div>
                @else
                    <div class="grid grid-cols-2 gap-2.5">
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center justify-center rounded-full bg-paper border border-line text-sm font-semibold py-3 hover:bg-line/60 transition-colors">
                            Log in
                        </a>
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center justify-center rounded-full bg-moss text-white text-sm font-semibold py-3 hover:bg-moss/90 transition-colors">
                            Register
                        </a>
                    </div>
                @endauth
            </div>
        </aside>
    </div>

    <!-- MOBILE SEARCH OVERLAY -->
    <div x-show="mobileSearchOpen" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" @click.self="mobileSearchOpen = false"
        x-init="$watch('mobileSearchOpen', v => v && $nextTick(() => $refs.mobileSearchInput.focus()))"
        class="lg:hidden fixed inset-0 z-50 bg-ink/40 backdrop-blur-sm flex items-start justify-center pt-20 px-4"
        role="dialog" aria-modal="true" aria-label="Search">
        <div x-show="mobileSearchOpen" x-transition:enter="transition ease-out duration-200 delay-75"
            x-transition:enter-start="opacity-0 -translate-y-3" x-transition:enter-end="opacity-100 translate-y-0"
            class="bg-white rounded-2xl p-4 w-full max-w-md shadow-2xl" x-data="{ q: '' }">

            {{-- Mobile search --}}
            <form action="{{ route('search') }}" method="GET" role="search" @submit="mobileSearchOpen = false">
                <div
                    class="flex items-center gap-3 bg-paper border border-line rounded-full pl-4 pr-1.5 h-12 focus-within:border-moss focus-within:ring-4 focus-within:ring-moss/10 transition-all search-wrapper">
                    <i class="fas fa-search text-muted text-sm"></i>
                    <input x-ref="mobileSearchInput" x-model="q" type="text" name="q"
                        placeholder="Search products, brands…" autocomplete="off" enterkeyhint="search"
                        aria-label="Search products"
                        class="bg-transparent outline-none w-full text-base placeholder:text-muted/70 search-input">
                    <button type="button" x-show="q.length > 0" x-cloak @click="q = ''; $refs.mobileSearchInput.focus()"
                        class="h-8 w-8 shrink-0 rounded-full flex items-center justify-center text-muted hover:text-ink"
                        aria-label="Clear search">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                    <button type="submit"
                        class="h-9 px-4 shrink-0 rounded-full bg-moss text-white font-semibold text-sm hover:bg-moss/90 transition-colors">
                        Go
                    </button>
                </div>
            </form>

            <button @click="mobileSearchOpen = false"
                class="mt-3 w-full py-2.5 rounded-full bg-paper text-ink/70 font-semibold text-sm hover:bg-line transition-colors">
                <i class="fas fa-times mr-1"></i> Cancel
            </button>
        </div>
    </div>

    <!-- MAIN -->
    <main class="max-w-7xl mx-auto px-4">

        <!-- HERO CAROUSEL -->
        <div class="relative rounded-xl2 overflow-hidden shadow-lg my-4" x-data="carousel"
            x-init="start()" @mouseenter="stop()" @mouseleave="start()">
            <div class="flex transition-transform duration-500 ease-out"
                :style="`transform: translateX(-${current * 100}%)`" @touchstart="onTouchStart($event)"
                @touchend="onTouchEnd($event)">
                @php
                    $slides = [
                        [
                            'eyebrow' => 'Weekend flash sale',
                            'title' => 'Save up to 30%',
                            'sub' => 'Limited-time offers on selected electronics.',
                            'cta' => 'Shop now',
                            'icon' => 'fa-bolt',
                            'grad' => 'linear-gradient(135deg, #14231C 0%, #2E4A38 100%)',
                        ],
                        [
                            'eyebrow' => 'Just landed',
                            'title' => 'New arrivals',
                            'sub' => 'Fresh drops in fashion, premium quality.',
                            'cta' => 'Explore',
                            'icon' => 'fa-star',
                            'grad' => 'linear-gradient(135deg, #1F2E24 0%, #3B4A2E 100%)',
                        ],
                        [
                            'eyebrow' => 'Nationwide',
                            'title' => 'Pay with M-Pesa',
                            'sub' => 'Fast, secure checkout, delivered to your door.',
                            'cta' => 'Pay now',
                            'icon' => 'fa-mobile-alt',
                            'grad' => 'linear-gradient(135deg, #14231C 0%, #204034 100%)',
                        ],
                    ];
                @endphp
                @foreach ($slides as $slide)
                    <div class="min-w-full px-6 py-10 sm:px-12 sm:py-16 relative flex flex-col justify-center text-white"
                        style="background-image: {{ $slide['grad'] }};">
                        <div class="absolute inset-0 bg-gradient-to-br from-black/55 via-black/15 to-transparent">
                        </div>
                        <div class="relative z-10 max-w-xs sm:max-w-md">
                            <span class="inline-flex items-center gap-2 text-sm text-white/75 mb-2">
                                <i class="fas {{ $slide['icon'] }} text-gold"></i>{{ $slide['eyebrow'] }}
                            </span>
                            <h2 class="font-serif text-3xl sm:text-5xl font-semibold tracking-tight leading-[1.05]">
                                {{ $slide['title'] }}</h2>
                            <p class="text-white/80 mt-3 mb-5 text-sm sm:text-base">{{ $slide['sub'] }}</p>
                            <button
                                class="inline-flex items-center gap-2 bg-gold text-ink font-bold px-6 py-2.5 rounded-full hover:bg-gold/90 transition-colors text-sm">
                                {{ $slide['cta'] }} <i class="fas fa-arrow-right text-xs"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="absolute bottom-4 right-5 flex gap-2">
                <template x-for="i in 3" :key="i">
                    <button @click="goTo(i - 1)" :class="current === i - 1 ? 'w-6 bg-white' : 'w-2 bg-white/40'"
                        class="h-2 rounded-full transition-all" :aria-label="`Slide ${i}`"></button>
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

        {{-- FEATURED SECTION --}}
        @if ($featured->isNotEmpty())
            <div class="flex items-baseline justify-between mt-2 mb-3">
                <h3 class="font-serif text-xl font-semibold flex items-center gap-2">
                    <i class="fas fa-crown text-gold text-base"></i> Featured
                </h3>
                <a href="#"
                    class="text-moss font-semibold text-sm flex items-center gap-1.5 hover:text-moss/70 transition-colors">
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
            <a href="#"
                class="text-moss font-semibold text-sm flex items-center gap-1.5 hover:text-moss/70 transition-colors">
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
        <div
            class="relative overflow-hidden rounded-xl2 bg-ink text-white px-6 py-8 sm:px-10 sm:py-10 mt-8 flex flex-col gap-2">
            <i class="fas fa-mobile-alt absolute -right-2 bottom-0 text-[7rem] text-white/[0.06] rotate-[-8deg]"></i>
            <h4 class="font-serif text-xl sm:text-2xl font-semibold flex items-center gap-2 relative z-10">
                <i class="fas fa-truck text-gold"></i> Free delivery over KSh 5,000
            </h4>
            <p class="text-white/70 text-sm relative z-10">Nationwide delivery, with M-Pesa and card accepted at
                checkout.</p>
            <button
                class="relative z-10 self-start mt-2 inline-flex items-center gap-2 bg-gold text-ink font-bold px-6 py-2.5 rounded-full hover:bg-gold/90 transition-colors text-sm">
                <i class="fas fa-shopping-cart"></i> Shop now
            </button>
        </div>

        <!-- BEST SELLERS -->
        <div class="flex items-baseline justify-between mt-9 mb-4">
            <h3 class="font-serif text-xl font-semibold flex items-center gap-2">
                <i class="fas fa-star text-gold text-base"></i> Best sellers
            </h3>
            <a href="#"
                class="text-moss font-semibold text-sm flex items-center gap-1.5 hover:text-moss/70 transition-colors">
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
            <i
                class="fas fa-store absolute -right-3 -bottom-3 text-[9rem] text-white/[0.05] rotate-[-6deg] pointer-events-none"></i>

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
                <p class="text-ink/60 text-sm mt-1.5 mb-5">Get deals, new arrivals and exclusives, straight to your
                    inbox.</p>
                <form class="flex gap-2 flex-wrap justify-center" @submit.prevent="sent = true">
                    <input type="email" x-model="email" placeholder="Your email" required
                        class="flex-1 min-w-[160px] px-5 py-2.5 rounded-full border border-line bg-paper text-sm focus:outline-none focus:ring-2 focus:ring-moss/40 focus:border-transparent search-input">
                    <button type="submit"
                        class="inline-flex items-center gap-2 bg-moss text-white font-bold px-6 py-2.5 rounded-full hover:bg-moss/90 transition-colors text-sm">
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
                    <img src="{{ asset('images/vukashop-logo.svg') }}" alt="VukaShop" class="h-8 w-auto">
                    <p class="text-ink/60 text-sm mt-1.5">Kenya's trusted marketplace.</p>
                </div>
                <div>
                    <h5 class="font-semibold text-sm mb-2">Shop</h5>
                    <ul class="space-y-1.5">
                        <li><a href="#"
                                class="text-ink/60 text-sm hover:text-moss transition-colors">Electronics</a></li>
                        <li><a href="#"
                                class="text-ink/60 text-sm hover:text-moss transition-colors">Fashion</a></li>
                        <li><a href="#" class="text-ink/60 text-sm hover:text-moss transition-colors">Home</a>
                        </li>
                        <li><a href="#" class="text-ink/60 text-sm hover:text-moss transition-colors">Beauty</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-semibold text-sm mb-2">Support</h5>
                    <ul class="space-y-1.5">
                        <li><a href="#"
                                class="text-ink/60 text-sm hover:text-moss transition-colors">Contact</a></li>
                        <li><a href="#"
                                class="text-ink/60 text-sm hover:text-moss transition-colors">Delivery</a></li>
                        <li><a href="#"
                                class="text-ink/60 text-sm hover:text-moss transition-colors">Returns</a></li>
                        <li><a href="#" class="text-ink/60 text-sm hover:text-moss transition-colors">M-Pesa
                                FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-semibold text-sm mb-2">Company</h5>
                    <ul class="space-y-1.5">
                        <li><a href="#" class="text-ink/60 text-sm hover:text-moss transition-colors">About</a>
                        </li>
                        <li><a href="{{ route('vendor.apply') }}"
                                class="text-ink/60 text-sm hover:text-moss transition-colors">Become a Vendor</a></li>
                        <li><a href="#"
                                class="text-ink/60 text-sm hover:text-moss transition-colors">Careers</a></li>
                        <li><a href="#"
                                class="text-ink/60 text-sm hover:text-moss transition-colors">Privacy</a></li>
                        <li><a href="#" class="text-ink/60 text-sm hover:text-moss transition-colors">Terms</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div
                class="mt-7 border-t border-line pt-4 flex flex-wrap items-center justify-between gap-3 text-sm text-muted">
                <span>© 2026 Vuka Shop. All rights reserved.</span>
                <span class="flex gap-4 text-base">
                    <i class="fab fa-twitter hover:text-moss transition-colors cursor-pointer"></i>
                    <i class="fab fa-instagram hover:text-moss transition-colors cursor-pointer"></i>
                    <i class="fab fa-youtube hover:text-moss transition-colors cursor-pointer"></i>
                </span>
            </div>
        </div>
    </footer>

    <!-- BOTTOM APP NAV (mobile / tablet) -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-line flex justify-around px-1"
        style="padding-bottom: calc(6px + env(safe-area-inset-bottom));" aria-label="App navigation">
        <a href="{{ route('home') }}"
            class="flex-1 flex flex-col items-center gap-1 py-2.5 text-[11px] font-semibold text-moss">
            <i class="fas fa-home text-base"></i> Home
        </a>
        <button type="button" @click="menuOpen = true"
            class="flex-1 flex flex-col items-center gap-1 py-2.5 text-[11px] font-semibold text-muted">
            <i class="fas fa-th-large text-base"></i> Categories
        </button>
        <a href="#"
            class="relative flex-1 flex flex-col items-center gap-1 py-2.5 text-[11px] font-semibold text-muted">
            <i class="fas fa-shopping-bag text-base"></i> Cart
            <span x-data x-show="$store.cart.count > 0" x-text="$store.cart.count"
                class="absolute top-1 right-1/3 w-4 h-4 rounded-full bg-gold text-ink text-[10px] font-bold flex items-center justify-center"></span>
        </a>
        @auth
            <a href="{{ route('dashboard') }}"
                class="flex-1 flex flex-col items-center gap-1 py-2.5 text-[11px] font-semibold text-muted">
                <i class="fas fa-user text-base"></i> Account
            </a>
        @else
            <a href="{{ route('login') }}"
                class="flex-1 flex flex-col items-center gap-1 py-2.5 text-[11px] font-semibold text-muted">
                <i class="fas fa-user text-base"></i> Account
            </a>
        @endauth
    </nav>

    @stack('scripts')
</body>

</html>