@php
    $galleryImages = collect($product->images ?? [])
        ->map(fn ($img) => is_array($img) ? ($img['path'] ?? null) : $img)
        ->filter()
        ->map(fn ($path) => asset('storage/' . $path))
        ->values()
        ->all();

    if (!empty($product->primary_image)) {
        $primary = asset('storage/' . $product->primary_image);
        $galleryImages = array_values(array_unique(array_merge([$primary], $galleryImages)));
    }

    $primaryImage = $galleryImages[0] ?? null;
    $maxQty = $product->track_inventory ? max(1, (int) $product->stock_quantity) : 999;

    $initialWishlisted = auth()->check() && auth()->user()->isWishlisted($product->id);
    $isGuest = ! auth()->check();

    $productSchema = array_filter([
        '@context' => 'https://schema.org/',
        '@type' => 'Product',
        'name' => $product->name,
        'image' => $galleryImages,
        'description' => $product->seoDescription(),
        'sku' => $product->sku,
        'brand' => $product->brand ? ['@type' => 'Brand', 'name' => $product->brand->name] : null,
        'offers' => [
            '@type' => 'Offer',
            'url' => route('product.show', $product->slug),
            'priceCurrency' => $product->currency,
            'price' => (string) $product->price,
            'availability' => $product->inStock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'itemCondition' => match ($product->condition) {
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
    ], fn ($v) => !is_null($v) && $v !== []);
@endphp

<x-shop-layout
    :title="$title"
    :meta-description="$metaDescription"
    :og-title="$product->seoTitle()"
    :og-description="$product->seoDescription()"
    :og-image="$primaryImage"
    og-type="product"
    :canonical="route('product.show', $product->slug)">

    @push('seo')
        <script type="application/ld+json">
        {!! json_encode($productSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
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

    <script>
        function productGallery(imagesArray, maxQty, initialWishlisted) {
            return {
                images: imagesArray || [],
                index: 0,
                maxQty: maxQty || 999,
                qty: 1,
                adding: false,
                added: false,

                wishlisted: !!initialWishlisted,
                wishlistBusy: false,

                lens: false,
                lensX: 50,
                lensY: 50,

                lightbox: false,
                scale: 1,
                panX: 0,
                panY: 0,
                dragging: false,
                dragStartX: 0,
                dragStartY: 0,
                touchStartX: 0,

                get active() { return this.images[this.index] || ''; },
                get multiple() { return this.images.length > 1; },

                go(i) {
                    if (!this.images.length) return;
                    this.index = (i + this.images.length) % this.images.length;
                    this.resetZoom();
                },
                next() { this.go(this.index + 1); },
                prev() { this.go(this.index - 1); },

                canHover() { return window.matchMedia('(hover: hover) and (pointer: fine)').matches; },
                enterLens() { if (this.canHover()) this.lens = true; },
                moveLens(e) {
                    const r = e.currentTarget.getBoundingClientRect();
                    this.lensX = Math.min(100, Math.max(0, ((e.clientX - r.left) / r.width) * 100));
                    this.lensY = Math.min(100, Math.max(0, ((e.clientY - r.top) / r.height) * 100));
                },

                swipeStart(e) { this.touchStartX = e.changedTouches[0].clientX; },
                swipeEnd(e) {
                    const dx = e.changedTouches[0].clientX - this.touchStartX;
                    if (Math.abs(dx) > 45 && this.multiple) { dx < 0 ? this.next() : this.prev(); }
                },

                open() {
                    if (!this.images.length) return;
                    this.lens = false;
                    this.lightbox = true;
                    document.body.classList.add('overflow-hidden');
                },
                close() {
                    this.lightbox = false;
                    this.resetZoom();
                    document.body.classList.remove('overflow-hidden');
                },
                resetZoom() { this.scale = 1; this.panX = 0; this.panY = 0; },
                zoomBy(step) {
                    this.scale = Math.min(4, Math.max(1, +(this.scale + step).toFixed(2)));
                    if (this.scale === 1) { this.panX = 0; this.panY = 0; }
                },
                toggleZoom() { this.scale > 1 ? this.resetZoom() : (this.scale = 2.5); },
                wheelZoom(e) { this.zoomBy(e.deltaY < 0 ? 0.3 : -0.3); },
                dragStart(e) {
                    if (this.scale <= 1) return;
                    this.dragging = true;
                    this.dragStartX = e.clientX - this.panX;
                    this.dragStartY = e.clientY - this.panY;
                },
                dragMove(e) {
                    if (!this.dragging) return;
                    this.panX = e.clientX - this.dragStartX;
                    this.panY = e.clientY - this.dragStartY;
                },
                dragEnd() { this.dragging = false; },
                onKey(e) {
                    if (!this.lightbox) return;
                    if (e.key === 'Escape') this.close();
                    if (e.key === 'ArrowRight') this.next();
                    if (e.key === 'ArrowLeft') this.prev();
                    if (e.key === '+' || e.key === '=') this.zoomBy(0.5);
                    if (e.key === '-') this.zoomBy(-0.5);
                },

                inc() { this.qty = Math.min(this.maxQty, (parseInt(this.qty) || 1) + 1); },
                dec() { this.qty = Math.max(1, (parseInt(this.qty) || 1) - 1); },
                clampQty() { this.qty = Math.min(this.maxQty, Math.max(1, parseInt(this.qty) || 1)); },

                async addToCart() {
                    if (this.adding || this.added) return;
                    this.clampQty();
                    this.adding = true;
                    try {
                        const res = await fetch('{{ route('cart.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ product_id: {{ $product->id }}, quantity: this.qty })
                        });

                        if (res.status === 401) {
                            window.location.href = '{{ route('login') }}';
                            return;
                        }

                        const data = await res.json();

                        if (!res.ok || !data.success) {
                            if (window.showToast) window.showToast(data.message || 'Could not add to cart.', 'error');
                            return;
                        }

                        if (window.Alpine && Alpine.store('cart')) {
                            Alpine.store('cart').count = data.cart_count;
                        }

                        this.added = true;
                        if (window.showToast) window.showToast(data.message, 'success');
                        setTimeout(() => { this.added = false; }, 1500);
                    } catch (e) {
                        if (window.showToast) window.showToast('Network error. Please try again.', 'error');
                    } finally {
                        this.adding = false;
                    }
                },

                async toggleWishlist() {
                    if (this.wishlistBusy) return;
                    @if ($isGuest)
                        window.location.href = '{{ route('login') }}';
                        return;
                    @endif
                    this.wishlistBusy = true;
                    try {
                        const res = await fetch('{{ route('wishlist.toggle') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ product_id: {{ $product->id }} })
                        });

                        if (res.status === 401) {
                            window.location.href = '{{ route('login') }}';
                            return;
                        }

                        const data = await res.json();

                        if (!res.ok || !data.success) {
                            if (window.showToast) window.showToast(data.message || 'Could not update wishlist.', 'error');
                            return;
                        }

                        this.wishlisted = !!data.wishlisted;
                        if (window.Alpine && Alpine.store('wishlist')) {
                            Alpine.store('wishlist').count = data.wishlist_count;
                        }
                        if (window.showToast) window.showToast(data.message, 'success');
                    } catch (e) {
                        if (window.showToast) window.showToast('Network error. Please try again.', 'error');
                    } finally {
                        this.wishlistBusy = false;
                    }
                },

                async share() {
                    const data = { title: @js($product->name), url: window.location.href };
                    if (navigator.share) { try { await navigator.share(data); } catch (e) {} }
                    else if (navigator.clipboard) { await navigator.clipboard.writeText(data.url); alert('Link copied'); }
                },
            }
        }
    </script>

    <nav class="flex items-center gap-2 text-xs text-muted py-4 overflow-x-auto no-scrollbar" aria-label="Breadcrumb">
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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-12 mb-12"
         x-data="productGallery({{ Js::from($galleryImages) }}, {{ $maxQty }}, {{ $initialWishlisted ? 'true' : 'false' }})"
         @keydown.window="onKey($event)">

        <div class="lg:sticky lg:top-24 lg:self-start">
            <div class="flex gap-3 lg:flex-row flex-col">

                @if (count($galleryImages) > 1)
                    <div class="hidden lg:flex flex-col gap-2.5 max-h-[560px] overflow-y-auto no-scrollbar shrink-0 py-0.5">
                        <template x-for="(img, i) in images" :key="'d' + i">
                            <button type="button"
                                    @click="go(i)"
                                    @mouseenter="go(i)"
                                    :aria-label="'Show image ' + (i + 1)"
                                    class="w-[72px] h-[72px] rounded-xl overflow-hidden bg-white border-2 p-1 transition shrink-0"
                                    :class="index === i ? 'border-moss ring-2 ring-moss/20' : 'border-line opacity-70 hover:opacity-100 hover:border-moss/40'">
                                <img :src="img" alt="" class="w-full h-full object-contain" loading="lazy">
                            </button>
                        </template>
                    </div>
                @endif

                <div class="relative flex-1 min-w-0">
                    <div class="relative rounded-2xl overflow-hidden bg-white border border-line aspect-square select-none group">
                        @if (count($galleryImages))
                            <div class="absolute inset-0 cursor-zoom-in"
                                 @click="open()"
                                 @mouseenter="enterLens()"
                                 @mouseleave="lens = false"
                                 @mousemove="moveLens($event)"
                                 @touchstart.passive="swipeStart($event)"
                                 @touchend.passive="swipeEnd($event)"
                                 role="button" tabindex="0"
                                 @keydown.enter.prevent="open()"
                                 aria-label="Open image full screen">

                                <img :src="active"
                                     alt="{{ $product->name }}"
                                     class="absolute inset-0 w-full h-full object-contain p-3 sm:p-5 transition-opacity duration-200"
                                     :class="lens ? 'opacity-0' : 'opacity-100'"
                                     draggable="false">

                                <div x-show="lens" x-cloak
                                     class="absolute inset-0 bg-white bg-no-repeat pointer-events-none"
                                     :style="`background-image:url('${active}');background-size:220%;background-position:${lensX}% ${lensY}%`"></div>
                            </div>

                            <div class="absolute top-3 left-3 flex flex-col gap-2 items-start pointer-events-none">
                                @if ($product->is_featured)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-gold text-ink shadow-sm">
                                        <i class="fas fa-star text-[10px]"></i> Featured
                                    </span>
                                @endif
                                @if ($product->isOnSale())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-500 text-white shadow-sm">
                                        -{{ $product->discountPercent() }}% OFF
                                    </span>
                                @endif
                            </div>

                            <div class="absolute top-3 right-3 flex flex-col gap-2">
                                <button type="button"
                                        @click.stop="toggleWishlist()"
                                        :disabled="wishlistBusy"
                                        :aria-pressed="wishlisted"
                                        aria-label="Toggle wishlist for {{ $product->name }}"
                                        class="w-10 h-10 rounded-full bg-white/90 backdrop-blur border border-line flex items-center justify-center shadow-sm transition active:scale-90 hover:scale-110 focus:outline-none focus-visible:ring-2 focus-visible:ring-moss disabled:opacity-60"
                                        :class="wishlisted ? 'text-red-500 border-red-200 hover:bg-white hover:text-red-600' : 'text-ink/50 hover:bg-white hover:text-red-500'">
                                    <i class="fa-heart text-sm" :class="wishlisted ? 'fas' : 'far'"></i>
                                </button>
                                <button type="button" @click.stop="open()" aria-label="Zoom image"
                                        class="w-10 h-10 rounded-full bg-white/90 backdrop-blur border border-line text-ink flex items-center justify-center shadow-sm hover:bg-white hover:text-moss transition">
                                    <i class="fas fa-expand text-sm"></i>
                                </button>
                                <button type="button" @click.stop="share()" aria-label="Share product"
                                        class="w-10 h-10 rounded-full bg-white/90 backdrop-blur border border-line text-ink flex items-center justify-center shadow-sm hover:bg-white hover:text-moss transition">
                                    <i class="fas fa-share-alt text-sm"></i>
                                </button>
                            </div>

                            @if (count($galleryImages) > 1)
                                <button type="button" @click.stop="prev()" aria-label="Previous image"
                                        class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/90 border border-line text-ink flex items-center justify-center shadow-sm hover:bg-white opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition">
                                    <i class="fas fa-chevron-left text-sm"></i>
                                </button>
                                <button type="button" @click.stop="next()" aria-label="Next image"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/90 border border-line text-ink flex items-center justify-center shadow-sm hover:bg-white opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition">
                                    <i class="fas fa-chevron-right text-sm"></i>
                                </button>

                                <span class="absolute bottom-3 left-3 px-2.5 py-1 rounded-full bg-ink/70 text-white text-xs font-medium pointer-events-none">
                                    <span x-text="index + 1"></span> / <span x-text="images.length"></span>
                                </span>
                            @endif

                            <span class="hidden lg:flex absolute bottom-3 right-3 items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/90 border border-line text-xs text-muted pointer-events-none transition-opacity"
                                  :class="lens ? 'opacity-0' : 'opacity-100'">
                                <i class="fas fa-search-plus text-[10px]"></i> Hover to zoom, click to enlarge
                            </span>

                            @if (!$product->inStock())
                                <div class="absolute inset-0 bg-ink/50 flex items-center justify-center pointer-events-none">
                                    <span class="px-4 py-2 rounded-full bg-white text-ink text-sm font-semibold uppercase tracking-wide">
                                        Out of stock
                                    </span>
                                </div>
                            @endif
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center gap-2 bg-paper text-ink/30">
                                <i class="fas fa-image text-6xl"></i>
                                <span class="text-xs">No image available</span>
                            </div>
                        @endif
                    </div>

                    @if (count($galleryImages) > 1)
                        <div class="flex lg:hidden gap-2 mt-3 overflow-x-auto no-scrollbar">
                            <template x-for="(img, i) in images" :key="'m' + i">
                                <button type="button" @click="go(i)"
                                        :aria-label="'Show image ' + (i + 1)"
                                        class="shrink-0 w-16 h-16 rounded-xl overflow-hidden bg-white border-2 p-1 transition"
                                        :class="index === i ? 'border-moss ring-2 ring-moss/20' : 'border-line opacity-70'">
                                    <img :src="img" alt="" class="w-full h-full object-contain" loading="lazy">
                                </button>
                            </template>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-5">

            <div>
                @if ($product->brand)
                    <a href="#" class="inline-flex items-center gap-1.5 text-sm font-semibold text-moss hover:text-moss/70 transition">
                        {{ $product->brand->name }}
                        @if ($product->brand->is_verified)
                            <i class="fas fa-circle-check text-blue-500 text-xs" title="Verified brand"></i>
                        @endif
                    </a>
                @endif
                <h1 class="font-serif text-2xl sm:text-3xl lg:text-4xl font-semibold text-ink leading-tight mt-1">
                    {{ $product->name }}
                </h1>

                <div class="flex items-center gap-3 mt-3 text-xs text-muted flex-wrap">
                    @if ($product->rating_count > 0)
                        <a href="#reviews" class="flex items-center gap-1 text-moss font-semibold hover:underline">
                            <i class="fas fa-star text-gold text-xs"></i>
                            {{ number_format((float) $product->rating_avg, 1) }}
                            <span class="text-muted font-normal">({{ $product->rating_count }} {{ Str::plural('review', $product->rating_count) }})</span>
                        </a>
                        <span class="text-line">|</span>
                    @endif
                    <span class="font-mono">SKU: {{ $product->sku }}</span>
                </div>
            </div>

            @if ($product->short_description)
                <p class="text-sm sm:text-base text-ink/80 leading-relaxed">
                    {{ $product->short_description }}
                </p>
            @endif

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
                            <span class="w-2 h-2 rounded-full bg-moss"></span> In stock ({{ $product->stock_quantity }} available)
                        </span>
                    @endif
                </div>
            </div>

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

            <div class="space-y-3 pt-2">
                @if ($product->inStock())
                    @auth
                        <div class="flex items-center gap-3">
                            <div class="flex items-center border border-line rounded-full overflow-hidden bg-white">
                                <button type="button" @click="dec()" aria-label="Decrease quantity"
                                        class="w-11 h-11 flex items-center justify-center text-ink hover:bg-paper transition">
                                    <i class="fas fa-minus text-xs"></i>
                                </button>
                                <input type="number" x-model.number="qty" @change="clampQty()" min="1" max="{{ $maxQty }}"
                                       aria-label="Quantity"
                                       class="w-12 text-center border-0 focus:ring-0 text-sm font-semibold bg-white p-0 [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">
                                <button type="button" @click="inc()" aria-label="Increase quantity"
                                        class="w-11 h-11 flex items-center justify-center text-ink hover:bg-paper transition">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>

                            <button type="button"
                                    @click="addToCart()"
                                    :disabled="adding"
                                    :class="added ? 'bg-emerald-600' : 'bg-moss hover:bg-moss/90'"
                                    class="flex-1 text-white font-semibold py-3 px-6 rounded-full transition flex items-center justify-center gap-2 shadow-[0_10px_24px_-8px_rgba(32,80,60,0.55)] disabled:opacity-80 disabled:cursor-wait">
                                <i class="fas" :class="added ? 'fa-check' : (adding ? 'fa-spinner fa-spin' : 'fa-shopping-cart')"></i>
                                <span x-text="added ? 'Added to cart' : (adding ? 'Adding…' : 'Add to cart')"></span>
                            </button>
                        </div>

                        <button type="button"
                                class="w-full border-2 border-ink text-ink font-semibold py-3 rounded-full hover:bg-ink hover:text-white transition">
                            <i class="fas fa-bolt text-xs"></i> Buy now
                        </button>
                    @else
                        <a href="{{ route('login') }}"
                           class="w-full bg-moss text-white font-semibold py-3 px-6 rounded-full transition flex items-center justify-center gap-2 shadow-[0_10px_24px_-8px_rgba(32,80,60,0.55)] hover:bg-moss/90">
                            <i class="fas fa-lock text-xs"></i> Log in to add to cart
                        </a>
                    @endauth
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

        <template x-teleport="body">
            <div x-show="lightbox" x-cloak
                 x-transition.opacity.duration.150ms
                 class="fixed inset-0 z-[100] bg-black/95 flex flex-col"
                 role="dialog" aria-modal="true" aria-label="Product image viewer"
                 @pointermove.window="dragMove($event)"
                 @pointerup.window="dragEnd()"
                 @pointercancel.window="dragEnd()">

                <div class="flex items-center justify-between px-4 sm:px-6 py-3 text-white shrink-0">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold truncate">{{ $product->name }}</p>
                        <p class="text-xs text-white/60" x-show="multiple">
                            Image <span x-text="index + 1"></span> of <span x-text="images.length"></span>
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="zoomBy(-0.5)" :disabled="scale <= 1" aria-label="Zoom out"
                                class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 disabled:opacity-30 flex items-center justify-center transition">
                            <i class="fas fa-search-minus text-sm"></i>
                        </button>
                        <span class="w-12 text-center text-xs tabular-nums text-white/80" x-text="Math.round(scale * 100) + '%'"></span>
                        <button type="button" @click="zoomBy(0.5)" :disabled="scale >= 4" aria-label="Zoom in"
                                class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 disabled:opacity-30 flex items-center justify-center transition">
                            <i class="fas fa-search-plus text-sm"></i>
                        </button>
                        <button type="button" @click="close()" aria-label="Close"
                                class="w-10 h-10 ml-2 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="relative flex-1 min-h-0 overflow-hidden flex items-center justify-center"
                     @click.self="close()"
                     @wheel.prevent="wheelZoom($event)">

                    <img :src="active"
                         alt="{{ $product->name }}"
                         draggable="false"
                         @dblclick="toggleZoom()"
                         @pointerdown.prevent="dragStart($event)"
                         class="max-w-full max-h-full object-contain select-none touch-none"
                         :class="scale > 1 ? (dragging ? 'cursor-grabbing' : 'cursor-grab') : 'cursor-zoom-in'"
                         :style="`transform: translate(${panX}px, ${panY}px) scale(${scale}); transition: ${dragging ? 'none' : 'transform .2s ease'}`">

                    <template x-if="multiple">
                        <div>
                            <button type="button" @click="prev()" aria-label="Previous image"
                                    class="absolute left-3 sm:left-6 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center transition">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button type="button" @click="next()" aria-label="Next image"
                                    class="absolute right-3 sm:right-6 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center transition">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </template>
                </div>

                <div class="shrink-0 px-4 py-3 flex flex-col items-center gap-2">
                    <div class="flex gap-2 overflow-x-auto no-scrollbar max-w-full" x-show="multiple">
                        <template x-for="(img, i) in images" :key="'l' + i">
                            <button type="button" @click="go(i)" :aria-label="'Show image ' + (i + 1)"
                                    class="shrink-0 w-14 h-14 rounded-lg overflow-hidden bg-white/10 border-2 p-0.5 transition"
                                    :class="index === i ? 'border-white' : 'border-transparent opacity-60 hover:opacity-100'">
                                <img :src="img" alt="" class="w-full h-full object-contain">
                            </button>
                        </template>
                    </div>
                    <p class="text-[11px] text-white/50 hidden sm:block">
                        Scroll or double-click to zoom, drag to move, arrow keys to browse, Esc to close
                    </p>
                </div>
            </div>
        </template>
    </div>

    <div class="bg-white border border-line rounded-2xl overflow-hidden mb-8"
         x-data="{ tab: 'description' }">

        <div class="flex border-b border-line overflow-x-auto no-scrollbar" role="tablist">
            <button type="button" role="tab" @click="tab = 'description'"
                    :aria-selected="tab === 'description'"
                    :class="tab === 'description' ? 'text-ink border-b-2 border-moss font-semibold' : 'text-muted hover:text-ink'"
                    class="px-5 sm:px-6 py-4 text-sm transition whitespace-nowrap">
                Description
            </button>
            <button type="button" role="tab" @click="tab = 'specs'"
                    :aria-selected="tab === 'specs'"
                    :class="tab === 'specs' ? 'text-ink border-b-2 border-moss font-semibold' : 'text-muted hover:text-ink'"
                    class="px-5 sm:px-6 py-4 text-sm transition whitespace-nowrap">
                Specifications
                @php
                    $attrCount = count($product->attributes ?? []);
                @endphp
                @if ($attrCount > 0)
                    <span class="ml-1 text-xs text-muted">({{ $attrCount }})</span>
                @endif
            </button>
        </div>

        <div x-show="tab === 'description'" class="p-5 sm:p-6">
            @if ($product->description)
                <div class="prose prose-sm max-w-3xl text-ink/80 leading-relaxed
                            prose-headings:font-serif prose-headings:text-ink
                            prose-p:my-3 prose-ul:my-3 prose-ol:my-3 prose-li:my-1
                            prose-strong:text-ink prose-a:text-moss prose-a:no-underline hover:prose-a:underline
                            prose-img:rounded-xl prose-img:my-4">
                    {!! strip_tags(
                        $product->description,
                        '<p><br><br/><strong><b><em><i><u><s><ul><ol><li><h1><h2><h3><h4><h5><h6><blockquote><code><pre><hr><a><img><table><thead><tbody><tr><th><td><span><small><sup><sub>'
                    ) !!}
                </div>
            @else
                <p class="text-sm text-muted italic">No description provided.</p>
            @endif
        </div>

        <div x-show="tab === 'specs'" x-cloak class="p-5 sm:p-6">
            @php
                $productAttrs = $product->attributes ?? [];
            @endphp

            @if (!empty($productAttrs))
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-1">
                    @foreach ($attributeDefinitions as $def)
                        @if (array_key_exists($def->key, $productAttrs))
                            <div class="flex items-baseline justify-between gap-4 py-2.5 border-b border-line/60">
                                <dt class="text-xs text-muted uppercase tracking-wide">{{ $def->label }}</dt>
                                <dd class="text-sm text-ink font-medium text-right">
                                    {{ $def->displayValue($productAttrs[$def->key]) }}
                                </dd>
                            </div>
                        @endif
                    @endforeach

                    @foreach ($productAttrs as $key => $value)
                        @if (!$attributeDefinitions->contains('key', $key))
                            <div class="flex items-baseline justify-between gap-4 py-2.5 border-b border-line/60">
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

    <div id="reviews" class="scroll-mt-24">
        @include('shop.partials.product-reviews', ['product' => $product])
    </div>

    @if ($related->isNotEmpty())
        <section class="mb-12 mt-10">
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

</x-shop-layout>
