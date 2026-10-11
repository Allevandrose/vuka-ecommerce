@php
    // Define icons for the UI
    $icons = [
        'search' => 'M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z',
        'eye' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z',
        'arrow-left' => 'M10 19l-7-7m0 0l7-7m-7 7h18',
        'info' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'sitemap' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
        'tag' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
        'truck' => 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0',
        'list' => 'M4 6h16M4 10h16M4 14h16M4 18h16',
        'image' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
        'search-seo' => 'M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z',
        'flag' => 'M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9',
        'save' => 'M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4',
        'spinner' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
    ];
@endphp

<x-admin-layout>
    <!-- Load Inter font + FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body, .font-inter {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #F7F8F5; }
        ::-webkit-scrollbar-thumb { background: #C7CFC0; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #9DAF92; }

        /* Floating Back Button animation */
        .floating-back-btn {
            animation: floatIn 0.4s ease-out;
        }
        @keyframes floatIn {
            from { opacity: 0; transform: translate(-50%, 20px); }
            to   { opacity: 1; transform: translate(-50%, 0); }
        }
    </style>

    <!-- Top Header: Light Theme -->
    <x-slot name="header">
        <div class="flex items-center justify-between w-full font-inter">
            <div class="flex items-center gap-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Edit Product') }}
                </h2>
                <span class="text-sm text-[#336A29] font-mono bg-[#EAEF9D]/60 px-2.5 py-1 rounded border border-[#336A29]/20">
                    {{ $product->name }}
                </span>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.products.show', $product) }}"
                    class="text-sm text-gray-600 hover:text-gray-900 inline-flex items-center gap-1.5 transition px-3 py-2 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-eye text-xs"></i> View
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Main Content: Soft Cream Background -->
    <div class="py-10 min-h-screen bg-[#F7F8F5] font-inter pb-32" x-data="productEditForm()" x-init="init()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <p class="text-sm font-semibold text-red-800 mb-1 flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle"></i> Please fix the following:
                    </p>
                    <ul class="text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Meta strip --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl mb-6 border border-[#E5E7DE]">
                <div class="p-4 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-4 text-gray-500 flex-wrap">
                        <span>
                            <i class="fas fa-hashtag text-gray-400"></i>
                            ID: <span class="font-mono text-gray-800">{{ $product->id }}</span>
                        </span>
                        <span>
                            <i class="fas fa-link text-gray-400"></i>
                            <span class="font-mono text-gray-700">{{ $product->slug }}</span>
                        </span>
                        <span>
                            <i class="fas fa-barcode text-gray-400"></i>
                            <span class="font-mono text-gray-700">{{ $product->sku }}</span>
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
                            @if ($product->source_type === 'vendor') bg-amber-100 text-amber-800 border border-amber-200
                            @else bg-blue-100 text-blue-800 border border-blue-200 @endif">
                            <i class="fas {{ $product->source_type === 'vendor' ? 'fa-store' : 'fa-shield-alt' }} text-[9px]"></i>
                            {{ $product->source_type === 'vendor' ? ($product->source?->shop_name ?? 'Vendor') : 'Admin' }}
                        </span>
                    </div>
                    <div class="text-gray-400">
                        Created {{ $product->created_at->format('M d, Y') }}
                        · Updated {{ $product->updated_at->diffForHumans() }}
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.products.update', $product) }}"
                  enctype="multipart/form-data"
                  @submit="submitting = true">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- LEFT --}}
                    <div class="lg:col-span-2 space-y-6">

                        {{-- BASIC INFO --}}
                        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-[#E5E7DE]">
                            <div class="p-6 space-y-5">
                                <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide flex items-center gap-2 border-b border-gray-100 pb-3">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-[#EAEF9D]/60 text-[#336A29]">
                                        <i class="fas fa-info text-xs"></i>
                                    </span>
                                    Basic Information
                                </h3>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="sm:col-span-2">
                                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                            Product Name <span class="text-red-500">*</span>
                                        </label>
                                        <input id="name" name="name" type="text" required
                                            value="{{ old('name', $product->name) }}"
                                            class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-[#336A29] focus:ring-2 focus:ring-[#336A29]/30 text-sm">
                                        <p class="mt-1 text-xs text-gray-500">
                                            Current slug: <span class="font-mono text-gray-700">{{ $product->slug }}</span>
                                            — regenerates on rename.
                                        </p>
                                    </div>

                                    <div>
                                        <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                                        <input id="sku" name="sku" type="text"
                                            value="{{ old('sku', $product->sku) }}"
                                            class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-[#336A29] focus:ring-2 focus:ring-[#336A29]/30 font-mono text-sm">
                                    </div>

                                    <div>
                                        <label for="condition" class="block text-sm font-medium text-gray-700 mb-1">Condition</label>
                                        <select id="condition" name="condition"
                                            class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-[#336A29] focus:ring-2 focus:ring-[#336A29]/30 text-sm">
                                            @foreach (['new', 'used', 'refurbished'] as $c)
                                                <option value="{{ $c }}" {{ old('condition', $product->condition) === $c ? 'selected' : '' }}>
                                                    {{ ucfirst($c) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label for="short_description" class="block text-sm font-medium text-gray-700 mb-1">Short Description</label>
                                        <textarea id="short_description" name="short_description" rows="2" maxlength="500"
                                            class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-[#336A29] focus:ring-2 focus:ring-[#336A29]/30 text-sm">{{ old('short_description', $product->short_description) }}</textarea>
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Full Description</label>
                                        <textarea id="description" name="description" rows="6"
                                            placeholder="Rich description. Basic HTML supported: <p>, <br>, <strong>, <em>, <ul>, <li>, <h2>, <h3>, <a href='...'>."
                                            class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-[#336A29] focus:ring-2 focus:ring-[#336A29]/30 font-mono text-sm">{{ old('description', $product->description ?? '') }}</textarea>
                                        <p class="mt-1 text-xs text-gray-500">
                                            Basic HTML: <code class="bg-gray-100 px-1 rounded">&lt;p&gt;</code>,
                                            <code class="bg-gray-100 px-1 rounded">&lt;br&gt;</code>,
                                            <code class="bg-gray-100 px-1 rounded">&lt;strong&gt;</code>,
                                            <code class="bg-gray-100 px-1 rounded">&lt;em&gt;</code>,
                                            <code class="bg-gray-100 px-1 rounded">&lt;ul&gt;&lt;li&gt;</code>,
                                            <code class="bg-gray-100 px-1 rounded">&lt;h2&gt;</code>,
                                            <code class="bg-gray-100 px-1 rounded">&lt;h3&gt;</code>,
                                            <code class="bg-gray-100 px-1 rounded">&lt;a href="..."&gt;</code>.
                                            Scripts and event handlers are stripped for safety.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- CLASSIFICATION --}}
                        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-[#E5E7DE]">
                            <div class="p-6 space-y-5">
                                <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide flex items-center gap-2 border-b border-gray-100 pb-3">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-[#EAEF9D]/60 text-[#336A29]">
                                        <i class="fas fa-sitemap text-xs"></i>
                                    </span>
                                    Classification
                                </h3>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                                            Category <span class="text-red-500">*</span>
                                        </label>
                                        <select id="category_id" name="category_id" required
                                            x-model="categoryId"
                                            @change="loadAttributes()"
                                            class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-[#336A29] focus:ring-2 focus:ring-[#336A29]/30 text-sm">
                                            <option value="">— Select category —</option>
                                            @foreach ($categories as $cat)
                                                <option value="{{ $cat['id'] }}" {{ (string) old('category_id', $product->category_id) === (string) $cat['id'] ? 'selected' : '' }}>
                                                    {{ $cat['depth'] > 0 ? '↳ ' : '' }}{{ $cat['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="brand_id" class="block text-sm font-medium text-gray-700 mb-1">Brand</label>
                                        <select id="brand_id" name="brand_id"
                                            class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-[#336A29] focus:ring-2 focus:ring-[#336A29]/30 text-sm">
                                            <option value="">— No brand —</option>
                                            @foreach ($brands as $brand)
                                                <option value="{{ $brand->id }}" {{ (string) old('brand_id', $product->brand_id) === (string) $brand->id ? 'selected' : '' }}>
                                                    {{ $brand->name }}{{ $brand->is_verified ? ' ✓' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- PRICING --}}
                        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-[#E5E7DE]">
                            <div class="p-6 space-y-5">
                                <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide flex items-center gap-2 border-b border-gray-100 pb-3">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-[#EAEF9D]/60 text-[#336A29]">
                                        <i class="fas fa-tag text-xs"></i>
                                    </span>
                                    Pricing & Inventory
                                </h3>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1">
                                            Selling Price <span class="text-red-500">*</span>
                                        </label>
                                        <input id="price" name="price" type="number" step="0.01" min="0" required
                                            value="{{ old('price', $product->price) }}"
                                            class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-[#336A29] focus:ring-2 focus:ring-[#336A29]/30 text-sm">
                                    </div>

                                    <div>
                                        <label for="compare_at_price" class="block text-sm font-medium text-gray-700 mb-1">Compare-at Price</label>
                                        <input id="compare_at_price" name="compare_at_price" type="number" step="0.01" min="0"
                                            value="{{ old('compare_at_price', $product->compare_at_price) }}"
                                            class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-[#336A29] focus:ring-2 focus:ring-[#336A29]/30 text-sm">
                                    </div>

                                    <div>
                                        <label for="cost_price" class="block text-sm font-medium text-gray-700 mb-1">
                                            Cost Price <span class="text-gray-400 text-xs">(internal)</span>
                                        </label>
                                        <input id="cost_price" name="cost_price" type="number" step="0.01" min="0"
                                            value="{{ old('cost_price', $product->cost_price) }}"
                                            class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-[#336A29] focus:ring-2 focus:ring-[#336A29]/30 text-sm">
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-1">Stock Quantity</label>
                                        <input id="stock_quantity" name="stock_quantity" type="number" min="0"
                                            value="{{ old('stock_quantity', $product->stock_quantity) }}"
                                            class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-[#336A29] focus:ring-2 focus:ring-[#336A29]/30 text-sm">
                                    </div>

                                    <div>
                                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                                        <select id="currency" name="currency"
                                            class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-[#336A29] focus:ring-2 focus:ring-[#336A29]/30 text-sm">
                                            @foreach (['KES', 'USD', 'EUR', 'GBP'] as $c)
                                                <option value="{{ $c }}" {{ old('currency', $product->currency) === $c ? 'selected' : '' }}>{{ $c }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="flex items-center gap-6 flex-wrap pt-2 border-t border-gray-100">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="hidden" name="track_inventory" value="0">
                                        <input type="checkbox" name="track_inventory" value="1"
                                            {{ old('track_inventory', $product->track_inventory) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-[#336A29] shadow-sm focus:ring-[#336A29]">
                                        <span class="text-sm text-gray-700">Track inventory</span>
                                    </label>

                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="hidden" name="is_local" value="0">
                                        <input type="checkbox" name="is_local" value="1"
                                            {{ old('is_local', $product->is_local) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-[#336A29] shadow-sm focus:ring-[#336A29]">
                                        <span class="text-sm text-gray-700">Local stock</span>
                                    </label>

                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="hidden" name="is_international" value="0">
                                        <input type="checkbox" name="is_international" value="1"
                                            {{ old('is_international', $product->is_international) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-[#336A29] shadow-sm focus:ring-[#336A29]">
                                        <span class="text-sm text-gray-700">International shipping</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- SHIPPING --}}
                        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-[#E5E7DE]"
                             x-data="{ open: {{ $product->weight || $product->length || $product->width || $product->height || $product->lead_time_days ? 'true' : 'false' }} }">
                            <button type="button" @click="open = !open"
                                class="w-full flex items-center justify-between p-6 text-left hover:bg-gray-50 transition">
                                <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide flex items-center gap-2">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-[#EAEF9D]/60 text-[#336A29]">
                                        <i class="fas fa-truck text-xs"></i>
                                    </span>
                                    Shipping Dimensions <span class="text-gray-400 font-normal normal-case">(optional)</span>
                                </h3>
                                <i class="fas fa-chevron-down text-gray-400 transition" :class="open && 'rotate-180'"></i>
                            </button>

                            <div x-show="open" x-cloak class="px-6 pb-6 space-y-4">
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                    <div>
                                        <label for="weight" class="block text-xs font-medium text-gray-600 mb-1">Weight (kg)</label>
                                        <input id="weight" name="weight" type="number" step="0.001" min="0"
                                            value="{{ old('weight', $product->weight) }}"
                                            class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-[#336A29] focus:ring-2 focus:ring-[#336A29]/30 text-sm">
                                    </div>
                                    <div>
                                        <label for="length" class="block text-xs font-medium text-gray-600 mb-1">Length (cm)</label>
                                        <input id="length" name="length" type="number" step="0.01" min="0"
                                            value="{{ old('length', $product->length) }}"
                                            class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-[#336A29] focus:ring-2 focus:ring-[#336A29]/30 text-sm">
                                    </div>
                                    <div>
                                        <label for="width" class="block text-xs font-medium text-gray-600 mb-1">Width (cm)</label>
                                        <input id="width" name="width" type="number" step="0.01" min="0"
                                            value="{{ old('width', $product->width) }}"
                                            class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-[#336A29] focus:ring-2 focus:ring-[#336A29]/30 text-sm">
                                    </div>
                                    <div>
                                        <label for="height" class="block text-xs font-medium text-gray-600 mb-1">Height (cm)</label>
                                        <input id="height" name="height" type="number" step="0.01" min="0"
                                            value="{{ old('height', $product->height) }}"
                                            class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-[#336A29] focus:ring-2 focus:ring-[#336A29]/30 text-sm">
                                    </div>
                                </div>

                                <div>
                                    <label for="lead_time_days" class="block text-xs font-medium text-gray-600 mb-1">Lead time (days)</label>
                                    <input id="lead_time_days" name="lead_time_days" type="number" min="0" max="365"
                                        value="{{ old('lead_time_days', $product->lead_time_days) }}"
                                        class="w-32 rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-[#336A29] focus:ring-2 focus:ring-[#336A29]/30 text-sm">
                                </div>
                            </div>
                        </div>

                        {{-- ATTRIBUTES --}}
                        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-[#E5E7DE]">
                            <div class="p-6 space-y-5">
                                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                                    <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide flex items-center gap-2">
                                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-[#EAEF9D]/60 text-[#336A29]">
                                            <i class="fas fa-list text-xs"></i>
                                        </span>
                                        Product Attributes
                                    </h3>
                                    <span class="text-xs text-gray-400" x-show="attributesLoading" x-cloak>
                                        <i class="fas fa-circle-notch fa-spin"></i> Loading…
                                    </span>
                                </div>

                                <template x-if="categoryId && !attributesLoading && attributes.length === 0">
                                    <div class="text-sm text-gray-500 bg-gray-50 border border-gray-200 rounded-lg p-3">
                                        No attributes defined for this category.
                                    </div>
                                </template>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <template x-for="attr in attributes" :key="attr.id">
                                        <div :class="attr.type === 'textarea' || attr.type === 'multiselect' ? 'sm:col-span-2' : ''">
                                            <label :for="'attr_' + attr.key" class="block text-sm font-medium text-gray-700 mb-1">
                                                <span x-text="attr.label"></span>
                                                <span x-show="attr.is_required" class="text-red-500">*</span>
                                                <span x-show="attr.unit" class="text-gray-400 text-xs" x-text="'(' + attr.unit + ')'"></span>
                                            </label>

                                            <template x-if="attr.type === 'text'">
                                                <input :id="'attr_' + attr.key"
                                                    :name="'attributes[' + attr.key + ']'"
                                                    type="text"
                                                    :value="attrValues[attr.key] || ''"
                                                    class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-[#336A29] focus:ring-2 focus:ring-[#336A29]/30 text-sm">
                                            </template>

                                            <template x-if="attr.type === 'textarea'">
                                                <textarea :id="'attr_' + attr.key"
                                                    :name="'attributes[' + attr.key + ']'"
                                                    rows="3"
                                                    x-text="attrValues[attr.key] || ''"
                                                    class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-[#336A29] focus:ring-2 focus:ring-[#336A29]/30 text-sm"></textarea>
                                            </template>

                                            <template x-if="attr.type === 'number'">
                                                <input :id="'attr_' + attr.key"
                                                    :name="'attributes[' + attr.key + ']'"
                                                    type="number" step="any"
                                                    :value="attrValues[attr.key] ?? ''"
                                                    class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-[#336A29] focus:ring-2 focus:ring-[#336A29]/30 text-sm">
                                            </template>

                                            <template x-if="attr.type === 'select'">
                                                <select :id="'attr_' + attr.key"
                                                    :name="'attributes[' + attr.key + ']'"
                                                    class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-[#336A29] focus:ring-2 focus:ring-[#336A29]/30 text-sm">
                                                    <option value="">— Select —</option>
                                                    <template x-for="opt in attr.options" :key="opt">
                                                        <option :value="opt"
                                                            :selected="attrValues[attr.key] === opt"
                                                            x-text="opt"></option>
                                                    </template>
                                                </select>
                                            </template>

                                            <template x-if="attr.type === 'multiselect'">
                                                <div class="flex flex-wrap gap-2 p-2 border border-gray-200 rounded-lg bg-gray-50">
                                                    <template x-for="opt in attr.options" :key="opt">
                                                        <label class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white border border-gray-200 rounded-full text-xs cursor-pointer hover:bg-[#EAEF9D]/30 text-gray-700">
                                                            <input type="checkbox"
                                                                :name="'attributes[' + attr.key + '][]'"
                                                                :value="opt"
                                                                :checked="(attrValues[attr.key] || []).includes(opt)"
                                                                class="rounded border-gray-300 text-[#336A29] shadow-sm focus:ring-[#336A29]">
                                                            <span x-text="opt"></span>
                                                        </label>
                                                    </template>
                                                </div>
                                            </template>

                                            <template x-if="attr.type === 'boolean'">
                                                <div class="flex items-center gap-4 pt-1">
                                                    <label class="flex items-center gap-2 cursor-pointer">
                                                        <input type="hidden" :name="'attributes[' + attr.key + ']'" value="0">
                                                        <input type="checkbox"
                                                            :name="'attributes[' + attr.key + ']'"
                                                            value="1"
                                                            :checked="attrValues[attr.key] === true || attrValues[attr.key] === 1"
                                                            class="rounded border-gray-300 text-[#336A29] shadow-sm focus:ring-[#336A29]">
                                                        <span class="text-sm text-gray-700">Yes</span>
                                                    </label>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- IMAGES --}}
                        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-[#E5E7DE]">
                            <div class="p-6 space-y-4">
                                <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide flex items-center gap-2 border-b border-gray-100 pb-3">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-[#EAEF9D]/60 text-[#336A29]">
                                        <i class="fas fa-images text-xs"></i>
                                    </span>
                                    Product Images
                                </h3>

                                <p class="text-sm text-gray-500">
                                    Click the trash icon to remove an image (click again to restore). Click the star to set the primary.
                                    Add new images below.
                                </p>

                                <input type="hidden" name="images_submitted" value="1">

                                <div x-show="existingImages.length > 0" x-cloak class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    <template x-for="(img, originalIdx) in existingImages" :key="'img-' + originalIdx">
                                        <div class="relative group rounded-lg overflow-hidden border-2 transition bg-gray-50"
                                            :class="img.removed
                                                ? 'border-red-300'
                                                : (isPrimary('existing', originalIdx) ? 'border-[#336A29] ring-2 ring-[#336A29]/20' : 'border-gray-200')">

                                            <img :src="'/storage/' + img.path"
                                                :alt="img.alt || 'Product image'"
                                                class="w-full h-32 object-cover transition"
                                                :class="img.removed ? 'opacity-40' : ''">

                                            <button type="button"
                                                @click.prevent.stop="removeExisting(originalIdx)"
                                                :class="img.removed ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600'"
                                                class="absolute top-1.5 right-1.5 w-8 h-8 rounded-full text-white flex items-center justify-center shadow-lg z-10 transition"
                                                :title="img.removed ? 'Restore image' : 'Remove image'">
                                                <i class="fas text-sm" :class="img.removed ? 'fa-undo' : 'fa-trash'"></i>
                                            </button>

                                            <button type="button"
                                                x-show="!img.removed"
                                                @click.prevent.stop="setPrimary('existing', originalIdx)"
                                                :class="isPrimary('existing', originalIdx) ? 'bg-[#336A29] text-white' : 'bg-white text-gray-700 hover:bg-[#EAEF9D]'"
                                                class="absolute bottom-1.5 left-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold shadow-md z-10 transition border border-gray-200">
                                                <i class="fas fa-star text-[10px] mr-1"></i>
                                                <span x-text="isPrimary('existing', originalIdx) ? 'Primary' : 'Set primary'"></span>
                                            </button>

                                            <div x-show="img.removed" x-cloak
                                                class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                                <span class="px-2 py-1 rounded bg-red-600 text-white text-[10px] font-semibold uppercase tracking-wide">
                                                    Will be removed
                                                </span>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <div x-show="existingImages.length === 0 && newFiles.length === 0" x-cloak
                                    class="text-sm text-gray-500 bg-gray-50 border border-gray-200 rounded-lg p-3">
                                    No images yet. Upload below.
                                </div>

                                <template x-for="keptIdx in keptIndices()" :key="'kept-' + keptIdx">
                                    <input type="hidden" name="kept_image_indices[]" :value="keptIdx">
                                </template>

                                <input type="hidden" name="primary_image_index" :value="primaryMergedIndex()">

                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center bg-gray-50 hover:bg-[#EAEF9D]/20 transition">
                                    <input type="file"
                                        id="image_files"
                                        name="image_files[]"
                                        accept="image/jpeg,image/jfif,image/png,image/webp"
                                        multiple
                                        x-ref="newFilesInput"
                                        @change="onNewFilesSelected($event)"
                                        class="block w-full text-sm text-gray-600
                                            file:mr-4 file:py-2.5 file:px-5 file:rounded-lg file:border-0
                                            file:text-sm file:font-semibold file:bg-[#336A29] file:text-white
                                            hover:file:bg-[#285022] file:cursor-pointer cursor-pointer">
                                    <p class="text-xs text-gray-400 mt-3">
                                        New images append to the list · JPG/PNG/WebP · max 5MB each
                                    </p>

                                    <div x-show="newFiles.length > 0" x-cloak class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4">
                                        <template x-for="(file, idx) in newFiles" :key="'new-' + file.id">
                                            <div class="relative group rounded-lg overflow-hidden border-2 bg-gray-50"
                                                :class="isPrimary('new', idx) ? 'border-[#336A29] ring-2 ring-[#336A29]/20' : 'border-gray-200'">

                                                <img :src="file.url" :alt="file.name" class="w-full h-32 object-cover">

                                                <button type="button"
                                                    @click.prevent.stop="removeNew(idx)"
                                                    class="absolute top-1.5 right-1.5 w-8 h-8 rounded-full bg-red-500 hover:bg-red-600 text-white flex items-center justify-center shadow-lg z-10 transition"
                                                    title="Remove new image">
                                                    <i class="fas fa-trash text-sm"></i>
                                                </button>

                                                <button type="button"
                                                    @click.prevent.stop="setPrimary('new', idx)"
                                                    :class="isPrimary('new', idx) ? 'bg-[#336A29] text-white' : 'bg-white text-gray-700 hover:bg-[#EAEF9D]'"
                                                    class="absolute bottom-1.5 left-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold shadow-md z-10 transition border border-gray-200">
                                                    <i class="fas fa-star text-[10px] mr-1"></i>
                                                    <span x-text="isPrimary('new', idx) ? 'Primary' : 'Set primary'"></span>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SEO --}}
                        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-[#E5E7DE]"
                             x-data="{ open: {{ $product->meta_title || $product->meta_description || $product->meta_keywords ? 'true' : 'false' }} }">
                            <button type="button" @click="open = !open"
                                class="w-full flex items-center justify-between p-6 text-left hover:bg-gray-50 transition">
                                <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide flex items-center gap-2">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-[#EAEF9D]/60 text-[#336A29]">
                                        <i class="fas fa-search text-xs"></i>
                                    </span>
                                    SEO <span class="text-gray-400 font-normal normal-case">(optional)</span>
                                </h3>
                                <i class="fas fa-chevron-down text-gray-400 transition" :class="open && 'rotate-180'"></i>
                            </button>

                            <div x-show="open" x-cloak class="px-6 pb-6 space-y-4">
                                <div>
                                    <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                                    <input id="meta_title" name="meta_title" type="text" maxlength="255"
                                        value="{{ old('meta_title', $product->meta_title) }}"
                                        class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-[#336A29] focus:ring-2 focus:ring-[#336A29]/30 text-sm">
                                </div>
                                <div>
                                    <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                                    <textarea id="meta_description" name="meta_description" rows="2" maxlength="500"
                                        class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-[#336A29] focus:ring-2 focus:ring-[#336A29]/30 text-sm">{{ old('meta_description', $product->meta_description) }}</textarea>
                                </div>
                                <div>
                                    <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-1">Meta Keywords</label>
                                    <input id="meta_keywords" name="meta_keywords" type="text"
                                        value="{{ old('meta_keywords', $product->meta_keywords) }}"
                                        class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-[#336A29] focus:ring-2 focus:ring-[#336A29]/30 text-sm">
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- RIGHT --}}
                    <div class="lg:col-span-1 space-y-6">

                        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-[#E5E7DE] lg:sticky lg:top-6">
                            <div class="p-6 space-y-5">
                                <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide flex items-center gap-2 border-b border-gray-100 pb-3">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-[#EAEF9D]/60 text-[#336A29]">
                                        <i class="fas fa-flag text-xs"></i>
                                    </span>
                                    Publish
                                </h3>

                                @if ($product->status === 'rejected' && $product->rejection_reason)
                                    <div class="text-xs bg-red-50 border border-red-200 rounded-lg p-3 text-red-800">
                                        <strong>Rejected:</strong> {{ $product->rejection_reason }}
                                    </div>
                                @endif

                                @if ($product->status === 'disabled' && $product->disabled_reason)
                                    <div class="text-xs bg-red-50 border border-red-200 rounded-lg p-3 text-red-800">
                                        <strong>Disabled:</strong> {{ $product->disabled_reason }}
                                    </div>
                                @endif

                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select id="status" name="status"
                                        class="w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-[#336A29] focus:ring-2 focus:ring-[#336A29]/30 text-sm">
                                        @foreach (['draft' => 'Draft', 'active' => 'Active', 'archived' => 'Archived'] as $val => $label)
                                            <option value="{{ $val }}" {{ old('status', $product->status) === $val ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Current: <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $product->status)) }}</span>
                                    </p>
                                </div>

                                <div class="flex flex-col gap-3 pt-2 border-t border-gray-100">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="hidden" name="is_featured" value="0">
                                        <input type="checkbox" name="is_featured" value="1"
                                            {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-yellow-500 shadow-sm focus:ring-yellow-500">
                                        <span class="text-sm text-gray-700 flex items-center gap-1">
                                            <i class="fas fa-star text-yellow-500 text-xs"></i> Feature on homepage
                                        </span>
                                    </label>

                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="hidden" name="is_verified" value="0">
                                        <input type="checkbox" name="is_verified" value="1"
                                            {{ old('is_verified', $product->is_verified) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-blue-500 shadow-sm focus:ring-blue-500">
                                        <span class="text-sm text-gray-700 flex items-center gap-1">
                                            <i class="fas fa-shield-alt text-blue-500 text-xs"></i> Verified product
                                        </span>
                                    </label>

                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="hidden" name="is_certified" value="0">
                                        <input type="checkbox" name="is_certified" value="1"
                                            {{ old('is_certified', $product->is_certified) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-green-500 shadow-sm focus:ring-green-500">
                                        <span class="text-sm text-gray-700 flex items-center gap-1">
                                            <i class="fas fa-certificate text-green-500 text-xs"></i> Certified
                                        </span>
                                    </label>
                                </div>

                                <button type="submit"
                                    :disabled="submitting"
                                    class="w-full inline-flex items-center justify-center gap-2 bg-[#336A29] text-white px-6 py-3 rounded-lg hover:bg-[#285022] transition text-sm font-semibold disabled:opacity-60">
                                    <span x-show="!submitting"><i class="fas fa-save mr-1"></i> Update Product</span>
                                    <span x-show="submitting" x-cloak class="flex items-center gap-2">
                                        <i class="fas fa-circle-notch fa-spin"></i> Saving…
                                    </span>
                                </button>

                                <a href="{{ route('admin.products.index') }}"
                                    class="block text-center text-sm text-gray-500 hover:text-gray-900 transition">
                                    Cancel
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function productEditForm() {
            return {
                categoryId: @json(old('category_id', $product->category_id)),
                attributes: @json($attributes ?? []),
                attrValues: @json($attributeValues ?? []),
                attributesLoading: false,
                submitting: false,

                existingImages: @json($product->images->map(function($img, $idx) {
                    return ['path' => $img->path, 'alt' => $img->alt ?? '', 'removed' => false];
                })->values()->all()),

                newFiles: [],
                primaryType: 'existing',
                primaryIndex: {{ $product->images->search(fn($img) => $img->is_primary) !== false ? $product->images->search(fn($img) => $img->is_primary) : 0 }},

                init() {
                    if (this.categoryId) {
                        this.loadAttributes();
                    }
                },

                async loadAttributes() {
                    if (!this.categoryId) {
                        this.attributes = [];
                        return;
                    }

                    this.attributesLoading = true;

                    try {
                        const res = await fetch(
                            '{{ url("/admin/attributes/json") }}?category_id=' + encodeURIComponent(this.categoryId),
                            { headers: { 'Accept': 'application/json' } }
                        );

                        if (!res.ok) throw new Error('HTTP ' + res.status);

                        const data = await res.json();
                        this.attributes = data.attributes || [];
                    } catch (e) {
                        console.error('Failed to load attributes:', e);
                        this.attributes = [];
                    } finally {
                        this.attributesLoading = false;
                    }
                },

                isPrimary(type, idx) {
                    return this.primaryType === type && this.primaryIndex === idx;
                },

                setPrimary(type, idx) {
                    this.primaryType = type;
                    this.primaryIndex = idx;
                },

                removeExisting(idx) {
                    this.existingImages[idx].removed = !this.existingImages[idx].removed;
                },

                removeNew(idx) {
                    this.newFiles.splice(idx, 1);
                    if (this.primaryType === 'new' && this.primaryIndex === idx) {
                        this.primaryType = 'existing';
                        this.primaryIndex = 0;
                    }
                },

                onNewFilesSelected(event) {
                    const files = Array.from(event.target.files);
                    files.forEach(file => {
                        if (file.size > 5 * 1024 * 1024) return;
                        this.newFiles.push({
                            id: Date.now() + Math.random(),
                            file: file,
                            name: file.name,
                            url: URL.createObjectURL(file)
                        });
                    });
                    event.target.value = '';
                },

                keptIndices() {
                    return this.existingImages
                        .map((img, idx) => img.removed ? -1 : idx)
                        .filter(idx => idx >= 0);
                },

                primaryMergedIndex() {
                    if (this.primaryType === 'existing') {
                        const kept = this.keptIndices();
                        const pos = kept.indexOf(this.primaryIndex);
                        return pos >= 0 ? pos : 0;
                    }
                    return this.keptIndices().length + this.primaryIndex;
                },
            }
        }
    </script>
</x-admin-layout>