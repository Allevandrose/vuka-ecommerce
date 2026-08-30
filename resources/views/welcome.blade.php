{{-- resources/views/home.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Vuka Shop') }} – Kenyan eCommerce</title>

    <!-- Fonts: Fraunces for display, Instrument Sans for body -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=fraunces:500,600,700|instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Tailwind via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <style>
        .font-display { font-family: 'Fraunces', serif; font-optical-sizing: auto; }
    </style>
</head>
<body class="bg-white text-[#1E1C19] font-sans antialiased min-h-screen flex flex-col">

    {{-- ========== TOP STRIP ========== --}}
    <div class="w-full bg-[#1E3C2C] text-[#E9E3D3] text-xs font-medium">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-9 flex items-center justify-between">
            <span>🇰🇪 Nationwide delivery to all 47 counties</span>
            <span class="hidden sm:inline">Pay with M-Pesa · Cash on delivery available</span>
        </div>
    </div>

    {{-- ========== HEADER / NAV ========== --}}
    <header class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex items-center justify-between">
        {{-- Logo --}}
        <div class="flex items-center gap-2">
            <a href="{{ route('home') }}" class="font-display text-2xl font-semibold tracking-tight text-[#1E3C2C]">
                Vuka<span class="text-[#C5282C]">Shop</span>
            </a>
        </div>

        {{-- Nav links --}}
        <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-[#3E3A34]">
            <a href="#" class="hover:text-[#C5282C] transition">Electronics</a>
            <a href="#" class="hover:text-[#C5282C] transition">Fashion</a>
            <a href="#" class="hover:text-[#C5282C] transition">Home & Living</a>
            <a href="#" class="hover:text-[#C5282C] transition">Health & Beauty</a>
            <a href="#" class="hover:text-[#C5282C] transition">Deals</a>
        </nav>

        {{-- Right actions --}}
        <div class="flex items-center gap-3">
            @auth
                <a href="{{ route('dashboard') }}" class="text-sm font-medium text-[#1E3C2C] hover:text-[#C5282C] transition">
                    Dashboard
                </a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-[#C5282C] hover:text-[#A02024] transition">
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-sm font-medium text-[#1E3C2C] hover:text-[#C5282C] transition">
                    Log in
                </a>
                <a href="{{ route('register') }}" class="bg-[#1E3C2C] text-white text-sm font-medium px-5 py-2.5 rounded-full shadow-sm hover:bg-[#143023] transition">
                    Register
                </a>
            @endauth
        </div>
    </header>

    {{-- ========== MAIN HERO ========== --}}
    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center py-8 lg:py-14">
            {{-- Left content --}}
            <div class="space-y-6 order-2 lg:order-1">
                <div class="inline-flex items-center gap-2 bg-[#F5EFE4] text-[#1E3C2C] px-3 py-1.5 rounded-full text-xs font-semibold tracking-wide">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#C5282C]"></span>
                    Kenya's trusted marketplace
                </div>
                <h1 class="font-display text-5xl sm:text-6xl lg:text-[4.25rem] font-semibold leading-[1.05] tracking-tight text-[#1E1C19]">
                    Shop everything you need,
                    <span class="italic text-[#1E3C2C]">delivered to your door.</span>
                </h1>
                <p class="text-base sm:text-lg text-[#5E5850] max-w-md leading-relaxed">
                    From electronics and fashion to home essentials and beauty — Kenya's one-stop online marketplace with M-Pesa payments and nationwide delivery.
                </p>
                <div class="flex flex-wrap items-center gap-4 pt-2">
                    <a href="#" class="bg-[#C5282C] hover:bg-[#A02024] text-white font-medium px-7 py-3.5 rounded-full shadow-md transition flex items-center gap-2">
                        <span>Start Shopping</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <a href="#" class="flex items-center gap-3 text-[#1E3C2C] font-medium hover:text-[#C5282C] transition">
                        <span class="w-11 h-11 rounded-full bg-[#F5F0EB] flex items-center justify-center">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        </span>
                        Watch video
                    </a>
                </div>
                {{-- Trust badges --}}
                <div class="flex flex-wrap items-center gap-x-6 gap-y-2 pt-4 text-xs text-[#5E5850] border-t border-[#EEE9E1]">
                    <span class="flex items-center gap-1.5 pt-4"><span class="font-bold text-[#1E3C2C]">✓</span> M-Pesa &amp; card</span>
                    <span class="flex items-center gap-1.5 pt-4"><span class="font-bold text-[#1E3C2C]">✓</span> County delivery</span>
                    <span class="flex items-center gap-1.5 pt-4"><span class="font-bold text-[#1E3C2C]">✓</span> 24/7 support</span>
                    <span class="flex items-center gap-1.5 pt-4"><span class="font-bold text-[#1E3C2C]">✓</span> 100% genuine</span>
                </div>
            </div>

            {{-- Right visual: hero photo with floating category chips --}}
            <div class="relative order-1 lg:order-2">
                <div class="aspect-[4/5] sm:aspect-[5/4] rounded-[2rem] overflow-hidden shadow-xl">
                    <img
                        src="https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?auto=format&fit=crop&w=1000&q=80"
                        alt="Online shopping concept"
                        class="w-full h-full object-cover"
                        loading="lazy">
                </div>
                <div class="absolute -bottom-6 -left-4 sm:left-6 bg-white rounded-2xl shadow-lg px-5 py-4 flex items-center gap-3 border border-[#EEE9E1]">
                    <span class="text-2xl">🚚</span>
                    <div>
                        <p class="text-sm font-semibold text-[#1E1C19]">Free delivery</p>
                        <p class="text-xs text-[#8C847A]">Orders over KSh 5,000</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========== CATEGORY GRID ========== --}}
        <div class="border-t border-[#EEE9E1] pt-10 pb-2">
            <h2 class="font-display text-2xl font-semibold text-[#1E1C19] mb-6">Shop by category</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                @php
                    $categories = [
                        ['name' => 'Electronics', 'count' => '200+ items', 'icon' => '📱'],
                        ['name' => 'Fashion', 'count' => '500+ items', 'icon' => '👕'],
                        ['name' => 'Home & Living', 'count' => '300+ items', 'icon' => '🏠'],
                        ['name' => 'Health & Beauty', 'count' => '150+ items', 'icon' => '💄'],
                        ['name' => 'Books & Media', 'count' => '100+ items', 'icon' => '📚'],
                        ['name' => 'Sports & Outdoors', 'count' => '80+ items', 'icon' => '⚽'],
                    ];
                @endphp
                @foreach($categories as $cat)
                <a href="#" class="group bg-[#F7F4EF] rounded-2xl p-6 text-center hover:bg-[#1E3C2C] transition duration-300">
                    <div class="text-4xl mb-2 group-hover:scale-110 transition">{{ $cat['icon'] }}</div>
                    <p class="text-sm font-semibold text-[#1E1C19] group-hover:text-white transition">{{ $cat['name'] }}</p>
                    <p class="text-xs text-[#8C847A] group-hover:text-white/70 transition">{{ $cat['count'] }}</p>
                </a>
                @endforeach
            </div>
        </div>

        {{-- ========== WHY SHOP WITH US ========== --}}
        <div class="mt-14 border-t border-[#EEE9E1] pt-10">
            <h2 class="font-display text-2xl font-semibold text-[#1E1C19] text-center mb-10">Why shop with VukaShop?</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @php
                    $benefits = [
                        ['icon' => '🇰🇪', 'title' => 'Local Marketplace', 'desc' => '100% Kenyan-owned, supporting local businesses'],
                        ['icon' => '🔒', 'title' => 'Secure Payments', 'desc' => 'M-Pesa, credit cards, and cash on delivery'],
                        ['icon' => '🚚', 'title' => 'Nationwide Delivery', 'desc' => 'We deliver to all 47 counties in Kenya'],
                        ['icon' => '💬', 'title' => '24/7 Support', 'desc' => 'Get help anytime via phone, chat, or email'],
                    ];
                @endphp
                @foreach($benefits as $benefit)
                <div class="bg-white rounded-2xl p-6 text-center border border-[#EEE9E1] hover:shadow-lg transition">
                    <div class="text-5xl mb-3">{{ $benefit['icon'] }}</div>
                    <h3 class="font-semibold text-[#1E1C19]">{{ $benefit['title'] }}</h3>
                    <p class="text-sm text-[#5E5850] mt-1">{{ $benefit['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ========== FEATURED COLLECTIONS ========== --}}
        <div class="mt-14 border-t border-[#EEE9E1] pt-10">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-display text-2xl font-semibold text-[#1E1C19]">Featured collections</h2>
                <a href="#" class="text-sm font-medium text-[#C5282C] hover:underline">View all →</a>
            </div>

            <div class="grid sm:grid-cols-3 gap-5">
                @php
                    $featured = [
                        ['name' => 'Electronics Deals', 'badge' => 'Shop now', 'img' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=700&q=80'],
                        ['name' => 'Fashion Trends', 'badge' => 'Explore', 'img' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?auto=format&fit=crop&w=700&q=80'],
                        ['name' => 'Home Essentials', 'badge' => 'Discover', 'img' => 'https://images.unsplash.com/photo-1586023492125-27b868e9e602?auto=format&fit=crop&w=700&q=80'],
                    ];
                @endphp
                @foreach($featured as $item)
                <a href="#" class="group relative rounded-2xl overflow-hidden aspect-[4/3] block shadow-sm">
                    <img src="{{ $item['img'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/5 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-5">
                        <span class="text-xs font-semibold uppercase tracking-wide text-white/80">{{ $item['badge'] }}</span>
                        <h4 class="font-display text-xl font-semibold text-white">{{ $item['name'] }}</h4>
                    </div>
                </a>
                @endforeach
            </div>
        </div>

        {{-- ========== PRODUCT CARDS GRID ========== --}}
        <div class="mt-14 border-t border-[#EEE9E1] pt-10">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-display text-2xl font-semibold text-[#1E1C19]">Popular right now</h2>
                <a href="#" class="text-sm font-medium text-[#C5282C] hover:underline">View all →</a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5 sm:gap-6">
                @php
                    $products = [
                        ['name' => 'MacBook Air M2', 'price' => 'From KSh 149,900', 'tag' => 'Laptop', 'img' => 'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?auto=format&fit=crop&w=600&q=80'],
                        ['name' => 'Sony WH-1000XM5', 'price' => 'KSh 42,500', 'tag' => 'Headphones', 'img' => 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?auto=format&fit=crop&w=600&q=80'],
                        ['name' => 'Canon EOS Camera', 'price' => 'KSh 89,000', 'tag' => 'Camera', 'img' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=600&q=80'],
                        ['name' => 'AirPods Pro (2nd Gen)', 'price' => 'KSh 34,900', 'tag' => 'Bluetooth', 'img' => 'https://images.unsplash.com/photo-1600294037681-c80b4cb5b434?auto=format&fit=crop&w=600&q=80'],
                        ['name' => 'iPad Air', 'price' => 'KSh 62,000', 'tag' => 'Tablet', 'img' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?auto=format&fit=crop&w=600&q=80'],
                        ['name' => 'Galaxy Watch 6', 'price' => 'KSh 38,500', 'tag' => 'Smartwatch', 'img' => 'https://images.unsplash.com/photo-1579586337278-3befd40fd17a?auto=format&fit=crop&w=600&q=80'],
                        ['name' => 'Bose SoundLink', 'price' => 'KSh 27,900', 'tag' => 'Speaker', 'img' => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?auto=format&fit=crop&w=600&q=80'],
                        ['name' => 'iPhone 15 Pro', 'price' => 'KSh 185,000', 'tag' => 'Smartphone', 'img' => 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?auto=format&fit=crop&w=600&q=80'],
                    ];
                @endphp
                @foreach($products as $product)
                <div class="bg-white rounded-2xl border border-[#EEE9E1] overflow-hidden hover:shadow-lg transition group">
                    <div class="aspect-square overflow-hidden bg-[#F7F4EF]">
                        <img src="{{ $product['img'] }}" alt="{{ $product['name'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" loading="lazy">
                    </div>
                    <div class="p-4">
                        <span class="text-[10px] font-bold uppercase tracking-wide text-[#C5282C]">{{ $product['tag'] }}</span>
                        <h3 class="font-semibold text-sm sm:text-base text-[#1E1C19] leading-tight mt-0.5">{{ $product['name'] }}</h3>
                        <p class="text-sm font-medium text-[#5E5850] mt-1">{{ $product['price'] }}</p>
                        <button class="mt-3 w-full bg-[#1E3C2C] text-white text-xs font-medium py-2.5 rounded-full hover:bg-[#143023] transition">Add to cart</button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ========== M-PESA / DELIVERY BANNER ========== --}}
        <div class="mt-16 mb-4 bg-[#1E3C2C] rounded-3xl p-8 sm:p-10 text-white flex flex-col sm:flex-row items-center justify-between gap-6 overflow-hidden relative">
            <div class="flex items-center gap-4 relative z-10">
                <span class="text-4xl">🇰🇪</span>
                <div>
                    <h3 class="font-display text-2xl font-semibold">Pay with M-Pesa</h3>
                    <p class="text-sm text-[#C5D6C8] mt-1">Free delivery to all counties. Cash on delivery available.</p>
                </div>
            </div>
            <a href="#" class="bg-white text-[#1E3C2C] font-bold px-6 py-3 rounded-full shadow-md hover:bg-[#F5F0EB] transition text-sm relative z-10 whitespace-nowrap">Shop now →</a>
        </div>

        {{-- ========== NEWSLETTER SECTION ========== --}}
        <div class="mt-8 mb-4 bg-[#F5F0EB] rounded-3xl p-8 sm:p-10">
            <div class="text-center max-w-2xl mx-auto">
                <h3 class="font-display text-2xl font-semibold text-[#1E1C19]">Stay in the know</h3>
                <p class="text-[#5E5850] mt-2">Subscribe to get the latest deals, new arrivals, and exclusive offers.</p>
                <form class="mt-6 flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                    <input type="email" placeholder="Enter your email" class="flex-1 px-4 py-3 rounded-full border border-[#E5E0D8] focus:outline-none focus:ring-2 focus:ring-[#C5282C] focus:border-transparent">
                    <button type="submit" class="bg-[#C5282C] hover:bg-[#A02024] text-white font-medium px-6 py-3 rounded-full transition">Subscribe</button>
                </form>
            </div>
        </div>
    </main>

    {{-- ========== FOOTER ========== --}}
    <footer class="bg-[#1E1C19] text-[#B0A89B] mt-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-2 md:grid-cols-4 gap-8">
            <div>
                <span class="font-display text-xl font-semibold text-white">Vuka<span class="text-[#C5282C]">Shop</span></span>
                <p class="text-sm mt-3 max-w-xs leading-relaxed">Kenya's trusted online marketplace. Quality products, fair prices, nationwide delivery.</p>
                <div class="flex gap-3 mt-4 text-xl">
                    <a href="#" class="hover:text-white transition">📱</a>
                    <a href="#" class="hover:text-white transition">🐦</a>
                    <a href="#" class="hover:text-white transition">📸</a>
                    <a href="#" class="hover:text-white transition">▶️</a>
                </div>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-3 text-sm">Shop</h4>
                <ul class="text-sm space-y-2">
                    <li><a href="#" class="hover:text-white transition">Electronics</a></li>
                    <li><a href="#" class="hover:text-white transition">Fashion</a></li>
                    <li><a href="#" class="hover:text-white transition">Home & Living</a></li>
                    <li><a href="#" class="hover:text-white transition">Health & Beauty</a></li>
                    <li><a href="#" class="hover:text-white transition">Sports & Outdoors</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-3 text-sm">Customer Service</h4>
                <ul class="text-sm space-y-2">
                    <li><a href="#" class="hover:text-white transition">Contact Us</a></li>
                    <li><a href="#" class="hover:text-white transition">Delivery Information</a></li>
                    <li><a href="#" class="hover:text-white transition">Returns Policy</a></li>
                    <li><a href="#" class="hover:text-white transition">M-Pesa FAQ</a></li>
                    <li><a href="#" class="hover:text-white transition">Track Order</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-3 text-sm">Company</h4>
                <ul class="text-sm space-y-2">
                    <li><a href="#" class="hover:text-white transition">About Us</a></li>
                    <li><a href="#" class="hover:text-white transition">Careers</a></li>
                    <li><a href="#" class="hover:text-white transition">Privacy Policy</a></li>
                    <li><a href="#" class="hover:text-white transition">Terms & Conditions</a></li>
                </ul>
                <p class="text-xs mt-6 text-[#5E5850]">© 2026 Vuka Shop. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>