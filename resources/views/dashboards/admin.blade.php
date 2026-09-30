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
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
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
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Orders</p>
                                <p class="text-2xl font-semibold text-gray-900">0</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-lg p-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 3V9m0 3V9m0 3v1m0-3v-1" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Revenue</p>
                                <p class="text-2xl font-semibold text-gray-900">$0.00</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-500 rounded-lg p-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Pending Orders</p>
                                <p class="text-2xl font-semibold text-gray-900">0</p>
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
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
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
                                <p class="text-sm font-medium text-gray-500">Weekend Override</p>
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{ \App\Models\User::whereIn('user_type', ['admin', 'delivery', 'pickup'])->where('is_weekend_override', true)->count() }}
                                    <span class="text-sm font-normal text-gray-500">/
                                        {{ \App\Models\User::whereIn('user_type', ['admin', 'delivery', 'pickup'])->count() }}</span>
                                </p>
                            </div>
                            <div class="flex-shrink-0 bg-purple-500 rounded-lg p-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">Staff with weekend access override enabled</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">📦 Manage Products</h4>
                        <p class="text-sm text-gray-500 mb-4">Add, edit, or remove products from your store.</p>
                        <a href="#"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            Go to Products
                        </a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">👥 Manage Users</h4>
                        <p class="text-sm text-gray-500 mb-4">View and manage all user accounts.</p>
                        <a href="#"
                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                            Go to Users
                        </a>
                    </div>
                </div>

                <!-- Staff Management Card -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition border-2 border-indigo-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-lg font-semibold text-gray-900">👔 Staff</h4>
                            @php
                                $pendingStaff = \App\Models\User::whereIn('user_type', ['admin', 'delivery', 'pickup'])
                                    ->where(function ($q) {
                                        $q->where('is_active', false)->orWhereNull('approved_at');
                                    })
                                    ->count();
                            @endphp
                            @if ($pendingStaff > 0)
                                <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                    {{ $pendingStaff }} pending
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500 mb-4">Manage staff accounts and approvals.</p>
                        <a href="{{ route('admin.staff.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            Manage Staff
                        </a>
                    </div>
                </div>

                <!-- NEW: Vendor Management Card -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition border-2 border-amber-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-lg font-semibold text-gray-900">🏪 Vendors</h4>
                            @php
                                $pendingVendors = \App\Models\VendorApplication::where('status', 'pending')->count();
                            @endphp
                            @if ($pendingVendors > 0)
                                <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                    {{ $pendingVendors }} pending
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500 mb-4">Review applications and manage vendors.</p>
                        <a href="{{ route('admin.vendors.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-700">
                            Manage Vendors
                        </a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">📊 View Reports</h4>
                        <p class="text-sm text-gray-500 mb-4">Analytics and sales reports.</p>
                        <a href="#"
                            class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700">
                            View Reports
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
                                    <!-- Left: Avatar & Info -->
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div
                                            class="w-10 h-10 shrink-0 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                                            {{ substr($staff->name, 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-medium text-gray-900 truncate">{{ $staff->name }}</p>
                                            <p class="text-sm text-gray-500 truncate">{{ $staff->email }}</p>
                                        </div>
                                    </div>

                                    <!-- Right: Badges -->
                                    <div class="flex items-center gap-2 self-end sm:self-auto shrink-0">
                                        <!-- User Type Badge -->
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs font-medium 
                            @if ($staff->isAdmin()) bg-red-100 text-red-800
                            @elseif($staff->isDelivery()) bg-orange-100 text-orange-800
                            @else bg-purple-100 text-purple-800 @endif">
                                            {{ ucfirst($staff->user_type) }}
                                        </span>

                                        <!-- Status Badge -->
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs font-medium 
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