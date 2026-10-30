@php
    $cartCount = auth()->check() ? auth()->user()->cartCount() : 0;
    $wishlistCount = auth()->check() ? auth()->user()->wishlistCount() : 0;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#14231C">

    <title>{{ $title ?? config('app.name', 'Vuka Shop') }}</title>

    @isset($metaDescription)
        <meta name="description" content="{{ $metaDescription }}">
    @endisset

    <link rel="canonical" href="{{ $canonical ?? url()->current() }}">

    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:site_name" content="{{ config('app.name', 'VukaShop') }}">
    <meta property="og:title" content="{{ $ogTitle ?? ($title ?? config('app.name', 'Vuka Shop')) }}">
    <meta property="og:description" content="{{ $ogDescription ?? ($metaDescription ?? '') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @isset($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
        <meta property="og:image:alt" content="{{ $ogTitle ?? ($title ?? '') }}">
    @endisset

    <meta name="twitter:card" content="{{ isset($ogImage) ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $ogTitle ?? ($title ?? config('app.name', 'Vuka Shop')) }}">
    <meta name="twitter:description" content="{{ $ogDescription ?? ($metaDescription ?? '') }}">
    @isset($ogImage)
        <meta name="twitter:image" content="{{ $ogImage }}">
    @endisset

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

    @stack('seo')

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

        header a:focus-visible,
        header button:focus-visible,
        #mobile-drawer a:focus-visible,
        #mobile-drawer button:focus-visible {
            outline: 2px solid #20503C;
            outline-offset: 2px;
        }

        input[type="text"],
        input[type="email"],
        input[type="search"] {
            outline: none !important;
            box-shadow: none !important;
        }

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
                count: {{ (int) $cartCount }}
            });
            Alpine.store('wishlist', {
                count: {{ (int) $wishlistCount }}
            });
        });
    </script>
</head>

