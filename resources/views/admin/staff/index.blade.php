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

    <div x-data="{ sidebarOpen: true, mobileOpen: false }" 
         class="flex h-screen w-full overflow-hidden bg-[#F7F8F5] font-inter">

        {{-- Mobile Overlay --}}
        <div x-show="mobileOpen" x-transition.opacity @click="mobileOpen = false"
             class="fixed inset-0 bg-black/50 z-30 lg:hidden" x-cloak></div>

        {{-- Sidebar --}}
        <aside :class="[
                   sidebarOpen ? 'lg:w-64' : 'lg:w-20',
                   mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
               ]"
               class="fixed lg:relative inset-y-0 left-0 z-40 flex flex-col bg-[#013F4A] transition-all duration-300 ease-in-out shadow-2xl w-64">

            <div class="flex h-20 items-center justify-between px-4 border-b border-white/10">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-center overflow-hidden flex-1">
                    <img src="{{ asset('images/vukashop-logo.svg') }}" alt="VukaShop Logo" class="h-10 w-auto object-contain">
                </a>
                <button @click="mobileOpen = false" class="lg:hidden text-white/60 hover:text-white">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons['close'] }}" />
                    </svg>
                </button>
            </div>

            <button @click="sidebarOpen = !sidebarOpen" 
                    class="hidden lg:flex absolute -right-3 top-24 h-6 w-6 items-center justify-center rounded-full bg-[#336A29] text-white shadow-md hover:bg-[#285022] focus:outline-none transition-transform duration-300 z-50">
                <svg :class="sidebarOpen ? 'rotate-180' : ''" class="h-3 w-3 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <nav class="mt-4 flex-1 flex flex-col gap-1 px-3 overflow-y-auto sidebar-scroll">
                @foreach ($navItems as $item)
                    <a href="{{ $item['href'] }}" @click="mobileOpen = false"
                       class="group relative flex items-center gap-3 rounded-xl px-3 py-3 transition-colors 
                              {{ ($item['active'] ?? false) ? 'bg-[#336A29] text-white shadow-md' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$item['icon']] }}" />
                            </svg>
                        </span>
                        <span :class="sidebarOpen ? 'lg:inline' : 'lg:hidden'" class="whitespace-nowrap text-sm font-medium inline">
                            {{ $item['label'] }}
                        </span>
                        <div x-show="!sidebarOpen" class="hidden lg:group-hover:block absolute left-14 z-50 rounded-md bg-[#336A29] px-2 py-1 text-xs text-white whitespace-nowrap">
                            {{ $item['label'] }}
                        </div>
                    </a>
                @endforeach
            </nav>

            <div class="border-t border-white/10 p-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors text-slate-300 hover:bg-white/5 hover:text-white">
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons['profile'] }}" />
                        </svg>
                    </span>
                    <span :class="sidebarOpen ? 'lg:inline' : 'lg:hidden'" class="whitespace-nowrap text-sm font-medium inline">Profile</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"
                       class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors text-slate-300 hover:bg-white/5 hover:text-red-400">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons['logout'] }}" />
                            </svg>
                        </span>
                        <span :class="sidebarOpen ? 'lg:inline' : 'lg:hidden'" class="whitespace-nowrap text-sm font-medium inline">Log Out</span>
                    </a>
                </form>
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

        {{-- Main Content --}}
        <div class="flex flex-1 flex-col overflow-hidden min-w-0">
            <header class="flex h-20 items-center justify-between gap-4 border-b border-[#E5E7DE] bg-white px-4 sm:px-8 shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <button @click="mobileOpen = true" class="lg:hidden text-gray-600 hover:text-gray-900 p-2">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons['menu'] }}" />
                        </svg>
                    </button>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight truncate">
                        {{ __('Staff Management') }}
                    </h2>
                    <span class="hidden sm:inline text-sm text-[#336A29] font-mono bg-[#EAEF9D]/60 px-2.5 py-1 rounded border border-[#336A29]/20 whitespace-nowrap">
                        {{ $staff->count() }} {{ Str::plural('member', $staff->count()) }}
                    </span>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('admin.staff.create') }}"
                        class="inline-flex items-center gap-2 bg-[#336A29] text-white px-3 sm:px-4 py-2.5 rounded-lg hover:bg-[#285022] transition text-sm font-semibold shadow-sm whitespace-nowrap">
                        <i class="fas fa-user-plus text-xs"></i>
                        <span class="hidden sm:inline">Send Invitation</span>
                        <span class="sm:hidden">Invite</span>
                    </a>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto bg-[#F7F8F5] p-4 sm:p-8">
                <div class="max-w-7xl mx-auto">

                    @if(session('success'))
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm flex items-start gap-2">
                            <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm flex items-start gap-2">
                            <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-[#E5E7DE]">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-100 bg-gray-50">
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Weekend</th>
                                        <th class="text-right py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($staff as $member)
                                    <tr class="hover:bg-gray-50 transition">
                                        {{-- Name --}}
                                        <td class="py-3 px-4">
                                            <div class="flex items-center gap-3">
                                                <div class="shrink-0 h-9 w-9 rounded-lg bg-gradient-to-br from-[#336A29] to-[#013F4A] flex items-center justify-center text-white text-sm font-semibold">
                                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                                </div>
                                                <div class="font-medium text-gray-900">{{ $member->name }}</div>
                                            </div>
                                        </td>

                                        {{-- Email --}}
                                        <td class="py-3 px-4 text-sm text-gray-600">{{ $member->email }}</td>

                                        {{-- Role --}}
                                        <td class="py-3 px-4">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium border
                                                @if($member->isAdmin()) bg-red-50 text-red-700 border-red-200
                                                @elseif($member->isDelivery()) bg-orange-50 text-orange-700 border-orange-200
                                                @else bg-violet-50 text-violet-700 border-violet-200
                                                @endif">
                                                <i class="fas @if($member->isAdmin()) fa-shield-alt @elseif($member->isDelivery()) fa-truck @else fa-user @endif text-[9px]"></i>
                                                {{ ucfirst($member->user_type) }}
                                            </span>
                                        </td>

                                        {{-- Status --}}
                                        <td class="py-3 px-4">
                                            @if($member->is_active && $member->approved_at)
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200 w-fit">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Active
                                                </span>
                                            @elseif($member->approved_at && !$member->is_active)
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200 w-fit">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Inactive
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200 w-fit">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> Pending
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Weekend --}}
                                        <td class="py-3 px-4">
                                            @if($member->is_weekend_override)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200 w-fit">
                                                    <i class="fas fa-calendar-check text-[9px]"></i> Override
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200 w-fit">
                                                    Default
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Actions --}}
                                        <td class="py-3 px-4 text-right">
                                            <div class="flex items-center justify-end gap-1 flex-wrap">
                                                @if(!$member->is_active || !$member->approved_at)
                                                    <form method="POST" action="{{ route('admin.staff.activate', $member) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" title="Activate"
                                                            class="inline-flex items-center gap-1 text-green-600 hover:text-green-800 text-xs font-medium px-2.5 py-1.5 rounded-lg hover:bg-green-50 transition">
                                                            <i class="fas fa-check-circle text-xs"></i>
                                                            <span class="hidden sm:inline">Activate</span>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('admin.staff.deactivate', $member) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" title="Deactivate"
                                                            class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 text-xs font-medium px-2.5 py-1.5 rounded-lg hover:bg-red-50 transition">
                                                            <i class="fas fa-ban text-xs"></i>
                                                            <span class="hidden sm:inline">Deactivate</span>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form method="POST" action="{{ route('admin.staff.toggle-weekend-override', $member) }}" class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        title="{{ $member->is_weekend_override ? 'Remove Override' : 'Override Weekend' }}"
                                                        class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-xs font-medium px-2.5 py-1.5 rounded-lg hover:bg-blue-50 transition">
                                                        <i class="fas fa-calendar-alt text-xs"></i>
                                                        <span class="hidden sm:inline">{{ $member->is_weekend_override ? 'Remove' : 'Override' }}</span>
                                                    </button>
                                                </form>

                                                <a href="{{ route('admin.staff.edit', $member) }}" title="Edit"
                                                    class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 text-xs font-medium px-2.5 py-1.5 rounded-lg hover:bg-indigo-50 transition">
                                                    <i class="fas fa-pen text-xs"></i>
                                                    <span class="hidden sm:inline">Edit</span>
                                                </a>

                                                <form method="POST" action="{{ route('admin.staff.destroy', $member) }}" 
                                                      onsubmit="return confirm('Delete this staff member?')" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" title="Delete"
                                                        class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 text-xs font-medium px-2.5 py-1.5 rounded-lg hover:bg-red-50 transition">
                                                        <i class="fas fa-trash text-xs"></i>
                                                        <span class="hidden sm:inline">Delete</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-16">
                                            <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-[#EAEF9D]/60 text-[#336A29] mb-4">
                                                <i class="fas fa-users text-2xl"></i>
                                            </div>
                                            <p class="text-gray-600 mb-1 font-medium">No staff members found.</p>
                                            <p class="text-sm text-gray-400 mb-6">Send an invitation to get started.</p>
                                            <a href="{{ route('admin.staff.create') }}"
                                                class="inline-flex items-center gap-2 bg-[#336A29] text-white px-5 py-2.5 rounded-lg hover:bg-[#285022] transition text-sm font-semibold shadow-sm">
                                                <i class="fas fa-user-plus text-xs"></i> Send your first invitation
                                            </a>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>
</x-admin-layout>