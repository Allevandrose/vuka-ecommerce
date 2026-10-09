{{-- Public shop layout — reusable chrome for category, product, search pages --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="theme-color" content="#14231C">

    <title>{{ $title ?? config('app.name', 'Vuka Shop') }}</title>

    @isset($metaDescription)
        <meta name="description" content="{{ $metaDescription }}">
    @endisset

    {{-- Canonical --}}
    <link rel="canonical" href="{{ $canonical ?? url()->current() }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:site_name" content="{{ config('app.name', 'VukaShop') }}">
    <meta property="og:title" content="{{ $ogTitle ?? ($title ?? config('app.name', 'Vuka Shop')) }}">
    <meta property="og:description" content="{{ $ogDescription ?? ($metaDescription ?? '') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @isset($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
        <meta property="og:image:alt" content="{{ $ogTitle ?? ($title ?? '') }}">
    @endisset

    {{-- Twitter --}}
    <meta name="twitter:card" content="{{ isset($ogImage) ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $ogTitle ?? ($title ?? config('app.name', 'Vuka Shop')) }}">
    <meta name="twitter:description" content="{{ $ogDescription ?? ($metaDescription ?? '') }}">
    @isset($ogImage)
        <meta name="twitter:image" content="{{ $ogImage }}">
    @endisset

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

    {{-- Page-specific SEO (JSON-LD, etc.) --}}
    @stack('seo')

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
        });
    </script>
</head>
<body class="text-ink pb-24 sm:pb-0 antialiased" x-data="{ mobileSearchOpen: false }">

    {{-- HEADER --}}
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

            <form action="{{ route('search') }}" method="GET" class="hidden sm:flex items-center gap-2.5 bg-white border border-line rounded-full px-4 py-2.5 flex-1 max-w-sm mx-3">
                <i class="fas fa-search text-muted text-sm"></i>
                <input type="text" name="q" value="{{ request()->routeIs('search') ? request('q') : '' }}" placeholder="Search products, brands…" class="bg-transparent outline-none w-full text-sm placeholder:text-muted/70">
            </form>

            <nav class="hidden sm:flex items-center gap-7 text-[15px] font-medium text-ink/85">
                @php
                    $navCategories = \App\Models\Category::active()->topLevel()->ordered()->limit(4)->get();
                @endphp
                @foreach ($navCategories as $navCat)
                    <a href="{{ route('category.show', $navCat->slug) }}" class="hover:text-moss transition-colors">
                        {{ $navCat->name }}
                    </a>
                @endforeach
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

    {{-- MOBILE SEARCH OVERLAY --}}
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
            <form action="{{ route('search') }}" method="GET" @submit="mobileSearchOpen = false">
                <div class="flex items-center gap-3 bg-paper rounded-full px-4 py-3">
                    <i class="fas fa-search text-muted"></i>
                    <input x-ref="mobileSearchInput" type="text" name="q" placeholder="Search products, brands…" class="bg-transparent outline-none w-full text-base">
                    <button type="submit" class="shrink-0 text-moss font-semibold text-sm">Go</button>
                </div>
            </form>
            <button @click="mobileSearchOpen = false" class="mt-3 w-full py-2.5 rounded-full bg-paper text-ink/70 font-semibold text-sm hover:bg-line transition-colors">
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
                    <h5 class="font-serif text-lg font-semibold">Vuka<span class="text-moss">Shop</span></h5>
                    <p class="text-ink/60 text-sm mt-1.5">Kenya's trusted marketplace.</p>
                </div>
                <div>
                    <h5 class="font-semibold text-sm mb-2">Shop</h5>
                    <ul class="space-y-1.5">
                        @foreach ($navCategories->take(4) as $navCat)
                            <li><a href="{{ route('category.show', $navCat->slug) }}" class="text-ink/60 text-sm hover:text-moss transition-colors">{{ $navCat->name }}</a></li>
                        @endforeach
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