<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Brand Management') }}
            </h2>
            <a href="{{ route('admin.brands.create') }}"
                class="inline-flex items-center gap-2 bg-[#1E3C2C] text-white px-4 py-2 rounded-lg hover:bg-[#143023] transition text-sm font-semibold">
                <i class="fas fa-plus text-xs"></i> New Brand
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

            {{-- Filters Bar --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
                    <form method="GET" class="flex-1 flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-1">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" name="q" value="{{ request('q') }}"
                                placeholder="Search brands…"
                                class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>

                        <select name="filter"
                            class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">All brands</option>
                            <option value="verified" {{ request('filter') === 'verified' ? 'selected' : '' }}>Verified only</option>
                            <option value="unverified" {{ request('filter') === 'unverified' ? 'selected' : '' }}>Unverified only</option>
                            <option value="inactive" {{ request('filter') === 'inactive' ? 'selected' : '' }}>Inactive only</option>
                        </select>

                        <button type="submit"
                            class="px-4 py-2 bg-gray-800 text-white text-sm font-semibold rounded-lg hover:bg-gray-900">
                            Filter
                        </button>

                        @if (request()->hasAny(['q', 'filter']))
                            <a href="{{ route('admin.brands.index') }}"
                                class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 text-center">
                                Clear
                            </a>
                        @endif
                    </form>

                    <div class="text-xs text-gray-500 self-center">
                        {{ $brands->total() }} brand{{ $brands->total() === 1 ? '' : 's' }}
                    </div>
                </div>
            </div>

            {{-- Brand List --}}
            @if ($brands->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <p class="text-gray-500 mb-4">No brands found.</p>
                        @if (request()->hasAny(['q', 'filter']))
                            <a href="{{ route('admin.brands.index') }}"
                                class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                Clear filters
                            </a>
                        @else
                            <a href="{{ route('admin.brands.create') }}"
                                class="inline-flex items-center gap-2 bg-[#1E3C2C] text-white px-4 py-2 rounded-lg hover:bg-[#143023] transition text-sm font-semibold">
                                Create your first brand
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
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Brand</th>
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Website</th>
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Products</th>
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="text-right py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($brands as $brand)
                                    <tr class="hover:bg-gray-50 transition">
                                        {{-- Brand --}}
                                        <td class="py-3 px-4">
                                            <div class="flex items-center gap-3">
                                                <div class="shrink-0 w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center overflow-hidden">
                                                    @if ($brand->logo)
                                                        <img src="{{ $brand->logo }}" alt="{{ $brand->name }}" class="w-full h-full object-contain p-1">
                                                    @else
                                                        <span class="text-gray-500 font-semibold text-sm">
                                                            {{ strtoupper(substr($brand->name, 0, 1)) }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="font-medium text-gray-900 truncate">{{ $brand->name }}</span>
                                                        @if ($brand->is_verified)
                                                            <i class="fas fa-circle-check text-blue-500 text-xs" title="Verified brand"></i>
                                                        @endif
                                                    </div>
                                                    <div class="text-xs text-gray-500 font-mono">{{ $brand->slug }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Website --}}
                                        <td class="py-3 px-4 text-sm">
                                            @if ($brand->website)
                                                <a href="{{ $brand->website }}" target="_blank" rel="noopener"
                                                    class="text-indigo-600 hover:text-indigo-800 inline-flex items-center gap-1">
                                                    Visit <i class="fas fa-external-link-alt text-[10px]"></i>
                                                </a>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>

                                        {{-- Products --}}
                                        <td class="py-3 px-4 text-sm">
                                            <span class="inline-flex items-center gap-1.5 text-gray-700">
                                                <i class="fas fa-box text-gray-400 text-xs"></i>
                                                {{ $brand->products_count }}
                                            </span>
                                        </td>

                                        {{-- Status --}}
                                        <td class="py-3 px-4">
                                            <div class="flex flex-col gap-1">
                                                @if ($brand->is_active)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 w-fit">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Active
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 w-fit">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Inactive
                                                    </span>
                                                @endif
                                                @if ($brand->is_verified)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 w-fit">
                                                        <i class="fas fa-circle-check text-[9px]"></i> Verified
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Actions --}}
                                        <td class="py-3 px-4 text-right">
                                            <div class="flex items-center justify-end gap-1">
                                                <a href="{{ route('admin.brands.edit', $brand) }}"
                                                    class="text-indigo-600 hover:text-indigo-800 text-sm font-medium px-3 py-1.5 rounded hover:bg-indigo-50 transition">
                                                    Edit
                                                </a>
                                                <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}"
                                                    onsubmit="return confirm('Delete brand &quot;{{ $brand->name }}&quot;? Products using it will have no brand.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-800 text-sm font-medium px-3 py-1.5 rounded hover:bg-red-50 transition">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($brands->hasPages())
                        <div class="px-4 py-3 border-t border-gray-100">
                            {{ $brands->links() }}
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>
</x-app-layout>