<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create Product') }}
            </h2>
            <a href="{{ route('admin.products.index') }}"
                class="text-sm text-indigo-600 hover:text-indigo-800 inline-flex items-center gap-1">
                <i class="fas fa-arrow-left text-xs"></i> Back to Products
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="productForm()" x-init="init()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm font-medium text-red-800 mb-1 flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle"></i> Please fix the following:
                    </p>
                    <ul class="text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.products.store') }}"
                  enctype="multipart/form-data"
                  @submit="submitting = true">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- LEFT: MAIN FORM --}}
                    <div class="lg:col-span-2 space-y-6">

                        {{-- BASIC INFO --}}
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 space-y-5">
                                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide flex items-center gap-2">
                                    <i class="fas fa-info-circle text-gray-400"></i> Basic Information
                                </h3>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="sm:col-span-2">
                                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                            Product Name <span class="text-red-500">*</span>
                                        </label>
                                        <input id="name" name="name" type="text" required
                                            x-model="name"
                                            value="{{ old('name') }}"
                                            placeholder="e.g. Samsung Galaxy S24 Ultra 256GB"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <p class="mt-1 text-xs text-gray-500">
                                            Slug: <span class="font-mono" x-text="slug || '...'"></span>
                                        </p>
                                    </div>

                                    <div>
                                        <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">
                                            SKU <span class="text-gray-400 text-xs">(auto if blank)</span>
                                        </label>
                                        <input id="sku" name="sku" type="text"
                                            value="{{ old('sku') }}"
                                            placeholder="Leave blank to auto-generate"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm">
                                    </div>

                                    <div>
                                        <label for="condition" class="block text-sm font-medium text-gray-700 mb-1">
                                            Condition
                                        </label>
                                        <select id="condition" name="condition"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="new" {{ old('condition', 'new') === 'new' ? 'selected' : '' }}>New</option>
                                            <option value="used" {{ old('condition') === 'used' ? 'selected' : '' }}>Used</option>
                                            <option value="refurbished" {{ old('condition') === 'refurbished' ? 'selected' : '' }}>Refurbished</option>
                                        </select>
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label for="short_description" class="block text-sm font-medium text-gray-700 mb-1">
                                            Short Description <span class="text-gray-400 text-xs">(max 500)</span>
                                        </label>
                                        <textarea id="short_description" name="short_description" rows="2" maxlength="500"
                                            placeholder="One or two sentences. Shown on product cards."
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('short_description') }}</textarea>
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                            Full Description
                                        </label>
                                        <textarea id="description" name="description" rows="6"
                                            placeholder="Rich product description. HTML allowed."
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm">{{ old('description') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- CLASSIFICATION --}}
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 space-y-5">
                                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide flex items-center gap-2">
                                    <i class="fas fa-sitemap text-gray-400"></i> Classification
                                </h3>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                                            Category <span class="text-red-500">*</span>
                                        </label>
                                        <select id="category_id" name="category_id" required
                                            x-model="categoryId"
                                            @change="loadAttributes()"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">— Select category —</option>
                                            @foreach ($categories as $cat)
                                                <option value="{{ $cat['id'] }}" {{ old('category_id') == $cat['id'] ? 'selected' : '' }}>
                                                    {{ $cat['depth'] > 0 ? '↳ ' : '' }}{{ $cat['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="brand_id" class="block text-sm font-medium text-gray-700 mb-1">
                                            Brand
                                        </label>
                                        <select id="brand_id" name="brand_id"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">— No brand —</option>
                                            @foreach ($brands as $brand)
                                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                                    {{ $brand->name }}{{ $brand->is_verified ? ' ✓' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- PRICING & INVENTORY --}}
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 space-y-5">
                                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide flex items-center gap-2">
                                    <i class="fas fa-tag text-gray-400"></i> Pricing & Inventory
                                </h3>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1">
                                            Selling Price <span class="text-red-500">*</span>
                                        </label>
                                        <input id="price" name="price" type="number" step="0.01" min="0" required
                                            value="{{ old('price') }}"
                                            placeholder="0.00"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    <div>
                                        <label for="compare_at_price" class="block text-sm font-medium text-gray-700 mb-1">
                                            Compare-at Price
                                        </label>
                                        <input id="compare_at_price" name="compare_at_price" type="number" step="0.01" min="0"
                                            value="{{ old('compare_at_price') }}"
                                            placeholder="0.00"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    <div>
                                        <label for="cost_price" class="block text-sm font-medium text-gray-700 mb-1">
                                            Cost Price <span class="text-gray-400 text-xs">(internal)</span>
                                        </label>
                                        <input id="cost_price" name="cost_price" type="number" step="0.01" min="0"
                                            value="{{ old('cost_price') }}"
                                            placeholder="0.00"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-1">
                                            Stock Quantity
                                        </label>
                                        <input id="stock_quantity" name="stock_quantity" type="number" min="0"
                                            value="{{ old('stock_quantity', 0) }}"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    <div>
                                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">
                                            Currency
                                        </label>
                                        <select id="currency" name="currency"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="KES" {{ old('currency', 'KES') === 'KES' ? 'selected' : '' }}>KES</option>
                                            <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>USD</option>
                                            <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>EUR</option>
                                            <option value="GBP" {{ old('currency') === 'GBP' ? 'selected' : '' }}>GBP</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="flex items-center gap-6 flex-wrap pt-2 border-t border-gray-100">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="hidden" name="track_inventory" value="0">
                                        <input type="checkbox" name="track_inventory" value="1"
                                            {{ old('track_inventory', true) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="text-sm text-gray-700">Track inventory</span>
                                    </label>

                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="hidden" name="is_local" value="0">
                                        <input type="checkbox" name="is_local" value="1"
                                            {{ old('is_local', true) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="text-sm text-gray-700">Local stock</span>
                                    </label>

                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="hidden" name="is_international" value="0">
                                        <input type="checkbox" name="is_international" value="1"
                                            {{ old('is_international') ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="text-sm text-gray-700">International shipping</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- SHIPPING --}}
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                             x-data="{ open: false }">
                            <button type="button" @click="open = !open"
                                class="w-full flex items-center justify-between p-6 text-left hover:bg-gray-50 transition">
                                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide flex items-center gap-2">
                                    <i class="fas fa-truck text-gray-400"></i> Shipping Dimensions <span class="text-gray-400 font-normal normal-case">(optional)</span>
                                </h3>
                                <i class="fas fa-chevron-down text-gray-400 transition" :class="open && 'rotate-180'"></i>
                            </button>

                            <div x-show="open" x-cloak class="px-6 pb-6 space-y-4">
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                    <div>
                                        <label for="weight" class="block text-xs font-medium text-gray-700 mb-1">Weight (kg)</label>
                                        <input id="weight" name="weight" type="number" step="0.001" min="0"
                                            value="{{ old('weight') }}"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    </div>
                                    <div>
                                        <label for="length" class="block text-xs font-medium text-gray-700 mb-1">Length (cm)</label>
                                        <input id="length" name="length" type="number" step="0.01" min="0"
                                            value="{{ old('length') }}"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    </div>
                                    <div>
                                        <label for="width" class="block text-xs font-medium text-gray-700 mb-1">Width (cm)</label>
                                        <input id="width" name="width" type="number" step="0.01" min="0"
                                            value="{{ old('width') }}"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    </div>
                                    <div>
                                        <label for="height" class="block text-xs font-medium text-gray-700 mb-1">Height (cm)</label>
                                        <input id="height" name="height" type="number" step="0.01" min="0"
                                            value="{{ old('height') }}"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    </div>
                                </div>

                                <div>
                                    <label for="lead_time_days" class="block text-xs font-medium text-gray-700 mb-1">
                                        Lead time (days) — for international/backorder
                                    </label>
                                    <input id="lead_time_days" name="lead_time_days" type="number" min="0" max="365"
                                        value="{{ old('lead_time_days') }}"
                                        placeholder="e.g. 14"
                                        class="w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>
                            </div>
                        </div>

                        {{-- DYNAMIC ATTRIBUTES --}}
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 space-y-5">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide flex items-center gap-2">
                                        <i class="fas fa-list-ul text-gray-400"></i> Product Attributes
                                    </h3>
                                    <span class="text-xs text-gray-400" x-show="attributesLoading" x-cloak>
                                        <i class="fas fa-circle-notch fa-spin"></i> Loading…
                                    </span>
                                </div>

                                <template x-if="!categoryId">
                                    <div class="text-sm text-gray-500 bg-blue-50 border border-blue-200 rounded-lg p-3">
                                        <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                                        Select a category above to load its attributes.
                                    </div>
                                </template>

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
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            </template>

                                            <template x-if="attr.type === 'textarea'">
                                                <textarea :id="'attr_' + attr.key"
                                                    :name="'attributes[' + attr.key + ']'"
                                                    rows="3"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                            </template>

                                            <template x-if="attr.type === 'number'">
                                                <input :id="'attr_' + attr.key"
                                                    :name="'attributes[' + attr.key + ']'"
                                                    type="number" step="any"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            </template>

                                            <template x-if="attr.type === 'select'">
                                                <select :id="'attr_' + attr.key"
                                                    :name="'attributes[' + attr.key + ']'"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                    <option value="">— Select —</option>
                                                    <template x-for="opt in attr.options" :key="opt">
                                                        <option :value="opt" x-text="opt"></option>
                                                    </template>
                                                </select>
                                            </template>

                                            <template x-if="attr.type === 'multiselect'">
                                                <div class="flex flex-wrap gap-2 p-2 border border-gray-200 rounded-md">
                                                    <template x-for="opt in attr.options" :key="opt">
                                                        <label class="inline-flex items-center gap-1.5 px-2 py-1 bg-white border border-gray-200 rounded-full text-xs cursor-pointer hover:bg-gray-50">
                                                            <input type="checkbox"
                                                                :name="'attributes[' + attr.key + '][]'"
                                                                :value="opt"
                                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
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
                                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
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
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 space-y-4">
                                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide flex items-center gap-2">
                                    <i class="fas fa-images text-gray-400"></i> Product Images
                                </h3>

                                <p class="text-sm text-gray-500">
                                    Select up to 8 images. The <strong>first image</strong> becomes the primary.
                                    You can reorder and set a different primary later from the edit page.
                                </p>

                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center bg-gray-50">
                                    <input type="file"
                                        id="image_files"
                                        name="image_files[]"
                                        accept="image/jpeg,image/png,image/webp"
                                        multiple
                                        class="block w-full text-sm text-gray-600
                                            file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                                            file:text-sm file:font-semibold file:bg-[#1E3C2C] file:text-white
                                            hover:file:bg-[#143023] file:cursor-pointer cursor-pointer">

                                    <p class="text-xs text-gray-400 mt-3">
                                        JPG, PNG, WebP · up to 5MB each · max 8 images
                                    </p>
                                </div>

                                <input type="hidden" name="primary_image_index" value="0">
                            </div>
                        </div>

                        {{-- SEO --}}
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                             x-data="{ open: false }">
                            <button type="button" @click="open = !open"
                                class="w-full flex items-center justify-between p-6 text-left hover:bg-gray-50 transition">
                                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide flex items-center gap-2">
                                    <i class="fas fa-search text-gray-400"></i> SEO <span class="text-gray-400 font-normal normal-case">(optional)</span>
                                </h3>
                                <i class="fas fa-chevron-down text-gray-400 transition" :class="open && 'rotate-180'"></i>
                            </button>

                            <div x-show="open" x-cloak class="px-6 pb-6 space-y-4">
                                <div>
                                    <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                                    <input id="meta_title" name="meta_title" type="text" maxlength="255"
                                        value="{{ old('meta_title') }}"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                                    <textarea id="meta_description" name="meta_description" rows="2" maxlength="500"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('meta_description') }}</textarea>
                                </div>
                                <div>
                                    <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-1">Meta Keywords</label>
                                    <input id="meta_keywords" name="meta_keywords" type="text"
                                        value="{{ old('meta_keywords') }}"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- RIGHT: SIDEBAR --}}
                    <div class="lg:col-span-1 space-y-6">

                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 space-y-5">
                                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide flex items-center gap-2">
                                    <i class="fas fa-flag text-gray-400"></i> Publish
                                </h3>

                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                        Status
                                    </label>
                                    <select id="status" name="status"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>
                                            Draft — save without publishing
                                        </option>
                                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>
                                            Active — publish immediately
                                        </option>
                                    </select>
                                </div>

                                <div class="flex flex-col gap-3 pt-2 border-t border-gray-100">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="hidden" name="is_featured" value="0">
                                        <input type="checkbox" name="is_featured" value="1"
                                            {{ old('is_featured') ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-yellow-500 shadow-sm focus:ring-yellow-500">
                                        <span class="text-sm text-gray-700 flex items-center gap-1">
                                            <i class="fas fa-star text-yellow-500 text-xs"></i> Feature on homepage
                                        </span>
                                    </label>

                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="hidden" name="is_verified" value="0">
                                        <input type="checkbox" name="is_verified" value="1"
                                            {{ old('is_verified') ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-blue-500 shadow-sm focus:ring-blue-500">
                                        <span class="text-sm text-gray-700 flex items-center gap-1">
                                            <i class="fas fa-shield-alt text-blue-500 text-xs"></i> Verified product
                                        </span>
                                    </label>

                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="hidden" name="is_certified" value="0">
                                        <input type="checkbox" name="is_certified" value="1"
                                            {{ old('is_certified') ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-green-500 shadow-sm focus:ring-green-500">
                                        <span class="text-sm text-gray-700 flex items-center gap-1">
                                            <i class="fas fa-certificate text-green-500 text-xs"></i> Certified
                                        </span>
                                    </label>
                                </div>

                                <button type="submit"
                                    :disabled="submitting"
                                    class="w-full inline-flex items-center justify-center gap-2 bg-[#1E3C2C] text-white px-6 py-3 rounded-lg hover:bg-[#143023] transition text-sm font-semibold disabled:opacity-60">
                                    <span x-show="!submitting"><i class="fas fa-save mr-1"></i> Create Product</span>
                                    <span x-show="submitting" x-cloak class="flex items-center gap-2">
                                        <i class="fas fa-circle-notch fa-spin"></i> Saving…
                                    </span>
                                </button>

                                <a href="{{ route('admin.products.index') }}"
                                    class="block text-center text-sm text-gray-500 hover:text-gray-900">
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
        function productForm() {
            return {
                name: @json(old('name', '')),
                categoryId: @json(old('category_id', '')),
                attributes: [],
                attributesLoading: false,
                submitting: false,

                init() {
                    if (this.categoryId) {
                        this.loadAttributes();
                    }
                },

                get slug() {
                    return this.name
                        .toLowerCase()
                        .trim()
                        .replace(/[^a-z0-9\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-');
                },

                async loadAttributes() {
                    if (!this.categoryId) {
                        this.attributes = [];
                        return;
                    }

                    this.attributesLoading = true;

                    try {
                        const res = await fetch(
                            '{{ url('/admin/attributes/json') }}?category_id=' + encodeURIComponent(this.categoryId),
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
            }
        }
    </script>
</x-app-layout>