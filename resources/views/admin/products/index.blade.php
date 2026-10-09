<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Product Management') }}
            </h2>
            <a href="{{ route('admin.products.create') }}"
                class="inline-flex items-center gap-2 bg-[#1E3C2C] text-white px-4 py-2 rounded-lg hover:bg-[#143023] transition text-sm font-semibold">
                <i class="fas fa-plus text-xs"></i> New Product
            </a>
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
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Disabled</p>
                    <p class="text-xl font-semibold text-red-600 mt-1">{{ $stats['disabled'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Admin</p>
                    <p class="text-xl font-semibold text-blue-600 mt-1">{{ $stats['admin_owned'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Vendor</p>
                    <p class="text-xl font-semibold text-amber-600 mt-1">{{ $stats['vendor_owned'] }}</p>
                </div>
            </div>

            {{-- Filters --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <form method="GET" class="p-4 flex flex-col gap-3">
                    <div class="flex flex-col lg:flex-row gap-3">
                        <div class="relative flex-1">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" name="q" value="{{ request('q') }}"
                                placeholder="Search by name, SKU, or slug…"
                                class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>

                        <select name="source" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">All sources</option>
                            <option value="admin" {{ request('source') === 'admin' ? 'selected' : '' }}>Admin products</option>
                            <option value="vendor" {{ request('source') === 'vendor' ? 'selected' : '' }}>Vendor products</option>
                        </select>

                        <select name="status" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">Any status</option>
                            @foreach (['draft', 'active', 'pending_review', 'rejected', 'disabled', 'archived'] as $s)
                                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $s)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col lg:flex-row gap-3">
                        <select name="category" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm flex-1">
                            <option value="">All categories</option>
                            @foreach ($categories as $parent)
                                <option value="{{ $parent->id }}" {{ (string) request('category') === (string) $parent->id ? 'selected' : '' }}>
                                    {{ $parent->name }}
                                </option>
                                @foreach ($parent->children as $child)
                                    <option value="{{ $child->id }}" {{ (string) request('category') === (string) $child->id ? 'selected' : '' }}>
                                        &nbsp;&nbsp;&nbsp;↳ {{ $child->name }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>

                        <select name="brand" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm flex-1">
                            <option value="">All brands</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" {{ (string) request('brand') === (string) $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>

                        <select name="sort" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest first</option>
                            <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name A–Z</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: low to high</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: high to low</option>
                            <option value="stock" {{ request('sort') === 'stock' ? 'selected' : '' }}>Lowest stock</option>
                            <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>Top rated</option>
                        </select>

                        <button type="submit"
                            class="px-4 py-2 bg-gray-800 text-white text-sm font-semibold rounded-lg hover:bg-gray-900 whitespace-nowrap">
                            Apply
                        </button>

                        @if (request()->hasAny(['q', 'source', 'category', 'brand', 'status', 'sort']))
                            <a href="{{ route('admin.products.index') }}"
                                class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 whitespace-nowrap text-center">
                                Clear
                            </a>
                        @endif
                    </div>
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
                        <p class="text-gray-500 mb-4">No products found.</p>
                        @if (request()->hasAny(['q', 'source', 'category', 'brand', 'status', 'sort']))
                            <a href="{{ route('admin.products.index') }}"
                                class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                Clear filters
                            </a>
                        @else
                            <a href="{{ route('admin.products.create') }}"
                                class="inline-flex items-center gap-2 bg-[#1E3C2C] text-white px-4 py-2 rounded-lg hover:bg-[#143023] transition text-sm font-semibold">
                                <i class="fas fa-plus text-xs"></i> Create your first product
                            </a>
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
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Source</th>
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
                                                            class="w-full h-full object-cover"
                                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                        <i class="fas fa-image text-gray-300" style="display:none;"></i>
                                                    @else
                                                        <i class="fas fa-image text-gray-300"></i>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="flex items-center gap-1.5">
                                                        <a href="{{ route('admin.products.show', $product) }}"
                                                            class="font-medium text-gray-900 hover:text-indigo-600 truncate">
                                                            {{ $product->name }}
                                                        </a>
                                                        @if ($product->is_featured)
                                                            <i class="fas fa-star text-yellow-500 text-xs" title="Featured"></i>
                                                        @endif
                                                    </div>
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

                                        {{-- Source --}}
                                        <td class="py-3 px-4 text-sm">
                                            @if ($product->isVendorProduct())
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                    <i class="fas fa-store text-[9px]"></i>
                                                    {{ $product->source?->shop_name ?? $product->source?->name ?? 'Vendor' }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <i class="fas fa-shield-alt text-[9px]"></i> Admin
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Price --}}
                                        <td class="py-3 px-4 text-sm text-right">
                                            <div class="font-medium text-gray-900">
                                                {{ $product->formattedPrice() }}
                                            </div>
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
                                            @if ($product->status === 'disabled' && $product->disabled_reason)
                                                <div class="text-[10px] text-red-600 mt-0.5 truncate max-w-[140px]" title="{{ $product->disabled_reason }}">
                                                    {{ \Illuminate\Support\Str::limit($product->disabled_reason, 40) }}
                                                </div>
                                            @endif
                                            @if ($product->status === 'pending_review')
                                                <div class="flex items-center gap-1.5 mt-1.5">
                                                    <form method="POST" action="{{ route('admin.products.approve', $product) }}">
                                                        @csrf
                                                        <button type="submit"
                                                            class="text-[11px] font-medium text-green-700 hover:text-green-900">
                                                            Approve
                                                        </button>
                                                    </form>
                                                    <button type="button"
                                                        onclick="openRejectModal({{ $product->id }}, '{{ addslashes($product->name) }}')"
                                                        class="text-[11px] font-medium text-red-700 hover:text-red-900">
                                                        Reject
                                                    </button>
                                                </div>
                                            @endif
                                        </td>

                                        {{-- Actions --}}
                                        <td class="py-3 px-4 text-right">
                                            <div class="flex items-center justify-end gap-1">
                                                <a href="{{ route('admin.products.show', $product) }}"
                                                    class="text-gray-600 hover:text-gray-900 text-sm font-medium px-2 py-1.5 rounded hover:bg-gray-100 transition"
                                                    title="View">
                                                    <i class="fas fa-eye text-xs"></i>
                                                </a>

                                                <a href="{{ route('admin.products.edit', $product) }}"
                                                    class="text-indigo-600 hover:text-indigo-800 text-sm font-medium px-2 py-1.5 rounded hover:bg-indigo-50 transition"
                                                    title="Edit">
                                                    <i class="fas fa-pen text-xs"></i>
                                                </a>

                                                <form method="POST" action="{{ route('admin.products.toggleFeatured', $product) }}" class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="text-sm font-medium px-2 py-1.5 rounded transition {{ $product->is_featured ? 'text-yellow-600 hover:text-yellow-800' : 'text-gray-400 hover:text-yellow-600' }}"
                                                        title="{{ $product->is_featured ? 'Unfeature' : 'Feature' }}">
                                                        <i class="fas fa-star text-xs"></i>
                                                    </button>
                                                </form>

                                                @if ($product->status === 'disabled')
                                                    <form method="POST" action="{{ route('admin.products.enable', $product) }}" class="inline">
                                                        @csrf
                                                        <button type="submit"
                                                            class="text-sm font-medium text-green-600 hover:text-green-800 px-2 py-1.5 rounded transition"
                                                            title="Enable">
                                                            <i class="fas fa-check-circle text-xs"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button type="button"
                                                        onclick="openDisableModal({{ $product->id }}, '{{ addslashes($product->name) }}')"
                                                        class="text-sm font-medium text-red-600 hover:text-red-800 px-2 py-1.5 rounded transition"
                                                        title="Disable">
                                                        <i class="fas fa-ban text-xs"></i>
                                                    </button>
                                                @endif

                                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                                    onsubmit="return confirm('Delete product &quot;{{ addslashes($product->name) }}&quot;? This soft-deletes it.')"
                                                    class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-sm font-medium text-red-600 hover:text-red-800 px-2 py-1.5 rounded transition"
                                                        title="Delete">
                                                        <i class="fas fa-trash text-xs"></i>
                                                    </button>
                                                </form>
                                            </div>
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

    {{-- Reject Modal --}}
    <div id="rejectModal" class="hidden fixed inset-0 z-50 bg-black/50 items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Reject Product</h3>
            <p class="text-sm text-gray-500 mb-4">
                Rejecting: <strong id="rejectProductName"></strong>
            </p>
            <form id="rejectForm" method="POST">
                @csrf
                <label class="block text-sm font-medium text-gray-700 mb-1">Rejection reason</label>
                <textarea name="rejection_reason" rows="3" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                    placeholder="Explain what needs to change…"></textarea>
                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" onclick="closeRejectModal()"
                        class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700">
                        Reject
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Disable Modal --}}
    <div id="disableModal" class="hidden fixed inset-0 z-50 bg-black/50 items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Disable Product</h3>
            <p class="text-sm text-gray-500 mb-4">
                Disabling hides the product <strong>site-wide</strong>. Product: <strong id="disableProductName"></strong>
            </p>
            <form id="disableForm" method="POST">
                @csrf
                <label class="block text-sm font-medium text-gray-700 mb-1">Reason for disabling</label>
                <textarea name="disabled_reason" rows="3" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                    placeholder="e.g. Regulatory violation, repeated complaints…"></textarea>
                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" onclick="closeDisableModal()"
                        class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700">
                        Disable Product
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRejectModal(productId, productName) {
            document.getElementById('rejectProductName').textContent = productName;
            document.getElementById('rejectForm').action = '/admin/products/' + productId + '/reject';
            document.getElementById('rejectModal').classList.remove('hidden');
            document.getElementById('rejectModal').classList.add('flex');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.getElementById('rejectModal').classList.remove('flex');
        }

        function openDisableModal(productId, productName) {
            document.getElementById('disableProductName').textContent = productName;
            document.getElementById('disableForm').action = '/admin/products/' + productId + '/disable';
            document.getElementById('disableModal').classList.remove('hidden');
            document.getElementById('disableModal').classList.add('flex');
        }

        function closeDisableModal() {
            document.getElementById('disableModal').classList.add('hidden');
            document.getElementById('disableModal').classList.remove('flex');
        }
    </script>
</x-app-layout>