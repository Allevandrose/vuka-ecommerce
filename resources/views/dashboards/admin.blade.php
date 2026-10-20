@php
    use App\Models\{User, Product, Category, Brand, Attribute, VendorApplication};

    $staffTypes = ['admin', 'delivery', 'pickup'];

    $counts = [
        'users' => User::count(),
        'products' => Product::count(),
        'categories' => Category::count(),
        'brands' => Brand::count(),
        'attributes' => Attribute::count(),
    ];

    // Calculate Pending Products for the Dashboard Tile
    $pendingProducts = Product::where('status', 'pending_review')->count();

    $staffTotal = User::whereIn('user_type', $staffTypes)->count();
    $staffActive = User::whereIn('user_type', $staffTypes)->where('is_active', true)->count();
    $staffInactive = User::whereIn('user_type', $staffTypes)->where('is_active', false)->count();
    $pendingStaff = User::whereIn('user_type', $staffTypes)
        ->where(fn($q) => $q->where('is_active', false)->orWhereNull('approved_at'))
        ->count();

    $vendorTotal = User::where('user_type', 'vendor')->count();
    $vendorActive = User::where('user_type', 'vendor')->where('is_active', true)->count();
    $vendorInactive = User::where('user_type', 'vendor')->where('is_active', false)->count();
    $pendingVendors = VendorApplication::where('status', 'pending')->count();

    $recentStaff = User::whereIn('user_type', $staffTypes)->orderBy('created_at', 'desc')->limit(5)->get();
    $recentVendors = User::where('user_type', 'vendor')->orderBy('created_at', 'desc')->limit(5)->get();

    $avatarColors = ['bg-[#068562]', 'bg-[#0a9e75]', 'bg-[#1fd3a3]', 'bg-[#013F4A]', 'bg-[#0b5e52]'];

    $icons = [
        'home' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        'products' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        'categories' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z',
        'brands' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
        'attributes' => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4',
        'staff' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
        'vendors' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
        'reports' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        'search' => 'M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z',
        'menu' => 'M4 6h16M4 12h16M4 18h16',
        'close' => 'M6 18L18 6M6 6l12 12',
        'profile' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        'logout' => 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1',
    ];

    // Sidebar Navigation Items
    $navItems = [
        ['label' => 'Home', 'href' => '#', 'icon' => 'home', 'active' => true],
        ['label' => 'Products', 'href' => route('admin.products.index'), 'icon' => 'products'],
        ['label' => 'Categories', 'href' => route('admin.categories.index'), 'icon' => 'categories'],
        ['label' => 'Brands', 'href' => route('admin.brands.index'), 'icon' => 'brands'],
        ['label' => 'Attributes', 'href' => route('admin.attributes.index'), 'icon' => 'attributes'],
        ['label' => 'Staff', 'href' => route('admin.staff.index'), 'icon' => 'staff'],
        ['label' => 'Vendors', 'href' => route('admin.vendors.index'), 'icon' => 'vendors'],
        ['label' => 'Reports', 'href' => '#', 'icon' => 'reports'],
    ];

    // Tiles
    $tiles = [
        // Note: The 'count' key here is used by the default tile template, 
        // but we will override the Products tile HTML below to use the new design.
        ['name' => 'Products', 'sub' => 'manage your catalog', 'href' => route('admin.products.index'), 'icon' => 'products', 'count' => $counts['products'], 'bg' => 'bg-[#068562] hover:bg-[#0a9e75]'],
        ['name' => 'Categories', 'sub' => 'organize products', 'href' => route('admin.categories.index'), 'icon' => 'categories', 'count' => $counts['categories'], 'bg' => 'bg-[#013F4A] hover:bg-[#024e5c]'],
        ['name' => 'Brands', 'sub' => 'brand partners', 'href' => route('admin.brands.index'), 'icon' => 'brands', 'count' => $counts['brands'], 'bg' => 'bg-[#013F4A] hover:bg-[#024e5c]'],
        ['name' => 'Attributes', 'sub' => 'properties & filters', 'href' => route('admin.attributes.index'), 'icon' => 'attributes', 'count' => $counts['attributes'], 'bg' => 'bg-[#013F4A] hover:bg-[#024e5c]'],
        ['name' => 'Staff', 'sub' => 'accounts & approvals', 'href' => route('admin.staff.index'), 'icon' => 'staff', 'count' => $pendingStaff ?: null, 'bg' => 'bg-[#013F4A] hover:bg-[#024e5c]'],
        ['name' => 'Vendors', 'sub' => 'applications & shops', 'href' => route('admin.vendors.index'), 'icon' => 'vendors', 'count' => $pendingVendors ?: null, 'bg' => 'bg-[#013F4A] hover:bg-[#024e5c]'],
    ];
