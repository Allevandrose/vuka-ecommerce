<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Pending Product Review') }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    Review vendor-submitted products before they go live on the site.
                </p>
            </div>
            <a href="{{ route('admin.products.index') }}"
                class="text-sm text-indigo-600 hover:text-indigo-800 inline-flex items-center gap-1">
                <i class="fas fa-arrow-left text-xs"></i> All Products
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-5 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Pending</p>
                            <p class="text-3xl font-semibold text-yellow-600 mt-1">{{ $stats['total'] }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-lg bg-yellow-100 flex items-center justify-center text-yellow-600">
                            <i class="fas fa-clock text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-5 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">From Vendors</p>
                            <p class="text-3xl font-semibold text-amber-600 mt-1">{{ $stats['vendor'] }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600">
                            <i class="fas fa-store text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-5 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">From Admin</p>
                            <p class="text-3xl font-semibold text-blue-600 mt-1">{{ $stats['admin'] }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                            <i class="fas fa-shield-alt text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <form method="GET" class="p-4 flex flex-col lg:flex-row gap-3">
                    <div class="relative flex-1">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="q" value="{{ request('q') }}"
                            placeholder="Search by product name or SKU…"
                            class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>

                    <select name="source" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">All sources</option>
                        <option value="vendor" {{ request('source') === 'vendor' ? 'selected' : '' }}>Vendors only</option>
                        <option value="admin" {{ request('source') === 'admin' ? 'selected' : '' }}>Admin only</option>
                    </select>

                    @if ($vendors->isNotEmpty())
                        <select name="vendor" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">All vendors</option>
                            @foreach ($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ (string) request('vendor') === (string) $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->shop_name ?? $vendor->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif

                    <button type="submit"
                        class="px-4 py-2 bg-gray-800 text-white text-sm font-semibold rounded-lg hover:bg-gray-900 whitespace-nowrap">
                        Apply
                    </button>

                    @if (request()->hasAny(['q', 'source', 'vendor']))
                        <a href="{{ route('admin.products.pending') }}"
                            class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 whitespace-nowrap text-center">
                            Clear
                        </a>
                    @endif
                </form>
            </div>

            {{-- Pending Products --}}
            @if ($products->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-4 text-emerald-600">
                            <i class="fas fa-check-circle text-2xl"></i>
                        </div>
                        <h3 class="font-semibold text-lg text-gray-900 mb-2">All caught up!</h3>
                        <p class="text-sm text-gray-500">
                            @if (request()->hasAny(['q', 'source', 'vendor']))
                                No pending products match your filters.
                            @else
                                There are no products awaiting review.
                            @endif
                        </p>
                        @if (request()->hasAny(['q', 'source', 'vendor']))
                            <a href="{{ route('admin.products.pending') }}"
                                class="inline-block mt-4 text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                Clear filters
                            </a>
                        @endif
                    </div>
                </div>
            @else
                {{-- Bulk Approve Bar --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6"
                     x-data="{ selected: [] }">
                    <div class="p-4 bg-blue-50 border-b border-blue-100 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox"
                                    @change="selected = $event.target.checked ? [...document.querySelectorAll('input[name='product_ids[]']:not([name='product_ids[]']:checked)? ''] : []"
                                    x-on:change="
                                        const all = [...document.querySelectorAll('input[data-bulk-checkbox]')];
                                        if ($event.target.checked) {
                                            all.forEach(cb => { cb.checked = true; cb.dispatchEvent(new Event('change', { bubbles: true })); });
                                        } else {
                                            all.forEach(cb => { cb.checked = false; cb.dispatchEvent(new Event('change', { bubbles: true })); });
                                        }
                                    "
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="text-sm font-medium text-gray-800">Select all on page</span>
                            </label>
                        </div>

                        <form method="POST" action="{{ route('admin.products.bulkApprove') }}" class="contents">
                            @csrf
                            <div x-show="selected.length > 0" x-cloak class="flex items-center gap-3">
                                <span class="text-sm text-gray-700">
                                    <span x-text="selected.length"></span> selected
                                </span>
                                <button type="submit"
                                    class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm font-semibold">
                                    <i class="fas fa-check-circle text-xs"></i> Approve Selected
                                </button>
                            </div>

                            {{-- Hidden inputs for selected product ids --}}
                            <template x-for="id in selected" :key="id">
                                <input type="hidden" name="product_ids[]" :value="id">
                            </template>
                        </form>
                    </div>
                </div>

                {{-- Per-vendor groups --}}
                <div class="space-y-4">
                    @foreach ($groupedByVendor as $groupKey => $group)
                        @php
                            $firstProduct = $group->first();
                            $isVendorGroup = $firstProduct->source_type === 'vendor';
                            $vendor = $isVendorGroup ? $firstProduct->source : null;
                        @endphp

                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            {{-- Group header --}}
                            <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    @if ($isVendorGroup)
                                        <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center text-amber-700">
                                            <i class="fas fa-store text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-sm text-gray-900">
                                                {{ $vendor?->shop_name ?? $vendor?->name ?? 'Unknown vendor' }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $vendor?->email ?? '—' }}
                                                @if ($vendor?->shop_address)
                                                    · {{ Str::limit($vendor->shop_address, 40) }}
                                                @endif
                                            </p>
                                        </div>
                                    @else
                                        <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center text-blue-700">
                                            <i class="fas fa-shield-alt text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-sm text-gray-900">Admin products</p>
                                            <p class="text-xs text-gray-500">Submitted by admin</p>
                                        </div>
                                    @endif
                                </div>
                                <span class="text-xs text-gray-500">
                                    {{ $group->count() }} product{{ $group->count() === 1 ? '' : 's' }}
                                </span>
                            </div>

                            {{-- Group products --}}
                            <div class="divide-y divide-gray-100">
                                @foreach ($group as $product)
                                    <div class="p-4 flex flex-col sm:flex-row sm:items-center gap-4 hover:bg-gray-50 transition">
                                        {{-- Checkbox --}}
                                        <div class="shrink-0">
                                            <input type="checkbox"
                                                value="{{ $product->id }}"
                                                data-bulk-checkbox
                                                x-on:change="
                                                    if ($event.target.checked) {
                                                        if (!selected.includes({{ $product->id }})) selected.push({{ $product->id }});
                                                    } else {
                                                        selected = selected.filter(i => i !== {{ $product->id }});
                                                    }
                                                "
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        </div>

                                        {{-- Thumbnail --}}
                                        <div class="shrink-0 w-14 h-14 rounded-lg bg-gray-100 border border-gray-200 overflow-hidden flex items-center justify-center">
                                            @if ($product->thumbnail)
                                                <img src="{{ asset('storage/' . $product->thumbnail) }}"
                                                    alt="{{ $product->name }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <i class="fas fa-image text-gray-300"></i>
                                            @endif
                                        </div>

                                        {{-- Info --}}
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <a href="{{ route('admin.products.show', $product) }}"
                                                    class="font-medium text-gray-900 hover:text-indigo-600 truncate">
                                                    {{ $product->name }}
                                                </a>
                                                @if ($product->is_featured)
                                                    <i class="fas fa-star text-yellow-500 text-xs"></i>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500 mt-0.5 font-mono truncate">
                                                {{ $product->sku }}
                                                @if ($product->category)
                                                    · {{ $product->category->name }}
                                                @endif
                                                @if ($product->brand)
                                                    · {{ $product->brand->name }}
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-400 mt-0.5">
                                                Submitted {{ $product->created_at->diffForHumans() }}
                                            </div>
                                        </div>

                                        {{-- Price --}}
                                        <div class="shrink-0 text-right sm:min-w-[100px]">
                                            <div class="font-semibold text-gray-900">{{ $product->formattedPrice() }}</div>
                                            @if ($product->isOnSale())
                                                <div class="text-xs text-gray-400 line-through">
                                                    {{ $product->formattedCompareAtPrice() }}
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Actions --}}
                                        <div class="shrink-0 flex items-center gap-1">
                                            <a href="{{ route('admin.products.show', $product) }}"
                                                class="text-gray-600 hover:text-gray-900 text-sm px-2 py-1.5 rounded hover:bg-gray-100 transition"
                                                title="View full details">
                                                <i class="fas fa-eye text-xs"></i>
                                            </a>

                                            <form method="POST" action="{{ route('admin.products.approve', $product) }}" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="text-green-600 hover:text-green-800 text-sm font-medium px-3 py-1.5 rounded hover:bg-green-50 transition"
                                                    title="Approve product">
                                                    <i class="fas fa-check text-xs"></i> Approve
                                                </button>
                                            </form>

                                            <button type="button"
                                                onclick="openRejectModal({{ $product->id }}, '{{ addslashes($product->name) }}')"
                                                class="text-red-600 hover:text-red-800 text-sm font-medium px-3 py-1.5 rounded hover:bg-red-50 transition"
                                                title="Reject product">
                                                <i class="fas fa-times text-xs"></i> Reject
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($products->hasPages())
                    <div class="mt-6">
                        {{ $products->links() }}
                    </div>
                @endif
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
                    placeholder="Explain what the vendor needs to fix…"></textarea>
                <p class="mt-1 text-xs text-gray-500">
                    The vendor will see this reason in their product list and can resubmit after fixing.
                </p>
                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" onclick="closeRejectModal()"
                        class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700">
                        Reject Product
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
    </script>
</x-app-layout>