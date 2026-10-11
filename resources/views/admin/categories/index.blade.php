@php
    // Sidebar icons
    $icons = [
        'home' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        'products' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        'categories' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z',
        'brands' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
        'attributes' => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4',
        'staff' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
        'vendors' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
        'reports' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        'profile' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        'logout' => 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1',
        'menu' => 'M4 6h16M4 12h16M4 18h16',
        'close' => 'M6 18L18 6M6 6l12 12',
    ];

    $navItems = [
        ['label' => 'Home', 'href' => route('admin.dashboard'), 'icon' => 'home'],
        ['label' => 'Products', 'href' => route('admin.products.index'), 'icon' => 'products', 'active' => request()->routeIs('admin.products.*')],
        ['label' => 'Categories', 'href' => route('admin.categories.index'), 'icon' => 'categories', 'active' => request()->routeIs('admin.categories.*')],
        ['label' => 'Brands', 'href' => route('admin.brands.index'), 'icon' => 'brands', 'active' => request()->routeIs('admin.brands.*')],
        ['label' => 'Attributes', 'href' => route('admin.attributes.index'), 'icon' => 'attributes', 'active' => request()->routeIs('admin.attributes.*')],
        ['label' => 'Staff', 'href' => route('admin.staff.index'), 'icon' => 'staff', 'active' => request()->routeIs('admin.staff.*')],
        ['label' => 'Vendors', 'href' => route('admin.vendors.index'), 'icon' => 'vendors', 'active' => request()->routeIs('admin.vendors.*')],
        ['label' => 'Reports', 'href' => '#', 'icon' => 'reports'],
    ];
@endphp