@endphp

<x-admin-layout>
    <!-- Main Container: Full Height/Width, Sidebar State -->
    <div x-data="{ sidebarOpen: true, q: '' }" 
         class="flex h-screen w-full overflow-hidden bg-[#013F4A] text-slate-100 font-sans selection:bg-[#068562] selection:text-white">

        <!-- ============ Expandable Sidebar ============ -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'" 
               class="relative z-20 flex flex-col border-r border-white/10 bg-[#013F4A] transition-all duration-300 ease-in-out shadow-2xl">
            
            <!-- Sidebar Header: Logo Only -->
            <div class="flex h-20 items-center justify-center px-4">
                <a href="{{ route('dashboard') }}" class="flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('images/vukashop-logo.svg') }}" alt="VukaShop Logo" class="h-10 w-auto object-contain">
                </a>
            </div>

            <!-- Sidebar Toggle Button -->
            <button @click="sidebarOpen = !sidebarOpen" 
                    class="absolute -right-3 top-24 flex h-6 w-6 items-center justify-center rounded-full bg-[#068562] text-white shadow-md hover:bg-[#0a9e75] focus:outline-none transition-transform duration-300 z-30">
                <svg :class="sidebarOpen ? 'rotate-180' : ''" class="h-3 w-3 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <!-- Navigation Links -->
            <nav class="mt-6 flex flex-1 flex-col gap-2 px-3 overflow-y-auto overflow-x-hidden">
                @foreach ($navItems as $item)
                    <a href="{{ $item['href'] }}" 
                       class="group flex items-center gap-3 rounded-xl px-3 py-3 transition-colors 
                              {{ ($item['active'] ?? false) ? 'bg-[#068562] text-white shadow-md' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$item['icon']] }}" />
                            </svg>
                        </span>
                        
                        <span x-show="sidebarOpen" x-transition.opacity.duration.200ms class="whitespace-nowrap text-sm font-medium">
                            {{ $item['label'] }}
                        </span>

                        <!-- Tooltip for collapsed state -->
                        <div x-show="!sidebarOpen" class="absolute left-14 z-50 hidden rounded-md bg-[#068562] px-2 py-1 text-xs text-white group-hover:block">
                            {{ $item['label'] }}
                        </div>
                    </a>
                @endforeach
            </nav>

            <!-- ============ User Actions & Info (Bottom Sidebar) ============ -->
            <div class="border-t border-white/10 p-4 space-y-2">
                
                <!-- Independent Profile & Logout Icons -->
                <div class="flex flex-col gap-1">
                    <!-- Profile Link -->
                    <a href="{{ route('profile.edit') }}" 
                       class="group flex items-center gap-3 rounded-xl px-3 py-3 transition-colors text-slate-300 hover:bg-white/5 hover:text-white">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons['profile'] }}" />
                            </svg>
                        </span>
                        <span x-show="sidebarOpen" x-transition.opacity.duration.200ms class="whitespace-nowrap text-sm font-medium">
                            Profile
                        </span>
                        <div x-show="!sidebarOpen" class="absolute left-14 z-50 hidden rounded-md bg-[#068562] px-2 py-1 text-xs text-white group-hover:block">
                            Profile
                        </div>
                    </a>

                    <!-- Logout Link -->
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <a href="{{ route('logout') }}"
                           onclick="event.preventDefault(); this.closest('form').submit();"
                           class="group flex items-center gap-3 rounded-xl px-3 py-3 transition-colors text-slate-300 hover:bg-white/5 hover:text-red-400">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons['logout'] }}" />
                                </svg>
                            </span>
                            <span x-show="sidebarOpen" x-transition.opacity.duration.200ms class="whitespace-nowrap text-sm font-medium">
                                Log Out
                            </span>
                            <div x-show="!sidebarOpen" class="absolute left-14 z-50 hidden rounded-md bg-[#068562] px-2 py-1 text-xs text-white group-hover:block">
                                Log Out
                            </div>
                        </a>
                    </form>
                </div>

                <!-- User Info (Read-only display) -->
                <div class="flex items-center gap-3 rounded-xl bg-black/10 p-2 border border-white/5">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-[#068562] to-[#013F4A] text-sm font-bold text-white border border-white/20">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div x-show="sidebarOpen" x-transition.opacity.duration.200ms class="overflow-hidden">
                        <p class="truncate text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                        <p class="truncate text-xs text-[#1fd3a3]">{{ ucfirst(Auth::user()->user_type) }}</p>
                    </div>
                </div>

            </div>
        </aside>

        <!-- ============ Main Content Area (No Top Navigation) ============ -->
        <div class="flex flex-1 flex-col overflow-hidden bg-[#068562]"> 
            
            <!-- Main Content Scrollable -->
            <main class="flex-1 overflow-y-auto p-6 sm:p-8">
                
                <!-- Top Header inside Main Content (Replaces Navigation Bar) -->
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <h1 class="text-xl font-semibold text-white">Ibrahim Mulei</h1>
                        <span class="rounded-full bg-[#013F4A] px-3 py-1 text-xs font-medium text-[#1fd3a3] border border-[#1fd3a3]/30">Admin</span>
                    </div>
                    
                    <!-- Search -->
                    <label class="relative hidden w-64 md:block">
                        <span class="sr-only">Search</span>
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-white/60" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons['search'] }}" />
                        </svg>
                        <input type="text" x-model="q" placeholder="Search..." 
                               class="w-full rounded-xl border-0 bg-black/10 py-2.5 pl-9 pr-3 text-sm text-white placeholder-white/60 focus:bg-black/20 focus:ring-2 focus:ring-[#1fd3a3]" />
                    </label>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,400px)_1fr]">
                    
                    <!-- Left Column: Action Tiles -->
                    <section class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            @foreach ($tiles as $tile)
                                @if ($tile['name'] === 'Products')
                                    <!-- Custom Products Tile with Pending Count Logic -->
                                    <div x-show="!q || '{{ strtolower($tile['name'] . ' ' . $tile['sub']) }}'.includes(q.toLowerCase())"
                                         class="group relative flex aspect-square flex-col justify-between rounded-2xl p-5 text-white shadow-lg transition transform hover:-translate-y-1 {{ $tile['bg'] }}">
                                        
                                        <div class="flex items-start justify-between mb-2">
                                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 backdrop-blur-sm">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$tile['icon']] }}" />
                                                </svg>
                                            </span>
                                            
                                            @if ($pendingProducts > 0)
                                                <span class="px-2 py-0.5 text-[10px] font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                                    {{ $pendingProducts }} pending
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 text-[10px] font-medium bg-white/20 text-white rounded-full backdrop-blur-sm">
                                                    {{ $counts['products'] }}
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <div class="mt-auto">
                                            <p class="text-sm font-semibold">{{ $tile['name'] }}</p>
                                            <p class="mt-0.5 text-xs text-white/70 group-hover:text-white/90">{{ $tile['sub'] }}</p>
                                            
                                            <div class="flex flex-col gap-1.5 mt-3">
                                                <a href="{{ $tile['href'] }}"
                                                    class="inline-flex items-center justify-center px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-md hover:bg-blue-700 transition">
                                                    Manage
                                                </a>
                                                @if ($pendingProducts > 0)
                                                    <a href="{{ route('admin.products.pending') }}"
                                                        class="inline-flex items-center justify-center px-3 py-1.5 bg-yellow-500 text-white text-xs font-semibold rounded-md hover:bg-yellow-600 transition">
                                                        Review Queue
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <!-- Default Tile Template -->
                                    <a href="{{ $tile['href'] }}"
                                       x-show="!q || '{{ strtolower($tile['name'] . ' ' . $tile['sub']) }}'.includes(q.toLowerCase())"
                                       class="group relative flex aspect-square flex-col justify-between rounded-2xl p-5 text-white shadow-lg transition transform hover:-translate-y-1 {{ $tile['bg'] }}">
                                        <div class="flex items-start justify-between">
                                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 backdrop-blur-sm">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$tile['icon']] }}" />
                                                </svg>
                                            </span>
                                            @if ($tile['count'])
                                                <span class="rounded-full bg-white/20 px-2 py-0.5 text-xs font-semibold backdrop-blur-sm">
                                                    {{ $tile['count'] }}
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold">{{ $tile['name'] }}</p>
                                            <p class="mt-0.5 text-xs text-white/70 group-hover:text-white/90">{{ $tile['sub'] }}</p>
                                        </div>
                                    </a>
                                @endif
                            @endforeach

                            <!-- Reports Tile (Wide) -->
                            <a href="#"
                               x-show="!q || 'reports analytics and sales'.includes(q.toLowerCase())"
                               class="col-span-2 flex items-center gap-4 rounded-2xl bg-[#013F4A] p-5 text-white shadow-lg transition hover:bg-[#024e5c]">
                                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/5">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons['reports'] }}" />
                                    </svg>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold">Reports</p>
                                    <p class="mt-0.5 text-xs text-slate-400">analytics and sales</p>
                                </div>
                            </a>
                        </div>
                    </section>

                    <!-- Right Column: Stats & Lists -->
                    <section class="min-w-0 space-y-6">

                        <!-- Hero Clock Widget -->
                        <div x-data="{
                                now: new Date(),
                                init() { setInterval(() => this.now = new Date(), 1000) },
                                get time() { return this.now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false }) },
                                get date() { return this.now.toLocaleDateString('en-GB', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) }
                            }"
                            class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#013F4A] via-[#025e5e] to-[#068562] p-6 sm:p-8 shadow-xl border border-white/5">
                            
                            <div class="relative z-10">
                                <p class="text-6xl font-semibold tracking-tight text-white sm:text-7xl" x-text="time">
                                    {{ now()->format('H:i') }}
                                </p>
                                <p class="mt-3 text-sm text-[#1fd3a3] font-medium" x-text="date">{{ now()->format('l, j F Y') }}</p>
                                <p class="mt-6 text-sm text-white/80">
                                    Welcome back, <span class="font-medium text-white">{{ Auth::user()->name }}</span>. Here's your store today.
                                </p>
                            </div>

                            <!-- Decorative Graphic -->
                            <svg class="pointer-events-none absolute -bottom-4 right-0 h-48 w-48 opacity-20 sm:opacity-100 sm:right-8 sm:h-56 sm:w-56" viewBox="0 0 200 220" fill="none" aria-hidden="true">
                                <path d="M100 180C96 120 70 80 40 60c-4 40 14 90 60 120z" fill="#1fd3a3" />
                                <path d="M100 180C110 110 135 60 170 30c8 50-16 110-70 150z" fill="#068562" />
                                <path d="M100 180C98 130 104 90 118 50c14 40 6 90-18 130z" fill="#013F4A" />
                                <path d="M62 170h76l-8 42H70z" fill="#d8dee3" />
                                <rect x="56" y="160" width="88" height="14" rx="4" fill="#eef1f4" />
                            </svg>
                        </div>

                        <!-- Mini Stats Grid -->
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                            @foreach ([['Users', $counts['users']], ['Products', $counts['products']], ['Categories', $counts['categories']], ['Brands', $counts['brands']]] as [$label, $value])
                                <div class="rounded-2xl bg-[#013F4A] px-5 py-4 border border-white/5 shadow-lg">
                                    <p class="text-xs text-[#1fd3a3]">{{ $label }}</p>
                                    <p class="mt-1 text-2xl font-semibold text-white">{{ number_format($value) }}</p>
                                </div>
                            @endforeach
                        </div>

                        <!-- Staff Card -->
                        <div class="rounded-2xl bg-[#013F4A] p-6 border border-white/5 shadow-lg">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h2 class="text-base font-semibold text-white">Staff members</h2>
                                    <p class="mt-1 text-xs text-slate-400">
                                        {{ $staffTotal }} total
                                        @if ($pendingStaff > 0)
                                            <span class="text-[#1fd3a3]">· {{ $pendingStaff }} waiting for approval</span>
                                        @endif
                                    </p>
                                </div>
                                <a href="{{ route('admin.staff.index') }}" title="All staff"
                                   class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/5 text-slate-400 hover:text-white hover:bg-white/10 transition">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M4 10a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zm7.5 0a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM17 11.5a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" /></svg>
                                </a>
                            </div>

                            <div class="mt-5 flex flex-wrap items-center justify-between gap-4">
                                <div class="flex items-center">
                                    <div class="flex -space-x-2">
                                        @foreach ($recentStaff->take(3) as $i => $s)
                                            <span class="flex h-9 w-9 items-center justify-center rounded-lg text-sm font-bold text-[#013F4A] ring-2 ring-[#013F4A] {{ $avatarColors[$i % count($avatarColors)] }}"
                                                  title="{{ $s->name }}">{{ strtoupper(substr($s->name, 0, 1)) }}</span>
                                        @endforeach
                                        @if ($staffTotal > 3)
                                            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#068562] text-xs font-semibold text-white ring-2 ring-[#013F4A]">+{{ $staffTotal - 3 }}</span>
                                        @endif
                                    </div>
                                    <div class="ml-4 flex gap-4 text-xs">
                                        <span class="text-slate-400">Active <span class="font-semibold text-[#1fd3a3]">{{ $staffActive }}</span></span>
                                        <span class="text-slate-400">Inactive <span class="font-semibold text-yellow-400">{{ $staffInactive }}</span></span>
                                    </div>
                                </div>
                                <a href="{{ route('admin.staff.index') }}"
                                   class="rounded-lg bg-[#068562] px-5 py-2 text-xs font-semibold text-white transition hover:bg-[#0a9e75]">
                                    Manage staff
                                </a>
                            </div>
                        </div>

                        <!-- Vendors Card -->
                        <div class="rounded-2xl bg-[#013F4A] p-6 border border-white/5 shadow-lg">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h2 class="text-base font-semibold text-white">Vendors</h2>
                                    <p class="mt-1 text-xs text-slate-400">
                                        {{ $vendorTotal }} total
                                        @if ($pendingVendors > 0)
                                            <span class="text-[#1fd3a3]">· {{ $pendingVendors }} pending applications</span>
                                        @endif
                                    </p>
                                </div>
                                <a href="{{ route('admin.vendors.index') }}" title="All vendors"
                                   class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/5 text-slate-400 hover:text-white hover:bg-white/10 transition">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M4 10a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zm7.5 0a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM17 11.5a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" /></svg>
                                </a>
                            </div>

                            <div class="mt-5 flex flex-wrap items-center justify-between gap-4">
                                <div class="flex items-center">
                                    <div class="flex -space-x-2">
                                        @foreach ($recentVendors->take(3) as $i => $v)
                                            <span class="flex h-9 w-9 items-center justify-center rounded-lg text-sm font-bold text-[#013F4A] ring-2 ring-[#013F4A] {{ $avatarColors[($i + 2) % count($avatarColors)] }}"
                                                  title="{{ $v->name }}">{{ strtoupper(substr($v->name, 0, 1)) }}</span>
                                        @endforeach
                                        @if ($vendorTotal > 3)
                                            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#068562] text-xs font-semibold text-white ring-2 ring-[#013F4A]">+{{ $vendorTotal - 3 }}</span>
                                        @endif
                                    </div>
                                    <div class="ml-4 flex gap-4 text-xs">
                                        <span class="text-slate-400">Active <span class="font-semibold text-[#1fd3a3]">{{ $vendorActive }}</span></span>
                                        <span class="text-slate-400">Inactive <span class="font-semibold text-yellow-400">{{ $vendorInactive }}</span></span>
                                    </div>
                                </div>
                                <a href="{{ route('admin.vendors.index') }}"
                                   class="rounded-lg bg-[#068562] px-5 py-2 text-xs font-semibold text-white transition hover:bg-[#0a9e75]">
                                    {{ $pendingVendors > 0 ? 'Review applications' : 'Manage vendors' }}
                                </a>
                            </div>
                        </div>

                        <!-- Recent Staff List -->
                        <div class="rounded-2xl bg-[#013F4A] p-6 border border-white/5 shadow-lg">
                            <div class="mb-3 flex items-center justify-between">
                                <h2 class="text-base font-semibold text-white">Recent staff</h2>
                                <a href="{{ route('admin.staff.index') }}" class="text-xs font-medium text-[#1fd3a3] hover:text-white transition">View all</a>
                            </div>

                            @forelse ($recentStaff as $i => $staff)
                                <div class="flex flex-col gap-3 border-t border-white/5 py-3 first:border-t-0 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex min-w-0 items-center gap-3">
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-sm font-bold text-[#013F4A] {{ $avatarColors[$i % count($avatarColors)] }}">
                                            {{ strtoupper(substr($staff->name, 0, 1)) }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-medium text-white">{{ $staff->name }}</p>
                                            <p class="truncate text-xs text-slate-400">{{ $staff->email }}</p>
                                        </div>
                                    </div>
                                    <div class="flex shrink-0 items-center gap-2 self-end sm:self-auto">
                                        <span class="rounded-full px-2.5 py-1 text-xs font-medium
                                            @if ($staff->isAdmin()) bg-red-500/15 text-red-300
                                            @elseif($staff->isDelivery()) bg-orange-500/15 text-orange-300
                                            @else bg-purple-500/15 text-purple-300 @endif">
                                            {{ ucfirst($staff->user_type) }}
                                        </span>
                                        <span class="rounded-full px-2.5 py-1 text-xs font-medium
                                            @if ($staff->is_active && $staff->approved_at) bg-[#068562]/20 text-[#1fd3a3]
                                            @elseif($staff->approved_at && !$staff->is_active) bg-red-500/15 text-red-300
                                            @else bg-yellow-500/15 text-yellow-300 @endif">
                                            @if ($staff->is_active && $staff->approved_at)
                                                Active
                                            @elseif($staff->approved_at && !$staff->is_active)
                                                Inactive
                                            @else
                                                Pending
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-slate-400">No staff members yet. Staff accounts will appear here once they register.</p>
                            @endforelse
                        </div>

                    </section>
                </div>
            </main>
        </div>
    </div>
</x-admin-layout>