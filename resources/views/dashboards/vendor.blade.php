<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Vendor Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Welcome --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">
                                Welcome back, {{ Auth::user()->name }}! 👋
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                @if (Auth::user()->is_active && Auth::user()->approved_at)
                                    Your vendor account is active.
                                    @php $totalProducts = Auth::user()->products()->count(); @endphp
                                    You have <strong class="text-gray-900">{{ $totalProducts }}</strong>
                                    {{ Str::plural('product', $totalProducts) }} in your catalog.
                                @else
                                    Your vendor account is pending activation.
                                @endif
                            </p>
                        </div>
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                            Vendor
                        </span>
                    </div>
                </div>
            </div>

            {{-- Quick stats --}}
            @php
                $vendor = Auth::user();
                $stats = [
                    'active' => $vendor->products()->where('status', 'active')->count(),
                    'pending' => $vendor->products()->where('status', 'pending_review')->count(),
                    'rejected' => $vendor->products()->where('status', 'rejected')->count(),
                    'draft' => $vendor->products()->where('status', 'draft')->count(),
                ];
                $cap = $vendor->productLimit();
                $used = $vendor->productCount();
                $canAdd = $vendor->canCreateProduct();
            @endphp

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Active</p>
                    <p class="text-2xl font-semibold text-green-600 mt-1">{{ $stats['active'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Pending</p>
                    <p class="text-2xl font-semibold text-yellow-600 mt-1">{{ $stats['pending'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Rejected</p>
                    <p class="text-2xl font-semibold text-red-600 mt-1">{{ $stats['rejected'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Drafts</p>
                    <p class="text-2xl font-semibold text-gray-600 mt-1">{{ $stats['draft'] }}</p>
                </div>
            </div>

            {{-- Shop Info --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">
                        Your Shop
                    </h4>

                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-xs text-gray-500 uppercase">Shop Name</dt>
                            <dd class="text-sm text-gray-900 mt-1">
                                {{ Auth::user()->shop_name ?? '—' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 uppercase">Status</dt>
                            <dd class="text-sm text-gray-900 mt-1">
                                @if (Auth::user()->is_active && Auth::user()->approved_at)
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-xs text-gray-500 uppercase">Address</dt>
                            <dd class="text-sm text-gray-900 mt-1">
                                {{ Auth::user()->shop_address ?? '—' }}
                            </dd>
                        </div>
                        @if (Auth::user()->shop_latitude && Auth::user()->shop_longitude)
                            <div>
                                <dt class="text-xs text-gray-500 uppercase">Latitude</dt>
                                <dd class="text-sm text-gray-900 mt-1">{{ Auth::user()->shop_latitude }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500 uppercase">Longitude</dt>
                                <dd class="text-sm text-gray-900 mt-1">{{ Auth::user()->shop_longitude }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            {{-- Action Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Products (LIVE) --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-2 border-emerald-200 hover:shadow-md transition">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-lg font-semibold text-gray-900">📦 Products</h4>
                            <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">
                                {{ $used }} / {{ $cap }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mb-4">
                            Manage your product listings, track review status, and add new products.
                        </p>

                        {{-- Product cap indicator --}}
                        <div class="mb-4">
                            <div class="flex justify-between text-[11px] text-gray-500 mb-1">
                                <span>Product slots</span>
                                <span>{{ $used }} of {{ $cap }} used</span>
                            </div>
                            <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all
                                    @if ($used >= $cap) bg-red-500
                                    @elseif ($used >= $cap * 0.8) bg-amber-500
                                    @else bg-emerald-500 @endif"
                                    style="width: {{ $cap > 0 ? min(100, ($used / $cap) * 100) : 0 }}%"></div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('vendor.products.index') }}"
                                class="inline-flex items-center gap-2 bg-[#1E3C2C] text-white px-4 py-2 rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-[#143023] transition">
                                Manage Products
                            </a>
                            @if ($canAdd)
                                <a href="{{ route('vendor.products.create') }}"
                                    class="inline-flex items-center gap-2 bg-emerald-600 text-white px-4 py-2 rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-emerald-700 transition">
                                    + Add New
                                </a>
                            @else
                                <span class="inline-flex items-center gap-2 bg-gray-200 text-gray-500 px-4 py-2 rounded-md font-semibold text-xs uppercase tracking-widest cursor-not-allowed"
                                    title="You've reached your product limit ({{ $cap }}).">
                                    Limit Reached
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Orders (Still coming soon) --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-2 border-dashed border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-lg font-semibold text-gray-900">📋 Orders</h4>
                            <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">
                                Soon
                            </span>
                        </div>
                        <p class="text-sm text-gray-500">
                            View and fulfill orders for your products.
                        </p>
                    </div>
                </div>

                {{-- Analytics (Still coming soon) --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-2 border-dashed border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-lg font-semibold text-gray-900">📊 Analytics</h4>
                            <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">
                                Soon
                            </span>
                        </div>
                        <p class="text-sm text-gray-500">
                            Track sales, views, and performance.
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>