<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('My Products') }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $used }} / {{ $cap }} products used
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if ($canAdd)
                    <a href="{{ route('vendor.products.create') }}"
                        class="inline-flex items-center gap-2 bg-[#1E3C2C] text-white px-4 py-2 rounded-lg hover:bg-[#143023] transition text-sm font-semibold">
                        <i class="fas fa-plus text-xs"></i> New Product
                    </a>
                @else
                    <span class="inline-flex items-center gap-2 bg-gray-200 text-gray-500 px-4 py-2 rounded-lg text-sm font-semibold cursor-not-allowed"
                        title="You have reached your product limit ({{ $cap }}).">
                        <i class="fas fa-lock text-xs"></i> Limit reached
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Product cap warning --}}
            @if (!$canAdd)
                <div class="mb-4 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                    <p class="text-sm font-semibold text-amber-800 flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle"></i> Product limit reached
                    </p>
                    <p class="text-sm text-amber-700 mt-1">
                        You've listed {{ $cap }} products, which is your current maximum.
                        Contact VukaShop support to request a higher limit.
                    </p>
                </div>
            @endif

            {{-- Stats Strip --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Total</p>
                    <p class="text-xl font-semibold text-gray-900 mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Active</p>
                    <p class="text-xl font-semibold text-green-600 mt-1">{{ $stats['active'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Pending</p>
                    <p class="text-xl font-semibold text-yellow-600 mt-1">{{ $stats['pending'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Rejected</p>
                    <p class="text-xl font-semibold text-red-600 mt-1">{{ $stats['rejected'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Drafts</p>
                    <p class="text-xl font-semibold text-gray-600 mt-1">{{ $stats['draft'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Disabled</p>
                    <p class="text-xl font-semibold text-red-600 mt-1">{{ $stats['disabled'] }}</p>
                </div>
            </div>

            {{-- Filters --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <form method="GET" class="p-4 flex flex-col lg:flex-row gap-3">
                    <div class="relative flex-1">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="q" value="{{ request('q') }}"
                            placeholder="Search by name or SKU…"
                            class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>

                    <select name="status" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Any status</option>
                        @foreach (['draft', 'pending_review', 'active', 'rejected', 'disabled', 'archived'] as $s)
                            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $s)) }}
                            </option>
                        @endforeach
                    </select>

                    <select name="sort" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Newest first</option>
                        <option value="name" {{ $sort === 'name' ? 'selected' : '' }}>Name A–Z</option>
                        <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Price: low to high</option>
                        <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Price: high to low</option>
                        <option value="status" {{ $sort === 'status' ? 'selected' : '' }}>Status</option>
                    </select>

                    <button type="submit"
                        class="px-4 py-2 bg-gray-800 text-white text-sm font-semibold rounded-lg hover:bg-gray-900 whitespace-nowrap">
                        Apply
                    </button>

                    @if (request()->hasAny(['q', 'status', 'sort']))
                        <a href="{{ route('vendor.products.index') }}"
                            class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 whitespace-nowrap text-center">
                            Clear
                        </a>
                    @endif
                </form>

                <div class="px-4 pb-4 text-xs text-gray-500">
                    Showing <strong>{{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}</strong>
                    of <strong>{{ $products->total() }}</strong> product{{ $products->total() === 1 ? '' : 's' }}
                </div>
            </div>

            {{-- Product List --}}
            @if ($products->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <i class="fas fa-box-open text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 mb-4">
                            @if (request()->hasAny(['q', 'status']))
                                No products match your filters.
                            @else
                                You haven't added any products yet.
                            @endif
                        </p>
                        @if (request()->hasAny(['q', 'status']))
                            <a href="{{ route('vendor.products.index') }}"
                                class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                Clear filters
                            </a>
                        @elseif ($canAdd)
                            <p class="text-sm text-gray-400">
                                Add your first product to start selling on VukaShop.
                            </p>
                        @endif
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b bg-gray-50">
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="text-right py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                    <th class="text-center py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="text-right py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($products as $product)
                                    <tr class="hover:bg-gray-50 transition {{ $product->status === 'disabled' ? 'opacity-60' : '' }}">
                                        {{-- Product --}}
                                        <td class="py-3 px-4">
                                            <div class="flex items-center gap-3">
                                                <div class="shrink-0 w-12 h-12 rounded-lg bg-gray-100 border border-gray-200 overflow-hidden flex items-center justify-center">
                                                    @if ($product->thumbnail)
                                                        <img src="{{ asset('storage/' . $product->thumbnail) }}"
                                                            alt="{{ $product->name }}"
                                                            class="w-full h-full object-cover">
                                                    @else
                                                        <i class="fas fa-image text-gray-300"></i>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="font-medium text-gray-900 truncate">{{ $product->name }}</div>
                                                    <div class="text-xs text-gray-500 font-mono truncate">
                                                        {{ $product->sku }}
                                                        @if ($product->brand)
                                                            · {{ $product->brand->name }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Category --}}
                                        <td class="py-3 px-4 text-sm">
                                            @if ($product->category)
                                                <span class="text-gray-700">{{ $product->category->name }}</span>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>

                                        {{-- Price --}}
                                        <td class="py-3 px-4 text-sm text-right">
                                            <div class="font-medium text-gray-900">{{ $product->formattedPrice() }}</div>
                                            @if ($product->isOnSale())
                                                <div class="text-xs text-gray-400 line-through">
                                                    {{ $product->formattedCompareAtPrice() }}
                                                </div>
                                            @endif
                                        </td>

                                        {{-- Stock --}}
                                        <td class="py-3 px-4 text-center text-sm">
                                            @if (!$product->track_inventory)
                                                <span class="text-xs text-gray-400">N/A</span>
                                            @elseif ($product->stock_quantity === 0)
                                                <span class="inline-flex items-center gap-1 text-xs font-medium text-red-600">
                                                    <i class="fas fa-times-circle text-[9px]"></i> 0
                                                </span>
                                            @elseif ($product->isLowStock())
                                                <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-600">
                                                    <i class="fas fa-exclamation-triangle text-[9px]"></i>
                                                    {{ $product->stock_quantity }}
                                                </span>
                                            @else
                                                <span class="text-gray-700">{{ $product->stock_quantity }}</span>
                                            @endif
                                        </td>

                                        {{-- Status --}}
                                        <td class="py-3 px-4">
                                            @php
                                                $statusColors = [
                                                    'draft' => 'bg-gray-100 text-gray-700',
                                                    'active' => 'bg-green-100 text-green-800',
                                                    'pending_review' => 'bg-yellow-100 text-yellow-800',
                                                    'rejected' => 'bg-red-100 text-red-800',
                                                    'disabled' => 'bg-red-100 text-red-800',
                                                    'archived' => 'bg-gray-200 text-gray-600',
                                                ];
                                                $color = $statusColors[$product->status] ?? 'bg-gray-100 text-gray-700';
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                                                {{ ucfirst(str_replace('_', ' ', $product->status)) }}
                                            </span>
                                            @if ($product->status === 'rejected' && $product->rejection_reason)
                                                <div class="text-[10px] text-red-600 mt-1 truncate max-w-[180px]" title="{{ $product->rejection_reason }}">
                                                    <i class="fas fa-info-circle text-[9px]"></i>
                                                    {{ Str::limit($product->rejection_reason, 50) }}
                                                </div>
                                            @endif
                                            @if ($product->status === 'disabled' && $product->disabled_reason)
                                                <div class="text-[10px] text-red-600 mt-1 truncate max-w-[180px]" title="{{ $product->disabled_reason }}">
                                                    <i class="fas fa-ban text-[9px]"></i>
                                                    {{ Str::limit($product->disabled_reason, 50) }}
                                                </div>
                                            @endif
                                        </td>

                                        {{-- Actions --}}
                                        <td class="py-3 px-4 text-right">
                                            @if ($product->status === 'active')
                                                <a href="{{ route('product.show', $product->slug) }}" target="_blank"
                                                    class="text-gray-600 hover:text-gray-900 text-sm font-medium px-2 py-1.5 rounded hover:bg-gray-100 transition"
                                                    title="View public page">
                                                    <i class="fas fa-eye text-xs"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('vendor.products.edit', $product) }}"
                                                class="text-indigo-600 hover:text-indigo-800 text-sm font-medium px-2 py-1.5 rounded hover:bg-indigo-50 transition"
                                                title="Edit">
                                                <i class="fas fa-pen text-xs"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($products->hasPages())
                        <div class="px-4 py-3 border-t border-gray-100">
                            {{ $products->links() }}
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>
</x-app-layout>