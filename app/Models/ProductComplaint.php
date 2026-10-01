<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductComplaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'reason',
        'details',
        'images',
        'status',
        'resolved_by',
        'resolved_at',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'resolved_at' => 'datetime',
        ];
    }

    // ============================================
    // REASON LABELS
    // ============================================

    public const REASONS = [
        'counterfeit' => 'Counterfeit or fake',
        'misleading' => 'Misleading description or images',
        'damaged' => 'Arrived damaged',
        'not_received' => 'Never received',
        'restricted' => 'Restricted / prohibited item',
        'offensive' => 'Offensive or inappropriate',
        'other' => 'Other',
    ];

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

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeReviewing($query)
    {
        return $query->where('status', 'reviewing');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeDismissed($query)
    {
        return $query->where('status', 'dismissed');
    }

    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['open', 'reviewing']);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // ============================================
    // HELPERS
    // ============================================

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    public function reasonLabel(): string
    {
        return self::REASONS[$this->reason] ?? ucfirst((string) $this->reason);
    }

    /**
     * Move to "reviewing" state.
     */
    public function markReviewing(): void
    {
        $this->update(['status' => 'reviewing']);
    }

    /**
     * Mark resolved by an admin.
     */
    public function resolve(User $by, ?string $notes = null): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_by' => $by->id,
            'resolved_at' => now(),
            'admin_notes' => $notes ?? $this->admin_notes,
        ]);
    }

    /**
     * Dismiss (not actionable) by an admin.
     */
    public function dismiss(User $by, ?string $notes = null): void
    {
        $this->update([
            'status' => 'dismissed',
            'resolved_by' => $by->id,
            'resolved_at' => now(),
            'admin_notes' => $notes ?? $this->admin_notes,
        ]);
    }
}
