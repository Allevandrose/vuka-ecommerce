<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'slug',
        'name',
        'sku',
        'short_description',
        'description',
        'category_id',
        'brand_id',
        'source_type',
        'source_id',
        'cost_price',
        'price',
        'compare_at_price',
        'currency',
        'stock_quantity',
        'track_inventory',
        'weight',
        'length',
        'width',
        'height',
        'is_local',
        'is_international',
        'lead_time_days',
        'is_verified',
        'is_certified',
        'condition',
        'attributes',
        'images',
        'primary_image',
        'thumbnail',
        'status',
        'rejection_reason',
        'disabled_reason',
        'disabled_by',
        'disabled_at',
        'is_featured',
        'published_at',
        'views_count',
        'sales_count',
        'rating_avg',
        'rating_count',
        'complaint_count',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'weight' => 'decimal:3',
            'length' => 'decimal:2',
            'width' => 'decimal:2',
            'height' => 'decimal:2',
            'stock_quantity' => 'integer',
            'track_inventory' => 'boolean',
            'is_local' => 'boolean',
            'is_international' => 'boolean',
            'lead_time_days' => 'integer',
            'is_verified' => 'boolean',
            'is_certified' => 'boolean',
            'attributes' => 'array',
            'images' => 'array',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'disabled_at' => 'datetime',
            'rating_avg' => 'decimal:2',
            'views_count' => 'integer',
            'sales_count' => 'integer',
            'rating_count' => 'integer',
            'complaint_count' => 'integer',
        ];
    }

    // ============================================
    // BOOT — auto uuid + slug + sku
    // ============================================

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->uuid)) {
                $product->uuid = (string) Str::uuid();
            }
            if (empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product->name);
            }
            if (empty($product->sku)) {
                $product->sku = static::generateSku($product);
            }
        });

        static::updating(function (Product $product) {
            if ($product->isDirty('name') && !$product->isDirty('slug')) {
                $product->slug = static::generateUniqueSlug($product->name, $product->id);
            }
        });
    }

    protected static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (
            static::withTrashed()
            ->where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    /**
     * Simple SKU generator — prefix + random suffix, unique.
     */
    protected static function generateSku(Product $product): string
    {
        $prefix = $product->source_type === 'vendor' ? 'VND' : 'ADM';

        do {
            $sku = $prefix . '-' . strtoupper(Str::random(8));
        } while (static::withTrashed()->where('sku', $sku)->exists());

        return $sku;
    }

    // ============================================
    // RELATIONSHIPS
    // ============================================

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * The user who created/owns this product.
     * For source_type = 'admin'  → admin user (or null for system-owned)
     * For source_type = 'vendor' → the vendor user
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(User::class, 'source_id');
    }

    public function disabledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disabled_by');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(ProductComplaint::class);
    }

    // ============================================
    // SCOPES — visibility & workflow
    // ============================================

    /**
     * Only products that should be visible to the public.
     */
    public function scopeVisible($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePendingReview($query)
    {
        return $query->where('status', 'pending_review');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeDisabled($query)
    {
        return $query->where('status', 'disabled');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeForVendor($query, int $vendorId)
    {
        return $query->where('source_type', 'vendor')
            ->where('source_id', $vendorId);
    }

    public function scopeAdminOwned($query)
    {
        return $query->where('source_type', 'admin');
    }

    // ============================================
    // HELPERS — source
    // ============================================

    public function isVendorProduct(): bool
    {
        return $this->source_type === 'vendor';
    }

    public function isAdminProduct(): bool
    {
        return $this->source_type === 'admin';
    }

    // ============================================
    // HELPERS — workflow
    // ============================================

    public function isVisible(): bool
    {
        return $this->status === 'active'
            && (!$this->published_at || $this->published_at->isPast());
    }

    public function isPendingReview(): bool
    {
        return $this->status === 'pending_review';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isDisabled(): bool
    {
        return $this->status === 'disabled';
    }

    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    /**
     * Admin approves a pending vendor product.
     */
    public function approve(): void
    {
        $this->update([
            'status' => 'active',
            'published_at' => $this->published_at ?? now(),
            'rejection_reason' => null,
        ]);
    }

    /**
     * Admin rejects a pending vendor product.
     */
    public function reject(string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);
    }

    /**
     * Admin disables a product — hides it from the entire site.
     */
    public function disable(User $by, string $reason): void
    {
        $this->update([
            'status' => 'disabled',
            'disabled_reason' => $reason,
            'disabled_by' => $by->id,
            'disabled_at' => now(),
        ]);
    }

    /**
     * Admin re-enables a disabled product.
     */
    public function enable(): void
    {
        $this->update([
            'status' => 'active',
            'disabled_reason' => null,
            'disabled_by' => null,
            'disabled_at' => null,
        ]);
    }

    public function archive(): void
    {
        $this->update(['status' => 'archived']);
    }

    // ============================================
    // HELPERS — pricing
    // ============================================

    public function isOnSale(): bool
    {
        return $this->compare_at_price !== null
            && (float) $this->compare_at_price > (float) $this->price;
    }

    public function discountPercent(): ?int
    {
        if (!$this->isOnSale()) {
            return null;
        }

        $orig = (float) $this->compare_at_price;
        $now = (float) $this->price;

        return (int) round((($orig - $now) / $orig) * 100);
    }

    public function formattedPrice(): string
    {
        return $this->currency . ' ' . number_format((float) $this->price, 2);
    }

    public function formattedCompareAtPrice(): ?string
    {
        if ($this->compare_at_price === null) {
            return null;
        }

        return $this->currency . ' ' . number_format((float) $this->compare_at_price, 2);
    }

    // ============================================
    // HELPERS — inventory
    // ============================================

    public function inStock(): bool
    {
        return !$this->track_inventory || $this->stock_quantity > 0;
    }

    public function isLowStock(int $threshold = 5): bool
    {
        return $this->track_inventory
            && $this->stock_quantity > 0
            && $this->stock_quantity <= $threshold;
    }

    // ============================================
    // HELPERS — reviews
    // ============================================

    /**
     * Recompute and persist rating_avg and rating_count from approved reviews.
     * Called automatically when a review is saved or deleted.
     */
    public function recomputeRating(): void
    {
        $stats = $this->reviews()
            ->where('is_approved', true)
            ->selectRaw('COUNT(*) as count, COALESCE(AVG(rating), 0) as avg')
            ->first();

        $this->updateQuietly([
            'rating_count' => (int) ($stats->count ?? 0),
            'rating_avg' => round((float) ($stats->avg ?? 0), 2),
        ]);
    }

    // ============================================
    // HELPERS — attributes & images
    // ============================================

    public function getCustomAttribute(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    /**
     * Sorted images list.
     */
    public function sortedImages(): array
    {
        $images = $this->images ?? [];

        usort($images, fn($a, $b) => ($a['sort'] ?? 0) <=> ($b['sort'] ?? 0));

        return $images;
    }

    public function imageUrl(): string
    {
        return $this->primary_image
            ?? $this->thumbnail
            ?? '/images/placeholder.png';
    }

    // ============================================
    // HELPERS — SEO
    // ============================================

    public function seoTitle(): string
    {
        return $this->meta_title ?: $this->name;
    }

    public function seoDescription(): string
    {
        return $this->meta_description
            ?: $this->short_description
            ?: Str::limit(strip_tags((string) $this->description), 155);
    }
}
