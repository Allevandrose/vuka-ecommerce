<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'key',
        'label',
        'type',
        'options',
        'unit',
        'is_filterable',
        'is_required',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'is_filterable' => 'boolean',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    // ============================================
    // RELATIONSHIPS
    // ============================================

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFilterable($query)
    {
        return $query->where('is_filterable', true);
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeGlobal($query)
    {
        return $query->whereNull('category_id');
    }

    public function scopeForCategory($query, int $categoryId)
    {
        return $query->where(function ($q) use ($categoryId) {
            $q->where('category_id', $categoryId)
                ->orWhereNull('category_id');
        });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('label');
    }

    // ============================================
    // HELPERS
    // ============================================

    public function isGlobal(): bool
    {
        return $this->category_id === null;
    }

    /**
     * Does this attribute expect a list of allowed values (select/multiselect)?
     */
    public function hasOptions(): bool
    {
        return in_array($this->type, ['select', 'multiselect'], true);
    }

    /**
     * Display string for a given raw value.
     * e.g. for a number attribute with unit 'GB' and value 8 → "8 GB"
     *      for a boolean attribute and value true → "Yes"
     */
    public function displayValue(mixed $raw): string
    {
        if ($raw === null || $raw === '') {
            return '—';
        }

        return match ($this->type) {
            'boolean' => $raw ? 'Yes' : 'No',
            'number' => $this->unit ? "{$raw} {$this->unit}" : (string) $raw,
            'multiselect' => is_array($raw) ? implode(', ', $raw) : (string) $raw,
            default => (string) $raw,
        };
    }
}
