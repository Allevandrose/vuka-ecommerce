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
                                Your vendor account is active. Products and orders coming soon.
                            </p>
                        </div>
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                            Vendor
                        </span>
                    </div>
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

            {{-- Coming Soon Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-2 border-dashed border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-lg font-semibold text-gray-900">📦 Products</h4>
                            <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">
                                Soon
                            </span>
                        </div>
                        <p class="text-sm text-gray-500">
                            List products you want to sell on Vuka Shop.
                        </p>
                    </div>
                </div>

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