<x-admin-layout>
    <!-- Load Inter font + FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body, .font-inter {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #C7CFC0; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #9DAF92; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.25); }
    </style>

    <!-- Main Container: Sidebar + Content -->
    <div x-data="{ sidebarOpen: true, mobileOpen: false }" 
         class="flex h-screen w-full overflow-hidden bg-[#F7F8F5] font-inter">

        <!-- ============ Mobile Overlay ============ -->
        <div x-show="mobileOpen" 
             x-transition.opacity
             @click="mobileOpen = false"
             class="fixed inset-0 bg-black/50 z-30 lg:hidden"
             x-cloak></div>

        <!-- ============ Sidebar ============ -->
        <aside :class="[
                   sidebarOpen ? 'lg:w-64' : 'lg:w-20',
                   mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
               ]"
               class="fixed lg:relative inset-y-0 left-0 z-40 flex flex-col bg-[#013F4A] transition-all duration-300 ease-in-out shadow-2xl w-64">

            <!-- Sidebar Header: Logo -->
            <div class="flex h-20 items-center justify-between px-4 border-b border-white/10">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-center overflow-hidden flex-1">
                    <img src="{{ asset('images/vukashop-logo.svg') }}" alt="VukaShop Logo" class="h-10 w-auto object-contain">
                </a>
                <!-- Mobile close button -->
                <button @click="mobileOpen = false" class="lg:hidden text-white/60 hover:text-white">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons['close'] }}" />
                    </svg>
                </button>
            </div>

            <!-- Desktop Toggle Button -->
            <button @click="sidebarOpen = !sidebarOpen" 
                    class="hidden lg:flex absolute -right-3 top-24 h-6 w-6 items-center justify-center rounded-full bg-[#336A29] text-white shadow-md hover:bg-[#285022] focus:outline-none transition-transform duration-300 z-50">
                <svg :class="sidebarOpen ? 'rotate-180' : ''" class="h-3 w-3 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <!-- Navigation Links -->
            <nav class="mt-4 flex-1 flex flex-col gap-1 px-3 overflow-y-auto sidebar-scroll">
                @foreach ($navItems as $item)
                    <a href="{{ $item['href'] }}" 
                       @click="mobileOpen = false"
                       class="group relative flex items-center gap-3 rounded-xl px-3 py-3 transition-colors 
                              {{ ($item['active'] ?? false) ? 'bg-[#336A29] text-white shadow-md' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$item['icon']] }}" />
                            </svg>
                        </span>
                        
                        <span :class="sidebarOpen ? 'lg:inline' : 'lg:hidden'" 
                              class="whitespace-nowrap text-sm font-medium inline">
                            {{ $item['label'] }}
                        </span>

                        <!-- Tooltip for collapsed state (desktop only) -->
                        <div x-show="!sidebarOpen" 
                             class="hidden lg:group-hover:block absolute left-14 z-50 rounded-md bg-[#336A29] px-2 py-1 text-xs text-white whitespace-nowrap">
                            {{ $item['label'] }}
                        </div>
                    </a>
                @endforeach
            </nav>

            <!-- ============ User Section (Bottom) ============ -->
            <div class="border-t border-white/10 p-3 space-y-1">

                <!-- Profile Link -->
                <a href="{{ route('profile.edit') }}" 
                   class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors text-slate-300 hover:bg-white/5 hover:text-white">
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons['profile'] }}" />
                        </svg>
                    </span>
                    <span :class="sidebarOpen ? 'lg:inline' : 'lg:hidden'" class="whitespace-nowrap text-sm font-medium inline">
                        Profile
                    </span>
                </a>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault(); this.closest('form').submit();"
                       class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors text-slate-300 hover:bg-white/5 hover:text-red-400">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons['logout'] }}" />
                            </svg>
                        </span>
                        <span :class="sidebarOpen ? 'lg:inline' : 'lg:hidden'" class="whitespace-nowrap text-sm font-medium inline">
                            Log Out
                        </span>
                    </a>
                </form>

                <!-- User Info -->
                <div class="flex items-center gap-3 rounded-xl bg-black/20 p-2 border border-white/5 mt-2">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-[#336A29] to-[#013F4A] text-sm font-bold text-white border border-white/20">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div :class="sidebarOpen ? 'lg:block' : 'lg:hidden'" class="overflow-hidden block">
                        <p class="truncate text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                        <p class="truncate text-xs text-[#EAEF9D]">{{ ucfirst(Auth::user()->user_type) }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- ============ Main Content Area ============ -->
        <div class="flex flex-1 flex-col overflow-hidden min-w-0">

            <!-- Top Header -->
            <header class="flex h-20 items-center justify-between gap-4 border-b border-[#E5E7DE] bg-white px-4 sm:px-8 shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <!-- Mobile hamburger -->
                    <button @click="mobileOpen = true" class="lg:hidden text-gray-600 hover:text-gray-900 p-2">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons['menu'] }}" />
                        </svg>
                    </button>

                    <h2 class="font-semibold text-xl text-gray-800 leading-tight truncate">
                        {{ __('Category Management') }}
                    </h2>
                    <span class="hidden sm:inline text-sm text-[#336A29] font-mono bg-[#EAEF9D]/60 px-2.5 py-1 rounded border border-[#336A29]/20 whitespace-nowrap">
                        {{ $categories->count() }} {{ Str::plural('category', $categories->count()) }}
                    </span>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('admin.categories.create') }}"
                        class="inline-flex items-center gap-2 bg-[#336A29] text-white px-3 sm:px-4 py-2.5 rounded-lg hover:bg-[#285022] transition text-sm font-semibold shadow-sm whitespace-nowrap">
                        <i class="fas fa-plus text-xs"></i> 
                        <span class="hidden sm:inline">New Category</span>
                        <span class="sm:hidden">New</span>
                    </a>
                </div>
            </header>

            <!-- Main Scrollable Area -->
            <main class="flex-1 overflow-y-auto bg-[#F7F8F5] p-4 sm:p-8">
                <div class="max-w-7xl mx-auto">

                    {{-- Flash messages --}}
                    @if (session('success'))
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm flex items-start gap-2">
                            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm flex items-start gap-2">
                            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    @if ($categories->isEmpty())
                        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-[#E5E7DE]">
                            <div class="p-12 sm:p-16 text-center">
                                <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-[#EAEF9D]/60 text-[#336A29] mb-4">
                                    <i class="fas fa-sitemap text-2xl"></i>
                                </div>
                                <p class="text-gray-600 mb-1 font-medium">No categories yet.</p>
                                <p class="text-sm text-gray-400 mb-6">Get started by creating your first category.</p>
                                <a href="{{ route('admin.categories.create') }}"
                                    class="inline-flex items-center gap-2 bg-[#336A29] text-white px-5 py-2.5 rounded-lg hover:bg-[#285022] transition text-sm font-semibold shadow-sm">
                                    <i class="fas fa-plus text-xs"></i>
                                    Create your first category
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="space-y-5">
                            @foreach ($categories as $category)
                                {{-- Top-level card --}}
                                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-[#E5E7DE] hover:shadow-md transition">

                                    {{-- Top-level row --}}
                                    <div class="p-4 sm:p-5 border-b border-gray-100">
                                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">

                                            {{-- Swatch --}}
                                            <div class="shrink-0 w-14 h-14 rounded-xl border border-gray-200 flex items-center justify-center text-white text-lg font-semibold shadow-sm"
                                                style="background: {{ $category->heroBackground() }};">
                                                @if ($category->icon)
                                                    <i class="fas {{ $category->icon }}"></i>
                                                @else
                                                    {{ substr($category->name, 0, 1) }}
                                                @endif
                                            </div>

                                            {{-- Info --}}
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <h3 class="font-semibold text-lg text-gray-900">{{ $category->name }}</h3>
                                                    @if (!$category->is_active)
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 border border-red-200">
                                                            <i class="fas fa-circle text-[6px]"></i> Inactive
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 border border-green-200">
                                                            <i class="fas fa-circle text-[6px]"></i> Active
                                                        </span>
                                                    @endif
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-[#EAEF9D]/60 text-[#336A29] border border-[#336A29]/20">
                                                        <i class="fas fa-palette text-[10px]"></i>
                                                        {{ $category->theme }}
                                                    </span>
                                                </div>
                                                <p class="text-sm text-gray-500 mt-1 flex items-center gap-2 flex-wrap">
                                                    <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded text-gray-700">{{ $category->slug }}</span>
                                                    <span class="text-gray-300">·</span>
                                                    <span>Sort: <span class="font-medium text-gray-700">{{ $category->sort_order }}</span></span>
                                                    <span class="text-gray-300">·</span>
                                                    <span>
                                                        <i class="fas fa-sitemap text-[10px] text-gray-400"></i>
                                                        {{ $category->children->count() }} subcategor{{ $category->children->count() === 1 ? 'y' : 'ies' }}
                                                    </span>
                                                </p>
                                            </div>

                                            {{-- Actions --}}
                                            <div class="flex items-center gap-2 shrink-0">
                                                <a href="{{ route('admin.categories.edit', $category) }}"
                                                    class="inline-flex items-center gap-1.5 text-indigo-600 hover:text-indigo-800 text-sm font-medium px-3 py-2 rounded-lg hover:bg-indigo-50 transition">
                                                    <i class="fas fa-pen text-xs"></i>
                                                    <span class="hidden sm:inline">Edit</span>
                                                </a>
                                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                                    onsubmit="return confirm('Delete this category? Subcategories will be promoted to top-level.')"
                                                    class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="inline-flex items-center gap-1.5 text-red-600 hover:text-red-800 text-sm font-medium px-3 py-2 rounded-lg hover:bg-red-50 transition">
                                                        <i class="fas fa-trash text-xs"></i>
                                                        <span class="hidden sm:inline">Delete</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Children --}}
                                    @if ($category->children->isNotEmpty())
                                        <div class="divide-y divide-gray-100 bg-gray-50/50">
                                            @foreach ($category->children as $child)
                                                <div class="px-4 sm:px-5 py-3 flex items-center gap-3 sm:gap-4 hover:bg-white transition">
                                                    <div class="shrink-0 w-6 sm:w-8 flex justify-end">
                                                        <i class="fas fa-level-up-alt rotate-90 text-gray-300 text-xs"></i>
                                                    </div>

                                                    <div class="shrink-0 w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center text-xs text-white shadow-sm"
                                                        style="background: {{ $child->heroBackground() }};">
                                                        @if ($child->icon)
                                                            <i class="fas {{ $child->icon }}"></i>
                                                        @else
                                                            {{ substr($child->name, 0, 1) }}
                                                        @endif
                                                    </div>

                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-center gap-2 flex-wrap">
                                                            <span class="font-medium text-gray-900">{{ $child->name }}</span>
                                                            @if (!$child->is_active)
                                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 border border-red-200">
                                                                    <i class="fas fa-circle text-[6px]"></i> Inactive
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 border border-green-200">
                                                                    <i class="fas fa-circle text-[6px]"></i> Active
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <p class="text-xs text-gray-500 mt-0.5 flex items-center gap-2 flex-wrap">
                                                            <span class="font-mono bg-gray-100 px-1.5 py-0.5 rounded text-gray-600">{{ $child->slug }}</span>
                                                            <span class="text-gray-300">·</span>
                                                            <span>Sort: <span class="font-medium text-gray-700">{{ $child->sort_order }}</span></span>
                                                        </p>
                                                    </div>

                                                    <div class="flex items-center gap-1 shrink-0">
                                                        <a href="{{ route('admin.categories.edit', $child) }}"
                                                            class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 text-xs font-medium px-2.5 py-1.5 rounded-lg hover:bg-indigo-50 transition">
                                                            <i class="fas fa-pen text-[10px]"></i>
                                                            <span class="hidden sm:inline">Edit</span>
                                                        </a>
                                                        <form method="POST" action="{{ route('admin.categories.destroy', $child) }}"
                                                            onsubmit="return confirm('Delete this subcategory?')"
                                                            class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 text-xs font-medium px-2.5 py-1.5 rounded-lg hover:bg-red-50 transition">
                                                                <i class="fas fa-trash text-[10px]"></i>
                                                                <span class="hidden sm:inline">Delete</span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="px-5 py-4 text-sm text-gray-400 bg-gray-50/50 flex items-center gap-2">
                                            <i class="fas fa-info-circle text-gray-300"></i>
                                            <em>No subcategories yet.</em>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>
            </main>
        </div>
    </div>
</x-admin-layout>