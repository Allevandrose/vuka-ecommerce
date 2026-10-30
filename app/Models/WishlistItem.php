<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WishlistItem extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'product_id' => 'integer',
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
     * The live product this wishlist row points at.
     * Products use SoftDeletes — a soft-deleted product still resolves
     * here, but WishlistController filters those out of the rendered
     * wishlist. A hard delete cascades and removes this row entirely.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
