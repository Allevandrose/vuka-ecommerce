@php
    $primaryImage = !empty($product->primary_image)
        ? asset('storage/' . $product->primary_image)
        : (!empty($product->images[0]['path']) ? asset('storage/' . $product->images[0]['path']) : null);
@endphp

<x-shop-layout
    :title="$title"
    :meta-description="$metaDescription"
    :og-title="$product->seoTitle()"
    :og-description="$product->seoDescription()"
    :og-image="$primaryImage"
    og-type="product"
    :canonical="route('product.show', $product->slug)">

    {{-- STRUCTURED DATA --}}
    @push('seo')
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $product->name,
            'image' => array_values(array_map(fn($img) => asset('storage/' . $img['path']), $product->images ?? [])),
            'description' => $product->seoDescription(),
            'sku' => $product->sku,
            'brand' => $product->brand ? [
                '@type' => 'Brand',
                'name' => $product->brand->name,
            ] : null,
            'offers' => [
                '@type' => 'Offer',
                'url' => route('product.show', $product->slug),
                'priceCurrency' => $product->currency,
                'price' => (string) $product->price,
                'availability' => $product->inStock()
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
                'itemCondition' => match($product->condition) {
                    'used' => 'https://schema.org/UsedCondition',
                    'refurbished' => 'https://schema.org/RefurbishedCondition',
                    default => 'https://schema.org/NewCondition',
                },
            ],
            'aggregateRating' => $product->rating_count > 0 ? [
                '@type' => 'AggregateRating',
                'ratingValue' => (string) $product->rating_avg,
                'reviewCount' => (string) $product->rating_count,
            ] : null,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>

        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org/',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($breadcrumb)->map(function ($crumb, $i) use ($product) {
                $url = !empty($crumb['slug']) && $crumb['slug'] !== $product->slug
                    ? url('/category/' . $crumb['slug'])
                    : url('/product/' . $product->slug);

                return [
                    '@type' => 'ListItem',
                    'position' => $i + 1,
                    'name' => $crumb['name'],
                    'item' => $url,
                ];
            })->values()->all(),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endpush

    {{-- BREADCRUMB --}}
    <nav class="flex items-center gap-2 text-xs text-muted py-4 overflow-x-auto no-scrollbar">
        <a href="{{ route('home') }}" class="hover:text-moss transition-colors shrink-0">
            <i class="fas fa-home"></i> Home
        </a>
        @foreach ($breadcrumb as $crumb)
            <i class="fas fa-chevron-right text-[8px] text-muted/50 shrink-0"></i>
            @if ($loop->last || !$crumb['slug'])
                <span class="font-medium text-ink whitespace-nowrap truncate max-w-[200px]">{{ $crumb['name'] }}</span>
            @else
                <a href="{{ route('category.show', $crumb['slug']) }}" class="hover:text-moss transition-colors whitespace-nowrap">
                    {{ $crumb['name'] }}
                </a>
            @endif
        @endforeach
    </nav>

    {{-- MAIN PRODUCT SECTION --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-10 mb-10"
         x-data="productGallery({{ json_encode(array_values(array_map(fn($img) => asset('storage/' . $img['path']), $product->images ?? []))) }})">

        {{-- ============================================ --}}
        {{-- LEFT: IMAGE GALLERY --}}
        {{-- ============================================ --}}
        <div>
            {{-- Main image --}}
            <div class="relative rounded-2xl overflow-hidden bg-white border border-line aspect-square">
                @if (!empty($product->images))
                    <img :src="activeImage"
                         alt="{{ $product->name }}"
                         class="w-full h-full object-cover transition-opacity duration-200">

                    @if ($product->is_featured)
                        <span class="absolute top-4 left-4 inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-gold text-ink shadow-sm">
                            <i class="fas fa-star text-[10px]"></i> Featured
                        </span>
                    @endif

                    @if ($product->isOnSale())
                        <span class="absolute top-4 right-4 inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-500 text-white shadow-sm">
                            -{{ $product->discountPercent() }}% OFF
                        </span>
                    @endif

                    @if (!$product->inStock())
                        <div class="absolute inset-0 bg-ink/60 flex items-center justify-center">
                            <span class="px-4 py-2 rounded-full bg-white text-ink text-sm font-semibold uppercase tracking-wide">
                                Out of stock
                            </span>
                        </div>
                    @endif
                @else
                    <div class="w-full h-full flex items-center justify-center bg-paper">
                        <i class="fas fa-image text-6xl text-ink/20"></i>
                    </div>
                @endif
            </div>

            {{-- Thumbnails --}}
            @if (!empty($product->images) && count($product->images) > 1)
                <div class="flex gap-2 mt-3 overflow-x-auto no-scrollbar">
                    <template x-for="(img, i) in images" :key="i">
                        <button type="button"
                                @click="activeImage = img"
                                class="shrink-0 w-20 h-20 rounded-xl overflow-hidden border-2 transition"
                                :class="activeImage === img ? 'border-moss ring-2 ring-moss/20' : 'border-line hover:border-moss/40'">
                            <img :src="img" :alt="'View ' + (i + 1)" class="w-full h-full object-cover">
                        </button>
                    </template>
                </div>
            @endif
        </div>

        {{-- ============================================ --}}
        {{-- RIGHT: INFO PANEL --}}
        {{-- ============================================ --}}
        <div class="space-y-5">

            {{-- Brand + title --}}
            <div>
                @if ($product->brand)
                    <a href="#" class="inline-flex items-center gap-1.5 text-sm font-semibold text-moss hover:text-moss/70 transition">
                        {{ $product->brand->name }}
                        @if ($product->brand->is_verified)
                            <i class="fas fa-circle-check text-blue-500 text-xs" title="Verified brand"></i>
                        @endif
                    </a>
                @endif
                <h1 class="font-serif text-2xl sm:text-3xl font-semibold text-ink leading-tight mt-1">
                    {{ $product->name }}
                </h1>

                <div class="flex items-center gap-3 mt-2 text-xs text-muted flex-wrap">
                    <span class="font-mono">SKU: {{ $product->sku }}</span>
                    @if ($product->rating_count > 0)
                        <span class="flex items-center gap-1 text-moss font-semibold">
                            <i class="fas fa-star text-gold text-xs"></i>
                            {{ number_format((float) $product->rating_avg, 1) }}
                            <span class="text-muted font-normal">({{ $product->rating_count }} {{ Str::plural('review', $product->rating_count) }})</span>
                        </span>
                    @endif
                </div>
            </div>

            {{-- Short description --}}
            @if ($product->short_description)
                <p class="text-sm text-ink/80 leading-relaxed">
                    {{ $product->short_description }}
                </p>
            @endif

            {{-- Price block --}}
            <div class="bg-paper rounded-2xl p-4 sm:p-5">
                <div class="flex items-baseline gap-3 flex-wrap">
                    <span class="font-serif text-3xl sm:text-4xl font-bold text-ink">
                        {{ $product->formattedPrice() }}
                    </span>
                    @if ($product->isOnSale())
                        <span class="text-base text-muted line-through">
                            {{ $product->formattedCompareAtPrice() }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">
                            <i class="fas fa-tag text-[10px]"></i>
                            Save {{ $product->discountPercent() }}%
                        </span>
                    @endif
                </div>

                {{-- Stock status --}}
                <div class="mt-3 flex items-center gap-2 text-sm">
                    @if (!$product->track_inventory)
                        <span class="inline-flex items-center gap-1.5 text-moss font-medium">
                            <span class="w-2 h-2 rounded-full bg-moss"></span> Available
                        </span>
                    @elseif ($product->stock_quantity === 0)
                        <span class="inline-flex items-center gap-1.5 text-red-600 font-medium">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span> Out of stock
                        </span>
                    @elseif ($product->isLowStock())
                        <span class="inline-flex items-center gap-1.5 text-amber-600 font-medium">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            Only {{ $product->stock_quantity }} left
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 text-moss font-medium">
                            <span class="w-2 h-2 rounded-full bg-moss"></span>
                            In stock ({{ $product->stock_quantity }} available)
                        </span>
                    @endif
                </div>
            </div>

            {{-- Trust badges --}}
            <div class="flex flex-wrap gap-2">
                @if ($product->is_verified)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                        <i class="fas fa-shield-alt text-[10px]"></i> Verified
                    </span>
                @endif
                @if ($product->is_certified)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-100">
                        <i class="fas fa-certificate text-[10px]"></i> Certified
                    </span>
                @endif
                @if ($product->is_local)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                        <i class="fas fa-map-marker-alt text-[10px]"></i> Local
                    </span>
                @endif
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                    <i class="fas fa-sync text-[10px]"></i> {{ ucfirst($product->condition) }}
                </span>
            </div>

            {{-- Add to cart --}}
            <div class="space-y-3 pt-2">
                @if ($product->inStock())
                    <div class="flex items-center gap-3">
                        <div class="flex items-center border border-line rounded-full overflow-hidden bg-white"
                             x-data="{ qty: 1 }">
                            <button type="button" @click="qty = Math.max(1, qty - 1)"
                                    class="w-10 h-10 flex items-center justify-center text-ink hover:bg-paper transition">
                                <i class="fas fa-minus text-xs"></i>
                            </button>
                            <input type="number" x-model.number="qty" min="1" max="{{ $product->track_inventory ? $product->stock_quantity : 999 }}"
                                   class="w-12 text-center border-0 focus:ring-0 text-sm font-semibold bg-white p-0">
                            <button type="button" @click="qty = qty + 1"
                                    class="w-10 h-10 flex items-center justify-center text-ink hover:bg-paper transition">
                                <i class="fas fa-plus text-xs"></i>
                            </button>
                        </div>

                        <button type="button"
                                x-data="{ added: false }"
                                @click="added = true; setTimeout(() => added = false, 1500)"
                                :class="added ? 'bg-emerald-600' : 'bg-moss hover:bg-moss/90'"
                                class="flex-1 text-white font-semibold py-3 px-6 rounded-full transition flex items-center justify-center gap-2 shadow-[0_10px_24px_-8px_rgba(32,80,60,0.55)]">
                            <i class="fas" :class="added ? 'fa-check' : 'fa-shopping-cart'"></i>
                            <span x-text="added ? 'Added to cart' : 'Add to cart'"></span>
                        </button>
                    </div>

                    <button type="button"
                            class="w-full border-2 border-ink text-ink font-semibold py-3 rounded-full hover:bg-ink hover:text-white transition">
                        <i class="fas fa-bolt text-xs"></i> Buy now
                    </button>
                @else
                    <button type="button" disabled
                            class="w-full bg-gray-200 text-gray-500 font-semibold py-3 rounded-full cursor-not-allowed">
                        <i class="fas fa-ban text-xs"></i> Out of stock
                    </button>
                    <p class="text-xs text-center text-muted">
                        This product is currently unavailable. Check back soon.
                    </p>
                @endif
            </div>

            {{-- Delivery info --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-4 border-t border-line">
                <div class="flex items-start gap-2.5">
                    <div class="w-9 h-9 rounded-full bg-paper flex items-center justify-center text-moss shrink-0">
                        <i class="fas fa-truck text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-ink">Fast delivery</p>
                        <p class="text-xs text-muted mt-0.5">Nationwide 1–3 days</p>
                    </div>
                </div>

                <div class="flex items-start gap-2.5">
                    <div class="w-9 h-9 rounded-full bg-paper flex items-center justify-center text-moss shrink-0">
                        <i class="fas fa-undo text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-ink">Easy returns</p>
                        <p class="text-xs text-muted mt-0.5">7-day return policy</p>
                    </div>
                </div>
            </div>

            {{-- Vendor card (if vendor product) --}}
            @if ($product->isVendorProduct() && $product->source)
                <div class="bg-white border border-line rounded-2xl p-4 flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-amber-700 shrink-0">
                        <i class="fas fa-store text-lg"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-muted uppercase tracking-wide">Sold by</p>
                        <p class="font-semibold text-sm text-ink truncate">
                            {{ $product->source->shop_name ?? $product->source->name }}
                        </p>
                        @if ($product->source->shop_address)
                            <p class="text-xs text-muted truncate">
                                <i class="fas fa-map-marker-alt text-[9px]"></i>
                                {{ $product->source->shop_address }}
                            </p>
                        @endif
                    </div>
                    <a href="#" class="text-xs font-semibold text-moss hover:text-moss/70 transition shrink-0">
                        Visit shop <i class="fas fa-arrow-right text-[9px]"></i>
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- DESCRIPTION + SPECS (tabbed) --}}
    {{-- ============================================ --}}
    <div class="bg-white border border-line rounded-2xl overflow-hidden mb-8"
         x-data="{ tab: 'description' }">

        <div class="flex border-b border-line">
            <button type="button" @click="tab = 'description'"
                    :class="tab === 'description' ? 'text-ink border-b-2 border-moss font-semibold' : 'text-muted'"
                    class="px-5 sm:px-6 py-4 text-sm transition">
                Description
            </button>
            <button type="button" @click="tab = 'specs'"
                    :class="tab === 'specs' ? 'text-ink border-b-2 border-moss font-semibold' : 'text-muted'"
                    class="px-5 sm:px-6 py-4 text-sm transition">
                Specifications
                @php
                    $attrCount = count($product->attributes ?? []);
                @endphp
                @if ($attrCount > 0)
                    <span class="ml-1 text-xs text-muted">({{ $attrCount }})</span>
                @endif
            </button>
        </div>

        {{-- DESCRIPTION TAB --}}
        <div x-show="tab === 'description'" class="p-6">
            @if ($product->description)
                <div class="prose prose-sm max-w-none text-ink/80 leading-relaxed whitespace-pre-line">{{ $product->description }}</div>
            @else
                <p class="text-sm text-muted italic">No description provided.</p>
            @endif
        </div>

        {{-- SPECS TAB --}}
        <div x-show="tab === 'specs'" x-cloak class="p-6">
            @php
                $productAttrs = $product->attributes ?? [];
            @endphp

            @if (!empty($productAttrs))
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-3">
                    @foreach ($attributeDefinitions as $def)
                        @if (array_key_exists($def->key, $productAttrs))
                            <div class="flex items-baseline justify-between gap-4 py-2 border-b border-line/60">
                                <dt class="text-xs text-muted uppercase tracking-wide">{{ $def->label }}</dt>
                                <dd class="text-sm text-ink font-medium text-right">
                                    {{ $def->displayValue($productAttrs[$def->key]) }}
                                </dd>
                            </div>
                        @endif
                    @endforeach

                    {{-- Orphaned attributes (keys not in definitions) --}}
                    @foreach ($productAttrs as $key => $value)
                        @if (!$attributeDefinitions->contains('key', $key))
                            <div class="flex items-baseline justify-between gap-4 py-2 border-b border-line/60">
                                <dt class="text-xs text-muted uppercase tracking-wide">{{ ucfirst($key) }}</dt>
                                <dd class="text-sm text-ink font-medium text-right">
                                    {{ is_array($value) ? implode(', ', $value) : $value }}
                                </dd>
                            </div>
                        @endif
                    @endforeach
                </dl>
            @else
                <p class="text-sm text-muted italic">No specifications listed.</p>
            @endif
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- REVIEWS SECTION (read-only for now) --}}
    {{-- ============================================ --}}
    @include('shop.partials.product-reviews', ['product' => $product])

    {{-- ============================================ --}}
    {{-- RELATED PRODUCTS --}}
    {{-- ============================================ --}}
    @if ($related->isNotEmpty())
        <section class="mb-12">
            <div class="flex items-baseline justify-between mb-4">
                <h3 class="font-serif text-xl font-semibold flex items-center gap-2">
                    <i class="fas fa-th-large text-gold text-base"></i> You may also like
                </h3>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3.5">
                @foreach ($related as $relatedProduct)
                    <x-product-card :product="$relatedProduct" :show-category="false" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- GALLERY ALPINE COMPONENT --}}
    <script>
        function productGallery(imagesArray) {
            return {
                images: imagesArray || [],
                activeImage: imagesArray && imagesArray.length > 0 ? imagesArray[0] : '',
            }
        }
    </script>

</x-shop-layout>