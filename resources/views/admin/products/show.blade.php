<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="min-w-0">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight truncate">
                    {{ $product->name }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5 font-mono">
                    {{ $product->sku }}
                    @if ($product->brand)
                        · {{ $product->brand->name }}
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.products.edit', $product) }}"
                    class="inline-flex items-center gap-2 bg-[#1E3C2C] text-white px-4 py-2 rounded-lg hover:bg-[#143023] transition text-sm font-semibold">
                    <i class="fas fa-pen text-xs"></i> Edit
                </a>
                <a href="{{ route('admin.products.index') }}"
                    class="text-sm text-indigo-600 hover:text-indigo-800 inline-flex items-center gap-1 px-3 py-2">
                    <i class="fas fa-arrow-left text-xs"></i> Back
                </a>
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

            {{-- Workflow Alerts --}}
            @if ($product->status === 'rejected' && $product->rejection_reason)
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm font-semibold text-red-800 flex items-center gap-2">
                        <i class="fas fa-times-circle"></i> This product was rejected
                    </p>
                    <p class="text-sm text-red-700 mt-1">{{ $product->rejection_reason }}</p>
                </div>
            @endif

            @if ($product->status === 'disabled' && $product->disabled_reason)
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm font-semibold text-red-800 flex items-center gap-2">
                        <i class="fas fa-ban"></i> This product has been disabled site-wide
                    </p>
                    <p class="text-sm text-red-700 mt-1">{{ $product->disabled_reason }}</p>
                    <p class="text-xs text-red-600 mt-2">
                        Disabled by {{ $product->disabledBy?->name ?? 'admin' }}
                        @if ($product->disabled_at)
                            · {{ $product->disabled_at->diffForHumans() }}
                        @endif
                    </p>
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
                            <i class="fas fa-clock text-gray-400"></i>
                            Updated {{ $product->updated_at->diffForHumans() }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        @if ($product->is_featured)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-star text-[9px]"></i> Featured
                            </span>
                        @endif
                        @if ($product->is_verified)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-shield-alt text-[9px]"></i> Verified
                            </span>
                        @endif
                        @if ($product->is_certified)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-certificate text-[9px]"></i> Certified
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- LEFT: Images + Content --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Images --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4 flex items-center gap-2">
                                <i class="fas fa-images text-gray-400"></i> Images
                                <span class="text-gray-400 font-normal normal-case">({{ count($product->images ?? []) }})</span>
                            </h3>

                            @if (!empty($product->images))
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    @foreach ($product->sortedImages() as $img)
                                        <div class="relative group rounded-lg overflow-hidden border-2 {{ ($product->primary_image ?? '') === ($img['path'] ?? '') ? 'border-indigo-500' : 'border-gray-200' }}">
                                            <img src="{{ Storage::disk('public')->url($img['path']) }}"
                                                alt="{{ $img['alt'] ?? $product->name }}"
                                                class="w-full h-40 object-cover">
                                            @if (($product->primary_image ?? '') === ($img['path'] ?? ''))
                                                <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-600 text-white">
                                                    <i class="fas fa-star text-[9px] mr-0.5"></i> Primary
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-400 bg-gray-50 rounded-lg">
                                    <i class="fas fa-image text-3xl mb-2"></i>
                                    <p class="text-sm">No images uploaded.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4 flex items-center gap-2">
                                <i class="fas fa-align-left text-gray-400"></i> Description
                            </h3>

                            @if ($product->short_description)
                                <p class="text-sm text-gray-700 italic mb-4 pb-4 border-b border-gray-100">
                                    {{ $product->short_description }}
                                </p>
                            @endif

                            @if ($product->description)
                                <div class="text-sm text-gray-700 prose prose-sm max-w-none">
                                    {!! nl2br(e($product->description)) !!}
                                </div>
                            @else
                                <p class="text-sm text-gray-400 italic">No description.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Attributes --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4 flex items-center gap-2">
                                <i class="fas fa-list-ul text-gray-400"></i> Attributes
                            </h3>

                            @php
                                $productAttrs = $product->attributes ?? [];
                            @endphp

                            @if (!empty($productAttrs))
                                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    @foreach ($attributeDefinitions as $def)
                                        @if (array_key_exists($def->key, $productAttrs))
                                            <div>
                                                <dt class="text-xs text-gray-500 uppercase tracking-wide">{{ $def->label }}</dt>
                                                <dd class="text-sm text-gray-900 mt-1">
                                                    {{ $def->displayValue($productAttrs[$def->key]) }}
                                                </dd>
                                            </div>
                                        @endif
                                    @endforeach

                                    {{-- Attributes not in current definitions (orphaned keys) --}}
                                    @foreach ($productAttrs as $key => $value)
                                        @if (!$attributeDefinitions->contains('key', $key))
                                            <div>
                                                <dt class="text-xs text-gray-400 uppercase tracking-wide">
                                                    {{ $key }}
                                                    <span class="text-[10px] font-normal normal-case">(orphaned)</span>
                                                </dt>
                                                <dd class="text-sm text-gray-500 mt-1">
                                                    {{ is_array($value) ? implode(', ', $value) : $value }}
                                                </dd>
                                            </div>
                                        @endif
                                    @endforeach
                                </dl>
                            @else
                                <p class="text-sm text-gray-400 italic">No attributes set.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Reviews & Complaints (summary counts only — full management later) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3 flex items-center gap-2">
                                    <i class="fas fa-star text-gray-400"></i> Reviews
                                </h3>
                                <div class="flex items-baseline gap-3">
                                    <span class="text-3xl font-bold text-gray-900">
                                        {{ number_format((float) $product->rating_avg, 1) }}
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        / 5 · {{ $product->rating_count }} review{{ $product->rating_count === 1 ? '' : 's' }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-400 mt-3">
                                    Full review moderation UI coming in a later batch.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3 flex items-center gap-2">
                                    <i class="fas fa-flag text-gray-400"></i> Complaints
                                </h3>
                                <div class="flex items-baseline gap-3">
                                    <span class="text-3xl font-bold {{ $product->complaint_count > 0 ? 'text-red-600' : 'text-gray-900' }}">
                                        {{ $product->complaint_count }}
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        open complaint{{ $product->complaint_count === 1 ? '' : 's' }}
                                    </span>
                                </div>
                                @if ($product->complaint_count >= 10)
                                    <p class="text-xs text-red-600 mt-3 font-medium">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        High complaint count — consider disabling.
                                    </p>
                                @else
                                    <p class="text-xs text-gray-400 mt-3">
                                        Full complaint review UI coming in a later batch.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

                {{-- RIGHT: Sidebar --}}
                <div class="lg:col-span-1 space-y-6">

                    {{-- Status --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 space-y-4">
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide flex items-center gap-2">
                                <i class="fas fa-flag text-gray-400"></i> Status
                            </h3>

                            @php
                                $statusColors = [
                                    'draft' => 'bg-gray-100 text-gray-700',
                                    'active' => 'bg-green-100 text-green-800',
                                    'pending_review' => 'bg-yellow-100 text-yellow-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                    'disabled' => 'bg-red-100 text-red-800',
                                    'archived' => 'bg-gray-200 text-gray-600',
                                ];
                                $statusColor = $statusColors[$product->status] ?? 'bg-gray-100 text-gray-700';
                            @endphp

                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }}">
                                    {{ ucfirst(str_replace('_', ' ', $product->status)) }}
                                </span>
                                @if ($product->published_at && $product->status === 'active')
                                    <span class="text-xs text-gray-500">
                                        since {{ $product->published_at->format('M d, Y') }}
                                    </span>
                                @endif
                            </div>

                            @if ($product->is_visible)
                                <a href="#" target="_blank"
                                    class="text-xs text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                                    <i class="fas fa-external-link-alt text-[10px]"></i>
                                    View on site
                                    <span class="text-gray-400 italic">(public page not built yet)</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Pricing --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 space-y-3">
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide flex items-center gap-2">
                                <i class="fas fa-tag text-gray-400"></i> Pricing
                            </h3>

                            <div class="flex items-baseline gap-2">
                                <span class="text-2xl font-bold text-gray-900">{{ $product->formattedPrice() }}</span>
                                @if ($product->isOnSale())
                                    <span class="text-sm text-gray-400 line-through">{{ $product->formattedCompareAtPrice() }}</span>
                                    <span class="text-xs font-semibold text-red-600 bg-red-50 px-1.5 py-0.5 rounded">
                                        -{{ $product->discountPercent() }}%
                                    </span>
                                @endif
                            </div>

                            @if ($product->cost_price)
                                <div class="text-xs text-gray-500 pt-2 border-t border-gray-100">
                                    <div class="flex justify-between">
                                        <span>Cost price:</span>
                                        <span class="font-mono text-gray-700">
                                            {{ $product->currency }} {{ number_format((float) $product->cost_price, 2) }}
                                        </span>
                                    </div>
                                    @php
                                        $margin = (float) $product->price - (float) $product->cost_price;
                                        $marginPct = (float) $product->price > 0 ? ($margin / (float) $product->price) * 100 : 0;
                                    @endphp
                                    <div class="flex justify-between mt-1">
                                        <span>Margin:</span>
                                        <span class="font-mono {{ $margin > 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $product->currency }} {{ number_format($margin, 2) }}
                                            ({{ number_format($marginPct, 1) }}%)
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Inventory --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 space-y-3">
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide flex items-center gap-2">
                                <i class="fas fa-boxes text-gray-400"></i> Inventory
                            </h3>

                            <div class="flex items-baseline gap-2">
                                <span class="text-2xl font-bold {{ $product->stock_quantity === 0 ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ $product->stock_quantity }}
                                </span>
                                <span class="text-xs text-gray-500">units</span>
                            </div>

                            <div class="flex gap-2 flex-wrap text-xs">
                                @if ($product->track_inventory)
                                    <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700">Tracked</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">Not tracked</span>
                                @endif

                                @if ($product->is_local)
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700">Local</span>
                                @endif
                                @if ($product->is_international)
                                    <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-700">International</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Source --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 space-y-3">
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide flex items-center gap-2">
                                <i class="fas fa-shield-alt text-gray-400"></i> Source
                            </h3>

                            @if ($product->isVendorProduct())
                                <div class="flex items-center gap-3">
                                    <div class="shrink-0 w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center text-amber-700">
                                        <i class="fas fa-store"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $product->source?->shop_name ?? $product->source?->name ?? 'Vendor' }}
                                        </p>
                                        <p class="text-xs text-gray-500 truncate">
                                            {{ $product->source?->email ?? '—' }}
                                        </p>
                                    </div>
                                </div>
                                @if ($product->source?->shop_address)
                                    <p class="text-xs text-gray-500 mt-2 pt-2 border-t border-gray-100">
                                        <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>
                                        {{ $product->source->shop_address }}
                                    </p>
                                @endif
                            @else
                                <div class="flex items-center gap-3">
                                    <div class="shrink-0 w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-700">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Admin product</p>
                                        <p class="text-xs text-gray-500">
                                            Owned by {{ $product->source?->name ?? 'system' }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Category & Brand --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 space-y-3">
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide flex items-center gap-2">
                                <i class="fas fa-sitemap text-gray-400"></i> Classification
                            </h3>

                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Category</p>
                                <p class="text-sm text-gray-900 mt-0.5">
                                    {{ $product->category?->name ?? '—' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Brand</p>
                                <p class="text-sm text-gray-900 mt-0.5 flex items-center gap-1.5">
                                    {{ $product->brand?->name ?? '—' }}
                                    @if ($product->brand?->is_verified)
                                        <i class="fas fa-circle-check text-blue-500 text-xs" title="Verified brand"></i>
                                    @endif
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Condition</p>
                                <p class="text-sm text-gray-900 mt-0.5">{{ ucfirst($product->condition) }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Admin Actions --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 space-y-3">
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide flex items-center gap-2">
                                <i class="fas fa-sliders-h text-gray-400"></i> Actions
                            </h3>

                            {{-- Toggle featured --}}
                            <form method="POST" action="{{ route('admin.products.toggleFeatured', $product) }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition
                                        {{ $product->is_featured ? 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100' : 'bg-gray-50 text-gray-700 hover:bg-gray-100' }}">
                                    <i class="fas fa-star text-xs mr-1"></i>
                                    {{ $product->is_featured ? 'Remove from featured' : 'Mark as featured' }}
                                </button>
                            </form>

                            {{-- Enable/Disable --}}
                            @if ($product->status === 'disabled')
                                <form method="POST" action="{{ route('admin.products.enable', $product) }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium bg-green-50 text-green-700 hover:bg-green-100 transition">
                                        <i class="fas fa-check-circle text-xs mr-1"></i>
                                        Re-enable product
                                    </button>
                                </form>
                            @else
                                <button type="button"
                                    onclick="openDisableModal('{{ addslashes($product->name) }}')"
                                    class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium bg-red-50 text-red-700 hover:bg-red-100 transition">
                                    <i class="fas fa-ban text-xs mr-1"></i>
                                    Disable site-wide
                                </button>
                            @endif

                            {{-- Archive --}}
                            @if ($product->status !== 'archived')
                                <form method="POST" action="{{ route('admin.products.archive', $product) }}"
                                    onsubmit="return confirm('Archive this product? It will be hidden from public but not deleted.')">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium bg-gray-50 text-gray-700 hover:bg-gray-100 transition">
                                        <i class="fas fa-archive text-xs mr-1"></i>
                                        Archive
                                    </button>
                                </form>
                            @endif

                            {{-- Delete --}}
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                onsubmit="return confirm('Delete &quot;{{ addslashes($product->name) }}&quot;? This soft-deletes it and removes image files.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium bg-red-50 text-red-700 hover:bg-red-100 transition">
                                    <i class="fas fa-trash text-xs mr-1"></i>
                                    Delete product
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- SEO --}}
                    @if ($product->meta_title || $product->meta_description || $product->meta_keywords)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 space-y-3">
                                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide flex items-center gap-2">
                                    <i class="fas fa-search text-gray-400"></i> SEO
                                </h3>

                                @if ($product->meta_title)
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide">Title</p>
                                        <p class="text-sm text-gray-900 mt-0.5">{{ $product->meta_title }}</p>
                                    </div>
                                @endif

                                @if ($product->meta_description)
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide">Description</p>
                                        <p class="text-sm text-gray-700 mt-0.5">{{ $product->meta_description }}</p>
                                    </div>
                                @endif

                                @if ($product->meta_keywords)
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide">Keywords</p>
                                        <p class="text-sm text-gray-700 mt-0.5">{{ $product->meta_keywords }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- Disable Modal --}}
    <div id="disableModal" class="hidden fixed inset-0 z-50 bg-black/50 items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Disable Product</h3>
            <p class="text-sm text-gray-500 mb-4">
                Disabling hides the product <strong>site-wide</strong>. Product: <strong id="disableProductName"></strong>
            </p>
            <form id="disableForm" method="POST" action="{{ route('admin.products.disable', $product) }}">
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
        function openDisableModal(productName) {
            document.getElementById('disableProductName').textContent = productName;
            document.getElementById('disableModal').classList.remove('hidden');
            document.getElementById('disableModal').classList.add('flex');
        }

        function closeDisableModal() {
            document.getElementById('disableModal').classList.add('hidden');
            document.getElementById('disableModal').classList.remove('flex');
        }
    </script>
</x-app-layout>