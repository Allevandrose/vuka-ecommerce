<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'order_id',
        'rating',
        'title',
        'body',
        'images',
        'is_verified_purchase',
        'is_approved',
        'helpful_count',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'images' => 'array',
            'is_verified_purchase' => 'boolean',
            'is_approved' => 'boolean',
            'helpful_count' => 'integer',
        ];
    }

    // ============================================
    // RELATIONSHIPS
    // ============================================

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    public function scopeVerifiedPurchase($query)
    {
        return $query->where('is_verified_purchase', true);
    }

    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeHelpful($query)
    {
        return $query->orderBy('helpful_count', 'desc');
    }

    // ============================================
    // HELPERS
    // ============================================

    public function approve(): void
    {
        $this->update(['is_approved' => true]);
    }

    public function unapprove(): void
    {
        $this->update(['is_approved' => false]);
    }

    /**
     * Star display, e.g. "★★★★☆".
     */
    public function stars(): string
    {
        $rating = max(0, min(5, (int) $this->rating));
        return str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
    }

    // ============================================
    // BOOT — recompute product rating on save/delete
    // ============================================

    protected static function booted(): void
    {
        static::saved(function (ProductReview $review) {
            $review->product?->recomputeRating();
        });

        static::deleted(function (ProductReview $review) {
            $review->product?->recomputeRating();
        });
    }
}
