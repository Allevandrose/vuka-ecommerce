<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Edit Product') }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $product->name }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.products.show', $product) }}"
                    class="text-sm text-gray-600 hover:text-gray-900 inline-flex items-center gap-1">
                    <i class="fas fa-eye text-xs"></i> View
                </a>
                <a href="{{ route('admin.products.index') }}"
                    class="text-sm text-indigo-600 hover:text-indigo-800 inline-flex items-center gap-1">
                    <i class="fas fa-arrow-left text-xs"></i> Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="productEditForm()">
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

            {{-- Meta strip --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-4 text-gray-500 flex-wrap">
                        <span>
                            <i class="fas fa-hashtag text-gray-400"></i>
                            ID: <span class="font-mono">{{ $product->id }}</span>
                        </span>
                        <span>
                            <i class="fas fa-link text-gray-400"></i>
                            <span class="font-mono">{{ $product->slug }}</span>
                        </span>
                        <span>
                            <i class="fas fa-barcode text-gray-400"></i>
                            <span class="font-mono">{{ $product->sku }}</span>
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
                            @if ($product->source_type === 'vendor') bg-amber-100 text-amber-800
                            @else bg-blue-100 text-blue-800 @endif">
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
                                            value="{{ old('name', $product->name) }}"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <p class="mt-1 text-xs text-gray-500">
                                            Current slug: <span class="font-mono">{{ $product->slug }}</span>
                                            — regenerates on rename.
                                        </p>
                                    </div>

                                    <div>
                                        <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                                        <input id="sku" name="sku" type="text"
                                            value="{{ old('sku', $product->sku) }}"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm">
                                    </div>

                                    <div>
                                        <label for="condition" class="block text-sm font-medium text-gray-700 mb-1">Condition</label>
                                        <select id="condition" name="condition"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('short_description', $product->short_description) }}</textarea>
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Full Description</label>
                                        <textarea id="description" name="description" rows="6"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm">{{ old('description', $product->description) }}</textarea>
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
                                                <option value="{{ $cat['id'] }}" {{ (string) old('category_id', $product->category_id) === (string) $cat['id'] ? 'selected' : '' }}>
                                                    {{ $cat['depth'] > 0 ? '↳ ' : '' }}{{ $cat['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="brand_id" class="block text-sm font-medium text-gray-700 mb-1">Brand</label>
                                        <select id="brand_id" name="brand_id"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                                            value="{{ old('price', $product->price) }}"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    <div>
                                        <label for="compare_at_price" class="block text-sm font-medium text-gray-700 mb-1">Compare-at Price</label>
                                        <input id="compare_at_price" name="compare_at_price" type="number" step="0.01" min="0"
                                            value="{{ old('compare_at_price', $product->compare_at_price) }}"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    <div>
                                        <label for="cost_price" class="block text-sm font-medium text-gray-700 mb-1">
                                            Cost Price <span class="text-gray-400 text-xs">(internal)</span>
                                        </label>
                                        <input id="cost_price" name="cost_price" type="number" step="0.01" min="0"
                                            value="{{ old('cost_price', $product->cost_price) }}"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-1">Stock Quantity</label>
                                        <input id="stock_quantity" name="stock_quantity" type="number" min="0"
                                            value="{{ old('stock_quantity', $product->stock_quantity) }}"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    <div>
                                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                                        <select id="currency" name="currency"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="text-sm text-gray-700">Track inventory</span>
                                    </label>

                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="hidden" name="is_local" value="0">
                                        <input type="checkbox" name="is_local" value="1"
                                            {{ old('is_local', $product->is_local) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="text-sm text-gray-700">Local stock</span>
                                    </label>

                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="hidden" name="is_international" value="0">
                                        <input type="checkbox" name="is_international" value="1"
                                            {{ old('is_international', $product->is_international) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="text-sm text-gray-700">International shipping</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- SHIPPING --}}
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                             x-data="{ open: {{ $product->weight || $product->length || $product->width || $product->height || $product->lead_time_days ? 'true' : 'false' }} }">
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
                                            value="{{ old('weight', $product->weight) }}"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    </div>
                                    <div>
                                        <label for="length" class="block text-xs font-medium text-gray-700 mb-1">Length (cm)</label>
                                        <input id="length" name="length" type="number" step="0.01" min="0"
                                            value="{{ old('length', $product->length) }}"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    </div>
                                    <div>
                                        <label for="width" class="block text-xs font-medium text-gray-700 mb-1">Width (cm)</label>
                                        <input id="width" name="width" type="number" step="0.01" min="0"
                                            value="{{ old('width', $product->width) }}"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    </div>
                                    <div>
                                        <label for="height" class="block text-xs font-medium text-gray-700 mb-1">Height (cm)</label>
                                        <input id="height" name="height" type="number" step="0.01" min="0"
                                            value="{{ old('height', $product->height) }}"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    </div>
                                </div>

                                <div>
                                    <label for="lead_time_days" class="block text-xs font-medium text-gray-700 mb-1">Lead time (days)</label>
                                    <input id="lead_time_days" name="lead_time_days" type="number" min="0" max="365"
                                        value="{{ old('lead_time_days', $product->lead_time_days) }}"
                                        class="w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>
                            </div>
                        </div>

                        {{-- ATTRIBUTES --}}
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
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            </template>

                                            <template x-if="attr.type === 'textarea'">
                                                <textarea :id="'attr_' + attr.key"
                                                    :name="'attributes[' + attr.key + ']'"
                                                    rows="3"
                                                    x-text="attrValues[attr.key] || ''"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                            </template>

                                            <template x-if="attr.type === 'number'">
                                                <input :id="'attr_' + attr.key"
                                                    :name="'attributes[' + attr.key + ']'"
                                                    type="number" step="any"
                                                    :value="attrValues[attr.key] ?? ''"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            </template>

                                            <template x-if="attr.type === 'select'">
                                                <select :id="'attr_' + attr.key"
                                                    :name="'attributes[' + attr.key + ']'"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                    <option value="">— Select —</option>
                                                    <template x-for="opt in attr.options" :key="opt">
                                                        <option :value="opt"
                                                            :selected="attrValues[attr.key] === opt"
                                                            x-text="opt"></option>
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
                                                                :checked="(attrValues[attr.key] || []).includes(opt)"
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
                                                            :checked="attrValues[attr.key] === true || attrValues[attr.key] === 1"
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
                                    Click the trash icon to remove an image (click again to restore). Click the star to set the primary.
                                    Add new images below.
                                </p>

                                {{-- Marker so the controller knows the images section was rendered --}}
                                <input type="hidden" name="images_submitted" value="1">

                                {{-- Existing images --}}
                                <div x-show="existingImages.length > 0" x-cloak class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    <template x-for="(img, originalIdx) in existingImages" :key="'img-' + originalIdx">
                                        <div class="relative group rounded-lg overflow-hidden border-2 transition"
                                            :class="img.removed
                                                ? 'border-red-300'
                                                : (isPrimary('existing', originalIdx) ? 'border-indigo-500 ring-2 ring-indigo-200' : 'border-gray-200')">

                                            <img :src="'/storage/' + img.path"
                                                :alt="img.alt || 'Product image'"
                                                class="w-full h-32 object-cover transition"
                                                :class="img.removed ? 'opacity-40' : ''">

                                            <button type="button"
                                                @click.prevent.stop="removeExisting(originalIdx)"
                                                :class="img.removed ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600'"
                                                class="absolute top-1 right-1 w-7 h-7 rounded-full text-white flex items-center justify-center shadow-md z-10"
                                                :title="img.removed ? 'Restore image' : 'Remove image'">
                                                <i class="fas text-[11px]"
                                                    :class="img.removed ? 'fa-undo' : 'fa-trash'"></i>
                                            </button>

                                            <button type="button"
                                                x-show="!img.removed"
                                                @click.prevent.stop="setPrimary('existing', originalIdx)"
                                                :class="isPrimary('existing', originalIdx) ? 'bg-indigo-600 text-white' : 'bg-white/90 text-gray-700'"
                                                class="absolute bottom-1 left-1 px-2 py-0.5 rounded-full text-[10px] font-semibold shadow-sm z-10">
                                                <i class="fas fa-star text-[9px] mr-0.5"></i>
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

                                {{-- Indices of existing images that are kept (non-removed) --}}
                                <template x-for="keptIdx in keptIndices()" :key="'kept-' + keptIdx">
                                    <input type="hidden" name="kept_image_indices[]" :value="keptIdx">
                                </template>

                                <input type="hidden" name="primary_image_index" :value="primaryMergedIndex()">

                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center bg-gray-50">
                                    <input type="file"
                                        id="image_files"
                                        name="image_files[]"
                                        accept="image/jpeg,image/jfif,image/png,image/webp"
                                        multiple
                                        x-ref="newFilesInput"
                                        @change="onNewFilesSelected($event)"
                                        class="block w-full text-sm text-gray-600
                                            file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                                            file:text-sm file:font-semibold file:bg-[#1E3C2C] file:text-white
                                            hover:file:bg-[#143023] file:cursor-pointer cursor-pointer">
                                    <p class="text-xs text-gray-400 mt-3">
                                        New images append to the list · JPG/PNG/WebP · max 5MB each
                                    </p>

                                    <div x-show="newFiles.length > 0" x-cloak class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4">
                                        <template x-for="(file, idx) in newFiles" :key="'new-' + file.id">
                                            <div class="relative group rounded-lg overflow-hidden border-2"
                                                :class="isPrimary('new', idx) ? 'border-indigo-500 ring-2 ring-indigo-200' : 'border-gray-200'">

                                                <img :src="file.url" :alt="file.name" class="w-full h-32 object-cover">

                                                <button type="button"
                                                    @click.prevent.stop="removeNew(idx)"
                                                    class="absolute top-1 right-1 w-7 h-7 rounded-full bg-red-500 hover:bg-red-600 text-white flex items-center justify-center shadow-md z-10"
                                                    title="Remove new image">
                                                    <i class="fas fa-trash text-[11px]"></i>
                                                </button>

                                                <button type="button"
                                                    @click.prevent.stop="setPrimary('new', idx)"
                                                    :class="isPrimary('new', idx) ? 'bg-indigo-600 text-white' : 'bg-white/90 text-gray-700'"
                                                    class="absolute bottom-1 left-1 px-2 py-0.5 rounded-full text-[10px] font-semibold shadow-sm z-10">
                                                    <i class="fas fa-star text-[9px] mr-0.5"></i>
                                                    <span x-text="isPrimary('new', idx) ? 'Primary' : 'Set primary'"></span>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SEO --}}
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                             x-data="{ open: {{ $product->meta_title || $product->meta_description || $product->meta_keywords ? 'true' : 'false' }} }">
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
                                        value="{{ old('meta_title', $product->meta_title) }}"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                                    <textarea id="meta_description" name="meta_description" rows="2" maxlength="500"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('meta_description', $product->meta_description) }}</textarea>
                                </div>
                                <div>
                                    <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-1">Meta Keywords</label>
                                    <input id="meta_keywords" name="meta_keywords" type="text"
                                        value="{{ old('meta_keywords', $product->meta_keywords) }}"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- RIGHT --}}
                    <div class="lg:col-span-1 space-y-6">

                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 space-y-5">
                                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide flex items-center gap-2">
                                    <i class="fas fa-flag text-gray-400"></i> Publish
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
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                                            <i class="fas fa-star text-yellow-500 text-xs"></i> Featured
                                        </span>
                                    </label>

                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="hidden" name="is_verified" value="0">
                                        <input type="checkbox" name="is_verified" value="1"
                                            {{ old('is_verified', $product->is_verified) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-blue-500 shadow-sm focus:ring-blue-500">
                                        <span class="text-sm text-gray-700 flex items-center gap-1">
                                            <i class="fas fa-shield-alt text-blue-500 text-xs"></i> Verified
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
                                    class="w-full inline-flex items-center justify-center gap-2 bg-[#1E3C2C] text-white px-6 py-3 rounded-lg hover:bg-[#143023] transition text-sm font-semibold disabled:opacity-60">
                                    <span x-show="!submitting"><i class="fas fa-save mr-1"></i> Update Product</span>
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
        function productEditForm() {
            /**
             * Normalise the incoming images payload into a clean array of
             * { path, alt, sort, removed: false } objects. Also DEDUPLICATES
             * by path so historical duplicates collapse to one entry in the UI.
             */
            const normaliseImages = (raw) => {
                if (typeof raw === 'string') {
                    try { raw = JSON.parse(raw); } catch (e) { raw = []; }
                }
                if (!Array.isArray(raw)) raw = Object.values(raw || {});

                const seenPaths = new Set();
                const cleaned = [];

                for (const img of raw) {
                    if (!img) continue;
                    const obj = (typeof img === 'string') ? { path: img } : { ...img };
                    if (!obj.path || seenPaths.has(obj.path)) continue;
                    seenPaths.add(obj.path);
                    cleaned.push({ ...obj, removed: false });
                }

                return cleaned;
            };

            let fileCounter = 0;

            return {
                categoryId: @json((string) old('category_id', $product->category_id)),
                attributes: [],
                attributesLoading: false,
                submitting: false,

                attrValues: @json($product->attributes ?? []),

                existingImages: normaliseImages(@json($product->images ?? [])),

                newFiles: [],

                primary: null,

                init() {
                    @if (!empty($product->primary_image))
                        const primaryPath = @json($product->primary_image);
                        const idx = this.existingImages.findIndex(img => img.path === primaryPath);
                        if (idx !== -1) {
                            this.primary = { kind: 'existing', index: idx };
                        }
                    @endif

                    if (!this.primary && this.existingImages.length > 0) {
                        this.primary = { kind: 'existing', index: 0 };
                    }

                    if (this.categoryId) {
                        this.loadAttributes();
                    }
                },

                // ---------- Existing images ----------

                removeExisting(idx) {
                    const current = this.existingImages[idx];
                    if (!current) return;

                    // Toggle removal flag in place for Alpine reactivity.
                    current.removed = !current.removed;

                    if (current.removed && this.primary?.kind === 'existing' && this.primary.index === idx) {
                        this.primary = this.firstAvailablePrimary();
                    }

                    if (!current.removed && !this.primary) {
                        this.primary = { kind: 'existing', index: idx };
                    }
                },

                keptIndices() {
                    const kept = [];
                    this.existingImages.forEach((img, i) => {
                        if (!img.removed) kept.push(i);
                    });
                    return kept;
                },

                firstAvailablePrimary() {
                    const firstKeptExisting = this.existingImages.findIndex(i => !i.removed);
                    if (firstKeptExisting !== -1) {
                        return { kind: 'existing', index: firstKeptExisting };
                    }
                    if (this.newFiles.length > 0) {
                        return { kind: 'new', index: 0 };
                    }
                    return null;
                },

                // ---------- Primary ----------

                isPrimary(kind, index) {
                    return !!this.primary && this.primary.kind === kind && this.primary.index === index;
                },

                setPrimary(kind, index) {
                    this.primary = { kind, index };
                },

                primaryMergedIndex() {
                    if (!this.primary) return 0;

                    const keptCount = this.existingImages.filter(i => !i.removed).length;

                    if (this.primary.kind === 'existing') {
                        let pos = 0;
                        for (let i = 0; i < this.existingImages.length; i++) {
                            if (i === this.primary.index) break;
                            if (!this.existingImages[i].removed) pos++;
                        }
                        return pos;
                    }

                    if (this.primary.kind === 'new') {
                        return keptCount + this.primary.index;
                    }

                    return 0;
                },

                // ---------- New files ----------

                onNewFilesSelected(event) {
                    const files = Array.from(event.target.files || []);

                    for (const file of files) {
                        this.newFiles.push({
                            id: ++fileCounter,
                            url: URL.createObjectURL(file),
                            name: file.name,
                            file: file,
                        });
                    }

                    if (!this.primary && this.newFiles.length > 0) {
                        this.primary = { kind: 'new', index: 0 };
                    }

                    this.syncFileInput();
                },

                removeNew(idx) {
                    const item = this.newFiles[idx];
                    if (!item) return;

                    URL.revokeObjectURL(item.url);
                    this.newFiles.splice(idx, 1);

                    if (this.primary?.kind === 'new') {
                        if (this.primary.index === idx) {
                            this.primary = this.firstAvailablePrimary();
                        } else if (this.primary.index > idx) {
                            this.primary = { kind: 'new', index: this.primary.index - 1 };
                        }
                    }

                    this.syncFileInput();
                },

                syncFileInput() {
                    const input = this.$refs.newFilesInput;
                    if (!input) return;

                    try {
                        const dt = new DataTransfer();
                        this.newFiles.forEach(f => dt.items.add(f.file));
                        input.files = dt.files;
                    } catch (e) {
                        console.error('Could not sync file input:', e);
                    }
                },

                // ---------- Attributes ----------

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