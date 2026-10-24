<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'product_id' => 'integer',
            'quantity' => 'integer',
        ];
    }

    // ============================================
    // RELATIONSHIPS
    // ============================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The live product this cart row points at.
     * Products use SoftDeletes — a soft-deleted product still resolves
     * here, but CartController filters those out of the rendered cart.
     * A hard delete cascades and removes this row entirely.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // ============================================
    // PRICING (live — no snapshot)
    // ============================================

    /**
     * Unit price pulled live from the product.
     * No snapshot — if the product price changes, the cart reflects it.
     */
    public function unitPrice(): float
    {
        return (float) ($this->product?->price ?? 0);
    }

    public function currency(): string
    {
        return $this->product?->currency ?? 'KES';
    }

    public function subtotal(): float
    {
        return $this->unitPrice() * $this->quantity;
    }

    /**
     * Formats match Product::formattedPrice() so the cart UI is
     * visually identical to product cards and detail pages.
     */
    public function formattedUnitPrice(): string
    {
        return $this->currency() . ' ' . number_format($this->unitPrice(), 2);
    }

    public function formattedSubtotal(): string
    {
        return $this->currency() . ' ' . number_format($this->subtotal(), 2);
    }
}
