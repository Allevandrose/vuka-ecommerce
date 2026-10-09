{{-- Reviews section — read-only display of approved reviews --}}
<section class="mb-10 bg-white border border-line rounded-2xl overflow-hidden">
    <div class="p-6 border-b border-line">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <h3 class="font-serif text-xl font-semibold flex items-center gap-2">
                <i class="fas fa-star text-gold text-base"></i> Customer reviews
            </h3>

            @if ($product->rating_count > 0)
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-0.5 text-gold">
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= round((float) $product->rating_avg))
                                <i class="fas fa-star text-sm"></i>
                            @else
                                <i class="far fa-star text-sm"></i>
                            @endif
                        @endfor
                    </div>
                    <span class="text-sm font-semibold text-ink">
                        {{ number_format((float) $product->rating_avg, 1) }}
                    </span>
                    <span class="text-xs text-muted">
                        ({{ $product->rating_count }} {{ Str::plural('review', $product->rating_count) }})
                    </span>
                </div>
            @endif
        </div>
    </div>

    @if ($product->reviews->isEmpty())
        <div class="p-12 text-center">
            <i class="far fa-comment-dots text-4xl text-muted/40 mb-3"></i>
            <p class="text-sm text-muted">No reviews yet. Be the first to review this product!</p>
            <p class="text-xs text-muted/70 mt-1">Reviews from verified buyers will appear here.</p>
        </div>
    @else
        {{-- Rating breakdown summary --}}
        <div class="p-6 bg-paper border-b border-line">
            <div class="grid grid-cols-5 gap-2 max-w-md">
                @for ($stars = 5; $stars >= 1; $stars--)
                    @php
                        $count = $product->reviews->where('rating', $stars)->count();
                        $pct = $product->rating_count > 0 ? ($count / $product->rating_count) * 100 : 0;
                    @endphp
                    <div class="flex items-center gap-2 col-span-5">
                        <span class="text-xs text-muted w-6">{{ $stars }}★</span>
                        <div class="flex-1 h-2 bg-line rounded-full overflow-hidden">
                            <div class="h-full bg-gold transition-all" style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="text-xs text-muted w-8 text-right">{{ $count }}</span>
                    </div>
                @endfor
            </div>
        </div>

        {{-- Review list --}}
        <div class="divide-y divide-line">
            @foreach ($product->reviews as $review)
                <article class="p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-paper flex items-center justify-center text-moss font-bold shrink-0">
                            {{ strtoupper(substr($review->user?->name ?? 'U', 0, 1)) }}
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-semibold text-sm text-ink">
                                    {{ $review->user?->name ?? 'Anonymous' }}
                                </span>
                                @if ($review->is_verified_purchase)
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700">
                                        <i class="fas fa-check-circle text-[8px]"></i> Verified purchase
                                    </span>
                                @endif
                                <span class="text-xs text-muted">
                                    {{ $review->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <div class="flex items-center gap-0.5 mt-1 text-gold text-sm">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $review->rating)
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="far fa-star text-line"></i>
                                    @endif
                                @endfor
                            </div>

                            @if ($review->title)
                                <h4 class="font-semibold text-sm text-ink mt-2">{{ $review->title }}</h4>
                            @endif

                            @if ($review->body)
                                <p class="text-sm text-ink/80 mt-1 leading-relaxed">{{ $review->body }}</p>
                            @endif

                            @if (!empty($review->images))
                                <div class="flex gap-2 mt-3 overflow-x-auto no-scrollbar">
                                    @foreach ($review->images as $image)
                                        <div class="shrink-0 w-20 h-20 rounded-lg overflow-hidden border border-line">
                                            <img src="{{ asset('storage/' . $image['path']) }}"
                                                 alt="Review image"
                                                 class="w-full h-full object-cover">
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="flex items-center gap-4 mt-3 text-xs text-muted">
                                <button type="button"
                                        class="inline-flex items-center gap-1.5 hover:text-moss transition">
                                    <i class="far fa-thumbs-up"></i> Helpful ({{ $review->helpful_count }})
                                </button>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</section>