<body class="text-ink pb-24 lg:pb-0 antialiased"
      x-data="{ mobileSearchOpen: false, menuOpen: false, scrolled: false, announcementVisible: true }"
      x-effect="document.body.classList.toggle('overflow-hidden', menuOpen || mobileSearchOpen)"
      @scroll.window.passive="scrolled = window.scrollY > 8"
      @resize.window="if (window.innerWidth >= 1024) { menuOpen = false; mobileSearchOpen = false }"
      @keydown.escape.window="menuOpen = false; mobileSearchOpen = false">

    {{-- ANNOUNCEMENT BAR --}}
    <div x-data="{ show: true }" x-show="show" x-cloak class="bg-moss text-white text-xs sm:text-[13px]">
        <div class="max-w-7xl mx-auto px-4 py-2 flex items-center justify-center gap-2 relative">
            <i class="fas fa-truck text-gold text-[11px]"></i>
            <p class="text-center font-medium pr-6 sm:pr-0">Free delivery over KSh 5,000. Pay with M-Pesa or card.</p>
            <button @click="show = false" aria-label="Dismiss announcement"
                class="absolute right-3 top-1/2 -translate-y-1/2 h-6 w-6 rounded-full flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 transition-colors">
                <i class="fas fa-times text-[11px]"></i>
            </button>
        </div>
    </div>

    {{-- HEADER --}}
    <header class="sticky top-0 z-30 bg-paper/90 backdrop-blur-md border-b border-line transition-shadow duration-200"
            :class="scrolled ? 'shadow-[0_6px_20px_-12px_rgba(20,35,28,0.35)]' : ''">
        <div class="max-w-7xl mx-auto px-4 h-16 flex items-center gap-3 lg:gap-6">

            <button @click="menuOpen = true"
                class="lg:hidden h-10 w-10 shrink-0 rounded-full bg-white border border-line flex items-center justify-center text-ink shadow-sm hover:bg-line/50 transition-colors"
                aria-label="Open menu" :aria-expanded="menuOpen.toString()" aria-controls="mobile-drawer">
                <i class="fas fa-bars text-sm"></i>
            </button>

            <a href="{{ route('home') }}" class="shrink-0 max-lg:mx-auto lg:mx-0" aria-label="VukaShop home">
                <img src="{{ asset('images/vukashop-logo.svg') }}" alt="VukaShop" class="h-9 sm:h-10 w-auto">
            </a>

            <form action="{{ route('search') }}" method="GET" role="search" x-data="{ q: '', focused: false }"
                @keydown.slash.window="if (!['INPUT','TEXTAREA','SELECT'].includes(document.activeElement.tagName) && window.innerWidth >= 1024) { $event.preventDefault(); $refs.q.focus(); }"
                class="hidden lg:flex flex-1 max-w-xl items-center gap-2 rounded-full pl-5 pr-1.5 h-11 transition-all duration-200"
                :class="focused
                    ?
                    'bg-white shadow-[0_10px_30px_-10px_rgba(32,80,60,0.35)]' :
                    'bg-ink/[0.05] hover:bg-ink/[0.08]'">

                <input x-ref="q" x-model="q" @focus="focused = true" @blur="focused = false" type="text"
                    name="q" placeholder="Search products, brands…" autocomplete="off" enterkeyhint="search"
                    aria-label="Search products"
                    value="{{ request()->routeIs('search') ? request('q') : '' }}"
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

            <nav class="hidden lg:flex items-center gap-5 text-[14px] font-medium text-ink/85 ml-auto"
                aria-label="Primary">
                @php
                    $navCategories = \App\Models\Category::active()->topLevel()->ordered()->limit(4)->get();
                @endphp
                @foreach ($navCategories as $navCat)
                    <a href="{{ route('category.show', $navCat->slug) }}"
                       class="hover:text-moss transition-colors {{ request()->routeIs('category.show') && request()->route('category')?->id === $navCat->id ? 'text-moss' : '' }}">
                        {{ $navCat->name }}
                    </a>
                @endforeach
            </nav>

            <div class="hidden lg:flex items-center gap-3">
                <div class="flex items-center gap-1 text-lg text-ink/70">
                    <a href="{{ route('wishlist.index') }}"
                        class="relative h-10 w-10 rounded-full flex items-center justify-center hover:text-red-500 hover:bg-white transition-colors"
                        aria-label="Wishlist">
                        <i class="far fa-heart"></i>
                        <span x-show="$store.wishlist.count > 0" x-text="$store.wishlist.count"
                            class="absolute top-0.5 right-0.5 bg-red-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center"></span>
                    </a>
                    <a href="{{ route('cart.index') }}"
                        class="relative h-10 w-10 rounded-full flex items-center justify-center hover:text-gold hover:bg-white transition-colors"
                        aria-label="Cart">
                        <i class="fas fa-shopping-bag"></i>
                        <span x-show="$store.cart.count > 0" x-text="$store.cart.count"
                            class="absolute top-0.5 right-0.5 bg-gold text-ink text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center"></span>
                    </a>
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
                            <a href="{{ route('cart.index') }}"
                                class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-paper transition-colors">
                                <i class="fas fa-shopping-bag text-moss w-4 text-center"></i> My Cart
                            </a>
                            <a href="{{ route('wishlist.index') }}"
                                class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-paper transition-colors">
                                <i class="far fa-heart text-red-500 w-4 text-center"></i> My Wishlist
                                <span x-show="$store.wishlist.count > 0" x-text="$store.wishlist.count"
                                    class="ml-auto bg-red-500 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center"></span>
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

            <div class="lg:hidden flex items-center gap-2 shrink-0">
                <button @click="mobileSearchOpen = true"
                    class="h-10 w-10 rounded-full bg-white border border-line flex items-center justify-center text-ink shadow-sm hover:bg-line/50 transition-colors"
                    aria-label="Search">
                    <i class="fas fa-search text-sm"></i>
                </button>
                <a href="{{ route('wishlist.index') }}"
                    class="relative h-10 w-10 rounded-full bg-white border border-line flex items-center justify-center text-ink shadow-sm hover:bg-line/50 transition-colors"
                    aria-label="Wishlist">
                    <i class="far fa-heart text-sm"></i>
                    <span x-show="$store.wishlist.count > 0" x-text="$store.wishlist.count"
                        class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center"></span>
                </a>
                <a href="{{ route('cart.index') }}"
                    class="relative h-10 w-10 rounded-full bg-white border border-line flex items-center justify-center text-ink shadow-sm hover:bg-line/50 transition-colors"
                    aria-label="Cart">
                    <i class="fas fa-shopping-bag text-sm"></i>
                    <span x-show="$store.cart.count > 0" x-text="$store.cart.count"
                        class="absolute -top-1 -right-1 bg-gold text-ink text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center"></span>
                </a>
            </div>
        </div>
    </header>

    {{-- MOBILE DRAWER --}}
    <div class="lg:hidden" x-show="menuOpen" x-cloak>
        <div x-show="menuOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="menuOpen = false"
            class="fixed inset-0 z-50 bg-ink/50 backdrop-blur-sm" aria-hidden="true"></div>

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

                <form action="{{ route('search') }}" method="GET" role="search" x-data="{ q: '', focused: false }"
                    class="flex items-center gap-2 rounded-full pl-5 pr-1.5 h-11 transition-all duration-200"
                    :class="focused
                        ?
                        'bg-white shadow-[0_10px_30px_-10px_rgba(32,80,60,0.35)]' :
                        'bg-ink/[0.05] hover:bg-ink/[0.08]'">

                    <input x-model="q" @focus="focused = true" @blur="focused = false" type="text"
                        name="q" placeholder="Search products, brands…" autocomplete="off"
                        enterkeyhint="search" aria-label="Search products"
                        class="bg-transparent border-0 outline-none ring-0 focus:ring-0 focus:outline-none w-full text-sm placeholder:text-muted/70 search-input">

                    <button type="button" x-show="q.length > 0" x-cloak
                        @click="q = ''; $el.previousElementSibling.focus()" aria-label="Clear search"
                        class="h-7 w-7 shrink-0 rounded-full flex items-center justify-center text-muted hover:text-ink hover:bg-ink/5 transition-colors">
                        <i class="fas fa-times text-xs"></i>
                    </button>

                    <button type="submit" aria-label="Search"
                        class="h-8 w-8 shrink-0 rounded-full bg-gold text-ink flex items-center justify-center hover:bg-gold/90 active:scale-95 transition-all">
                        <i class="fas fa-search text-xs"></i>
                    </button>
                </form>

                <nav class="space-y-1" aria-label="Quick links">
                    <a href="{{ route('home') }}" @click="menuOpen = false"
                        class="flex items-center gap-3 px-3 py-3 rounded-xl text-[15px] font-semibold text-gold hover:bg-white transition-colors">
                        <i class="fas fa-bolt w-5 text-center"></i> Deals
                    </a>
                    <a href="{{ route('cart.index') }}" @click="menuOpen = false"
                        class="flex items-center gap-3 px-3 py-3 rounded-xl text-[15px] font-medium hover:bg-white transition-colors">
                        <i class="fas fa-shopping-bag w-5 text-center text-moss"></i>
                        My Cart
                        <span x-show="$store.cart.count > 0" x-text="$store.cart.count"
                            class="ml-auto bg-gold text-ink text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center"></span>
                    </a>
                    <a href="{{ route('wishlist.index') }}" @click="menuOpen = false"
                        class="flex items-center gap-3 px-3 py-3 rounded-xl text-[15px] font-medium hover:bg-white transition-colors">
                        <i class="far fa-heart w-5 text-center text-red-500"></i>
                        My Wishlist
                        <span x-show="$store.wishlist.count > 0" x-text="$store.wishlist.count"
                            class="ml-auto bg-red-500 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center"></span>
                    </a>
                    <a href="#" @click="menuOpen = false"
                        class="flex items-center gap-3 px-3 py-3 rounded-xl text-[15px] font-medium hover:bg-white transition-colors">
                        <i class="fas fa-truck-fast w-5 text-center text-moss"></i> Track Order
                    </a>
                    <a href="#" @click="menuOpen = false"
                        class="flex items-center gap-3 px-3 py-3 rounded-xl text-[15px] font-medium hover:bg-white transition-colors">
                        <i class="fas fa-headset w-5 text-center text-moss"></i> Support
                    </a>
                </nav>

                @if ($navCategories->isNotEmpty())
                    <div x-data="{ open: true }">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between px-3 mb-2 text-sm font-semibold text-muted"
                            :aria-expanded="open.toString()">
                            Categories
                            <i class="fas fa-chevron-down text-[10px] transition-transform"
                                :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-collapse.duration.200ms class="space-y-1">
                            @foreach ($navCategories as $category)
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

    {{-- MOBILE SEARCH OVERLAY --}}
    <div x-show="mobileSearchOpen" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" @click.self="mobileSearchOpen = false" x-init="$watch('mobileSearchOpen', v => v && $nextTick(() => $refs.mobileSearchInput.focus()))"
        class="lg:hidden fixed inset-0 z-50 bg-ink/40 backdrop-blur-sm flex items-start justify-center pt-20 px-4"
        role="dialog" aria-modal="true" aria-label="Search">
        <div x-show="mobileSearchOpen" x-transition:enter="transition ease-out duration-200 delay-75"
            x-transition:enter-start="opacity-0 -translate-y-3" x-transition:enter-end="opacity-100 translate-y-0"
            class="bg-white rounded-2xl p-4 w-full max-w-md shadow-2xl" x-data="{ q: '', focused: false }">

            <form action="{{ route('search') }}" method="GET" role="search" @submit="mobileSearchOpen = false">
                <div class="flex items-center gap-2 rounded-full pl-5 pr-1.5 h-11 transition-all duration-200"
                    :class="focused
                        ?
                        'bg-white shadow-[0_10px_30px_-10px_rgba(32,80,60,0.35)]' :
                        'bg-ink/[0.05] hover:bg-ink/[0.08]'">
                    <input x-ref="mobileSearchInput" x-model="q" @focus="focused = true" @blur="focused = false"
                        type="text" name="q" placeholder="Search products, brands…" autocomplete="off"
                        enterkeyhint="search" aria-label="Search products"
                        class="bg-transparent border-0 outline-none ring-0 focus:ring-0 focus:outline-none w-full text-sm placeholder:text-muted/70 search-input">
                    <button type="button" x-show="q.length > 0" x-cloak
                        @click="q = ''; $refs.mobileSearchInput.focus()"
                        class="h-7 w-7 shrink-0 rounded-full flex items-center justify-center text-muted hover:text-ink hover:bg-ink/5 transition-colors"
                        aria-label="Clear search">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                    <button type="submit" aria-label="Search"
                        class="h-8 w-8 shrink-0 rounded-full bg-gold text-ink flex items-center justify-center hover:bg-gold/90 active:scale-95 transition-all">
                        <i class="fas fa-search text-xs"></i>
                    </button>
                </div>
            </form>

            <button @click="mobileSearchOpen = false"
                class="mt-3 w-full py-2.5 rounded-full bg-paper text-ink/70 font-semibold text-sm hover:bg-line transition-colors">
                <i class="fas fa-times mr-1"></i> Cancel
            </button>
        </div>
    </div>

    {{-- PAGE CONTENT --}}
    <main class="max-w-7xl mx-auto px-4">
        {{ $slot }}
    </main>

    {{-- FOOTER --}}
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
                        @foreach ($navCategories->take(4) as $navCat)
                            <li><a href="{{ route('category.show', $navCat->slug) }}"
                                    class="text-ink/60 text-sm hover:text-moss transition-colors">{{ $navCat->name }}</a>
                            </li>
                        @endforeach
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
                        <li><a href="#" class="text-ink/60 text-sm hover:text-moss transition-colors">Terms</a></li>
                    </ul>
                </div>
            </div>
            <div
                class="mt-7 border-t border-line pt-4 flex flex-wrap items-center justify-between gap-3 text-sm text-muted">
                <span>© {{ date('Y') }} Vuka Shop. All rights reserved.</span>
                <span class="flex gap-4 text-base">
                    <i class="fab fa-twitter hover:text-moss transition-colors cursor-pointer"></i>
                    <i class="fab fa-instagram hover:text-moss transition-colors cursor-pointer"></i>
                    <i class="fab fa-youtube hover:text-moss transition-colors cursor-pointer"></i>
                </span>
            </div>
        </div>
    </footer>

    {{-- BOTTOM MOBILE NAV --}}
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-line flex justify-around px-1"
        style="padding-bottom: calc(6px + env(safe-area-inset-bottom));" aria-label="App navigation">
        <a href="{{ route('home') }}"
            class="flex-1 flex flex-col items-center gap-1 py-2.5 text-[11px] font-semibold transition-colors
                   {{ request()->routeIs('home') ? 'text-moss' : 'text-muted' }}">
            <i class="fas fa-home text-base"></i> Home
        </a>
        <button type="button" @click="menuOpen = true"
            class="flex-1 flex flex-col items-center gap-1 py-2.5 text-[11px] font-semibold text-muted">
            <i class="fas fa-th-large text-base"></i> Categories
        </button>
        <a href="{{ route('wishlist.index') }}"
            class="relative flex-1 flex flex-col items-center gap-1 py-2.5 text-[11px] font-semibold transition-colors
                   {{ request()->routeIs('wishlist.*') ? 'text-red-500' : 'text-muted' }}">
            <i class="far fa-heart text-base"></i> Wishlist
            <span x-data x-show="$store.wishlist.count > 0" x-text="$store.wishlist.count"
                class="absolute top-1 right-1/3 w-4 h-4 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center"></span>
        </a>
        <a href="{{ route('cart.index') }}"
            class="relative flex-1 flex flex-col items-center gap-1 py-2.5 text-[11px] font-semibold transition-colors
                   {{ request()->routeIs('cart.*') ? 'text-moss' : 'text-muted' }}">
            <i class="fas fa-shopping-bag text-base"></i> Cart
            <span x-data x-show="$store.cart.count > 0" x-text="$store.cart.count"
                class="absolute top-1 right-1/3 w-4 h-4 rounded-full bg-gold text-ink text-[10px] font-bold flex items-center justify-center"></span>
        </a>
        @auth
            <a href="{{ route('dashboard') }}"
                class="flex-1 flex flex-col items-center gap-1 py-2.5 text-[11px] font-semibold transition-colors
                       {{ request()->routeIs('*.dashboard') ? 'text-moss' : 'text-muted' }}">
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
