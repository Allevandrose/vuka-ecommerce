<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold">Welcome back, {{ Auth::user()->name }}! 👋</h3>
                            <p class="mt-1 text-sm text-gray-500">Here's what's happening with your store today.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                                Admin
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-lg p-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Users</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\User::count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-lg p-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Products</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\Product::count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-lg p-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 3V9m0 3V9m0 3v1m0-3v-1" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Categories</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\Category::count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-500 rounded-lg p-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Brands</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\Brand::count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Staff Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Staff Members</p>
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{ \App\Models\User::whereIn('user_type', ['admin', 'delivery', 'pickup'])->count() }}
                                </p>
                            </div>
                            <div class="flex-shrink-0 bg-indigo-500 rounded-lg p-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex gap-4 text-sm">
                            <span class="text-gray-600">Active: <span class="font-semibold text-green-600">
                                    {{ \App\Models\User::whereIn('user_type', ['admin', 'delivery', 'pickup'])->where('is_active', true)->count() }}
                                </span></span>
                            <span class="text-gray-600">Pending: <span class="font-semibold text-yellow-600">
                                    {{ \App\Models\User::whereIn('user_type', ['admin', 'delivery', 'pickup'])->where('is_active', false)->count() }}
                                </span></span>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Vendors</p>
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{ \App\Models\User::where('user_type', 'vendor')->count() }}
                                </p>
                            </div>
                            <div class="flex-shrink-0 bg-amber-500 rounded-lg p-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex gap-4 text-sm">
                            <span class="text-gray-600">Active: <span class="font-semibold text-green-600">
                                    {{ \App\Models\User::where('user_type', 'vendor')->where('is_active', true)->count() }}
                                </span></span>
                            <span class="text-gray-600">Pending: <span class="font-semibold text-yellow-600">
                                    {{ \App\Models\User::where('user_type', 'vendor')->where('is_active', false)->count() }}
                                </span></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-4">
                <!-- Products (UPDATED) -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition border-2 border-blue-200">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-base font-semibold text-gray-900">📦 Products</h4>
                            <span class="px-2 py-0.5 text-[10px] font-medium bg-gray-100 text-gray-700 rounded-full">
                                {{ \App\Models\Product::count() }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mb-3">Manage all products.</p>
                        <a href="{{ route('admin.products.index') }}"
                            class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-md hover:bg-blue-700">
                            Manage
                        </a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition border-2 border-emerald-200">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-base font-semibold text-gray-900">📁 Categories</h4>
                            <span class="px-2 py-0.5 text-[10px] font-medium bg-gray-100 text-gray-700 rounded-full">
                                {{ \App\Models\Category::count() }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mb-3">Organize catalog.</p>
                        <a href="{{ route('admin.categories.index') }}"
                            class="inline-flex items-center px-3 py-1.5 bg-emerald-600 text-white text-xs font-semibold rounded-md hover:bg-emerald-700">
                            Manage
                        </a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition border-2 border-purple-200">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-base font-semibold text-gray-900">🏷️ Brands</h4>
                            <span class="px-2 py-0.5 text-[10px] font-medium bg-gray-100 text-gray-700 rounded-full">
                                {{ \App\Models\Brand::count() }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mb-3">Brand partners.</p>
                        <a href="{{ route('admin.brands.index') }}"
                            class="inline-flex items-center px-3 py-1.5 bg-purple-600 text-white text-xs font-semibold rounded-md hover:bg-purple-700">
                            Manage
                        </a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition border-2 border-cyan-200">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-base font-semibold text-gray-900">⚙️ Attributes</h4>
                            <span class="px-2 py-0.5 text-[10px] font-medium bg-gray-100 text-gray-700 rounded-full">
                                {{ \App\Models\Attribute::count() }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mb-3">Product properties & filters.</p>
                        <a href="{{ route('admin.attributes.index') }}"
                            class="inline-flex items-center px-3 py-1.5 bg-cyan-600 text-white text-xs font-semibold rounded-md hover:bg-cyan-700">
                            Manage
                        </a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition border-2 border-indigo-200">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-base font-semibold text-gray-900">👔 Staff</h4>
                            @php
                                $pendingStaff = \App\Models\User::whereIn('user_type', ['admin', 'delivery', 'pickup'])
                                    ->where(function ($q) {
                                        $q->where('is_active', false)->orWhereNull('approved_at');
                                    })
                                    ->count();
                            @endphp
                            @if ($pendingStaff > 0)
                                <span class="px-2 py-0.5 text-[10px] font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                    {{ $pendingStaff }}
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mb-3">Accounts & approvals.</p>
                        <a href="{{ route('admin.staff.index') }}"
                            class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-xs font-semibold rounded-md hover:bg-indigo-700">
                            Manage
                        </a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition border-2 border-amber-200">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-base font-semibold text-gray-900">🏪 Vendors</h4>
                            @php
                                $pendingVendors = \App\Models\VendorApplication::where('status', 'pending')->count();
                            @endphp
                            @if ($pendingVendors > 0)
                                <span class="px-2 py-0.5 text-[10px] font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                    {{ $pendingVendors }}
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mb-3">Applications & shops.</p>
                        <a href="{{ route('admin.vendors.index') }}"
                            class="inline-flex items-center px-3 py-1.5 bg-amber-600 text-white text-xs font-semibold rounded-md hover:bg-amber-700">
                            Manage
                        </a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                    <div class="p-5">
                        <h4 class="text-base font-semibold text-gray-900 mb-2">📊 Reports</h4>
                        <p class="text-xs text-gray-500 mb-3">Analytics & sales.</p>
                        <a href="#"
                            class="inline-flex items-center px-3 py-1.5 bg-gray-600 text-white text-xs font-semibold rounded-md hover:bg-gray-700">
                            View
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Staff Activity -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-900">📋 Recent Staff Activity</h4>
                        <a href="{{ route('admin.staff.index') }}"
                            class="text-sm text-indigo-600 hover:text-indigo-800 shrink-0">View All →</a>
                    </div>

                    @php
                        $recentStaff = \App\Models\User::whereIn('user_type', ['admin', 'delivery', 'pickup'])
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get();
                    @endphp

                    @if ($recentStaff->count() > 0)
                        <div class="divide-y divide-gray-200">
                            @foreach ($recentStaff as $staff)
                                <div class="py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-10 h-10 shrink-0 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                                            {{ substr($staff->name, 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-medium text-gray-900 truncate">{{ $staff->name }}</p>
                                            <p class="text-sm text-gray-500 truncate">{{ $staff->email }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 self-end sm:self-auto shrink-0">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-medium 
                                            @if ($staff->isAdmin()) bg-red-100 text-red-800
                                            @elseif($staff->isDelivery()) bg-orange-100 text-orange-800
                                            @else bg-purple-100 text-purple-800 @endif">
                                            {{ ucfirst($staff->user_type) }}
                                        </span>
                                        <span class="px-2.5 py-1 rounded-full text-xs font-medium 
                                            @if ($staff->is_active && $staff->approved_at) bg-green-100 text-green-800
                                            @elseif($staff->approved_at && !$staff->is_active) bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
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
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">No staff members registered yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>