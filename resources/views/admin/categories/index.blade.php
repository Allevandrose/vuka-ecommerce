<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Category Management') }}
            </h2>
            <a href="{{ route('admin.categories.create') }}"
                class="inline-flex items-center gap-2 bg-[#1E3C2C] text-white px-4 py-2 rounded-lg hover:bg-[#143023] transition text-sm font-semibold">
                + New Category
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash messages --}}
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

            @if ($categories->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <p class="text-gray-500 mb-4">No categories yet.</p>
                        <a href="{{ route('admin.categories.create') }}"
                            class="inline-flex items-center gap-2 bg-[#1E3C2C] text-white px-4 py-2 rounded-lg hover:bg-[#143023] transition text-sm font-semibold">
                            Create your first category
                        </a>
                    </div>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($categories as $category)
                        {{-- Top-level card --}}
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                            {{-- Top-level row --}}
                            <div class="p-4 sm:p-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-4">

                                    {{-- Swatch --}}
                                    <div class="shrink-0 w-14 h-14 rounded-xl border border-gray-200 flex items-center justify-center text-white text-lg font-semibold shadow-sm"
                                        style="background: {{ $category->heroBackground() }};">
                                        @if ($category->icon)
                                            <i class="fas {{ $category->icon }}"></i>
                                        @else
                                            {{ substr($category->name, 0, 1) }}
                                        @endif
                                    </div>

                                    {{-- Info --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h3 class="font-semibold text-lg text-gray-900">{{ $category->name }}</h3>
                                            @if (!$category->is_active)
                                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactive</span>
                                            @endif
                                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                                theme: {{ $category->theme }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-0.5">
                                            <span class="font-mono text-xs bg-gray-100 px-1.5 py-0.5 rounded">{{ $category->slug }}</span>
                                            <span class="mx-2">·</span>
                                            sort: {{ $category->sort_order }}
                                            <span class="mx-2">·</span>
                                            {{ $category->children->count() }} subcategor{{ $category->children->count() === 1 ? 'y' : 'ies' }}
                                        </p>
                                    </div>

                                    {{-- Actions --}}
                                    <div class="flex items-center gap-2 shrink-0">
                                        <a href="{{ route('admin.categories.edit', $category) }}"
                                            class="text-indigo-600 hover:text-indigo-800 text-sm font-medium px-3 py-1.5">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                            onsubmit="return confirm('Delete this category? Subcategories will be promoted to top-level.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-800 text-sm font-medium px-3 py-1.5">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Children --}}
                            @if ($category->children->isNotEmpty())
                                <div class="divide-y divide-gray-100">
                                    @foreach ($category->children as $child)
                                        <div class="px-4 sm:px-5 py-3 flex items-center gap-4 hover:bg-gray-50 transition">
                                            {{-- Indent + connector --}}
                                            <div class="shrink-0 w-8 flex justify-end">
                                                <span class="text-gray-300">↳</span>
                                            </div>

                                            {{-- Child swatch --}}
                                            <div class="shrink-0 w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-xs text-white"
                                                style="background: {{ $child->heroBackground() }};">
                                                @if ($child->icon)
                                                    <i class="fas {{ $child->icon }}"></i>
                                                @else
                                                    {{ substr($child->name, 0, 1) }}
                                                @endif
                                            </div>

                                            {{-- Child info --}}
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="font-medium text-gray-900">{{ $child->name }}</span>
                                                    @if (!$child->is_active)
                                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactive</span>
                                                    @endif
                                                </div>
                                                <p class="text-xs text-gray-500 mt-0.5">
                                                    <span class="font-mono bg-gray-100 px-1.5 py-0.5 rounded">{{ $child->slug }}</span>
                                                    <span class="mx-2">·</span>
                                                    sort: {{ $child->sort_order }}
                                                </p>
                                            </div>

                                            {{-- Child actions --}}
                                            <div class="flex items-center gap-2 shrink-0">
                                                <a href="{{ route('admin.categories.edit', $child) }}"
                                                    class="text-indigo-600 hover:text-indigo-800 text-xs font-medium px-2 py-1">
                                                    Edit
                                                </a>
                                                <form method="POST" action="{{ route('admin.categories.destroy', $child) }}"
                                                    onsubmit="return confirm('Delete this subcategory?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-800 text-xs font-medium px-2 py-1">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="px-5 py-4 text-sm text-gray-500">
                                    <em>No subcategories yet.</em>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>