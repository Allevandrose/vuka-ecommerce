<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Attribute Management') }}
            </h2>
            <a href="{{ route('admin.attributes.create') }}"
                class="inline-flex items-center gap-2 bg-[#1E3C2C] text-white px-4 py-2 rounded-lg hover:bg-[#143023] transition text-sm font-semibold">
                <i class="fas fa-plus text-xs"></i> New Attribute
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
                <form method="GET" class="p-4 flex flex-col lg:flex-row gap-3">
                    <div class="relative flex-1">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="q" value="{{ request('q') }}"
                            placeholder="Search key or label…"
                            class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>

                    <select name="category"
                        class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">All categories</option>
                        <option value="global" {{ request('category') === 'global' ? 'selected' : '' }}>— Global only —</option>
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

                    <select name="type"
                        class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">All types</option>
                        @foreach ($types as $t)
                            <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>

                    <select name="filter"
                        class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Any status</option>
                        <option value="filterable" {{ request('filter') === 'filterable' ? 'selected' : '' }}>Filterable only</option>
                        <option value="required" {{ request('filter') === 'required' ? 'selected' : '' }}>Required only</option>
                        <option value="inactive" {{ request('filter') === 'inactive' ? 'selected' : '' }}>Inactive only</option>
                    </select>

                    <button type="submit"
                        class="px-4 py-2 bg-gray-800 text-white text-sm font-semibold rounded-lg hover:bg-gray-900">
                        Filter
                    </button>

                    @if (request()->hasAny(['q', 'category', 'type', 'filter']))
                        <a href="{{ route('admin.attributes.index') }}"
                            class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 text-center">
                            Clear
                        </a>
                    @endif
                </form>

                <div class="px-4 pb-4 text-xs text-gray-500">
                    {{ $attributes->total() }} attribute{{ $attributes->total() === 1 ? '' : 's' }}
                </div>
            </div>

            {{-- List --}}
            @if ($attributes->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <p class="text-gray-500 mb-4">No attributes found.</p>
                        @if (request()->hasAny(['q', 'category', 'type', 'filter']))
                            <a href="{{ route('admin.attributes.index') }}"
                                class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                Clear filters
                            </a>
                        @else
                            <a href="{{ route('admin.attributes.create') }}"
                                class="inline-flex items-center gap-2 bg-[#1E3C2C] text-white px-4 py-2 rounded-lg hover:bg-[#143023] transition text-sm font-semibold">
                                Create your first attribute
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
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Attribute</th>
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Scope</th>
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Options</th>
                                    <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Flags</th>
                                    <th class="text-right py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($attributes as $attr)
                                    <tr class="hover:bg-gray-50 transition">
                                        {{-- Attribute --}}
                                        <td class="py-3 px-4">
                                            <div class="font-medium text-gray-900">{{ $attr->label }}</div>
                                            <div class="text-xs text-gray-500 font-mono">{{ $attr->key }}</div>
                                        </td>

                                        {{-- Scope --}}
                                        <td class="py-3 px-4 text-sm">
                                            @if ($attr->category_id === null)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                    <i class="fas fa-globe text-[9px]"></i> Global
                                                </span>
                                            @else
                                                <span class="text-gray-700">{{ $attr->category?->name ?? '—' }}</span>
                                            @endif
                                        </td>

                                        {{-- Type --}}
                                        <td class="py-3 px-4 text-sm">
                                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                                {{ $attr->type }}
                                            </span>
                                            @if ($attr->unit)
                                                <span class="text-xs text-gray-500 ml-1">({{ $attr->unit }})</span>
                                            @endif
                                        </td>

                                        {{-- Options preview --}}
                                        <td class="py-3 px-4 text-sm text-gray-600">
                                            @if ($attr->hasOptions() && !empty($attr->options))
                                                <span class="text-xs">
                                                    {{ count($attr->options) }} option{{ count($attr->options) === 1 ? '' : 's' }}
                                                </span>
                                                <div class="text-[11px] text-gray-400 mt-0.5 truncate max-w-xs" title="{{ implode(', ', $attr->options) }}">
                                                    {{ implode(', ', array_slice($attr->options, 0, 5)) }}{{ count($attr->options) > 5 ? ', …' : '' }}
                                                </div>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>

                                        {{-- Flags --}}
                                        <td class="py-3 px-4">
                                            <div class="flex flex-col gap-1">
                                                @if ($attr->is_filterable)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 w-fit">
                                                        <i class="fas fa-filter text-[9px]"></i> Filterable
                                                    </span>
                                                @endif
                                                @if ($attr->is_required)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 w-fit">
                                                        Required
                                                    </span>
                                                @endif
                                                @if (!$attr->is_active)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 w-fit">
                                                        Inactive
                                                    </span>
                                                @endif
                                                @if (!$attr->is_filterable && !$attr->is_required && $attr->is_active)
                                                    <span class="text-xs text-gray-400">—</span>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Actions --}}
                                        <td class="py-3 px-4 text-right">
                                            <div class="flex items-center justify-end gap-1">
                                                <a href="{{ route('admin.attributes.edit', $attr) }}"
                                                    class="text-indigo-600 hover:text-indigo-800 text-sm font-medium px-3 py-1.5 rounded hover:bg-indigo-50 transition">
                                                    Edit
                                                </a>
                                                <form method="POST" action="{{ route('admin.attributes.destroy', $attr) }}"
                                                    onsubmit="return confirm('Delete attribute &quot;{{ $attr->label }}&quot;? Existing product data using this key will remain but be orphaned.')">
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

                    @if ($attributes->hasPages())
                        <div class="px-4 py-3 border-t border-gray-100">
                            {{ $attributes->links() }}
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>
</x-app-layout>