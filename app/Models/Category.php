<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'icon',
        'hero_image',
        'primary_color',
        'accent_color',
        'gradient_css',
        'accent_gradient_css',
        'theme',
        'is_active',
        'sort_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    // ============================================
    // BOOT — auto-slug
    // ============================================

    protected static function booted(): void
    {
        static::creating(function (Category $category) {
            if (empty($category->slug)) {
                $category->slug = static::generateUniqueSlug($category->name);
            }
        });

        static::updating(function (Category $category) {
            if ($category->isDirty('name') && !$category->isDirty('slug')) {
                $category->slug = static::generateUniqueSlug($category->name, $category->id);
            }
        });
    }

    protected static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (
            static::where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    // ============================================
    // RELATIONSHIPS
    // ============================================

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * All descendants, recursively. Use sparingly — can be heavy.
     * For deep trees use a closure table or path column. This is fine for
     * a 2–3 level catalog.
     */
    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class)->orderBy('sort_order');
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // ============================================
    // HELPERS
    // ============================================

    public function isTopLevel(): bool
    {
        return $this->parent_id === null;
    }

    /**
     * Get the full breadcrumb path from root to this category.
     * Returns an array of ['id', 'name', 'slug'] entries.
     */
    public function breadcrumb(): array
    {
        $trail = [];
        $node = $this;

        while ($node) {
            array_unshift($trail, [
                'id' => $node->id,
                'name' => $node->name,
                'slug' => $node->slug,
            ]);
            $node = $node->parent;
        }

        return $trail;
    }

    /**
     * Full path string, e.g. "electronics/smartphones/android".
     */
    public function path(): string
    {
        return collect($this->breadcrumb())->pluck('slug')->implode('/');
    }

    // ============================================
    // THEMING
    // ============================================

    /**
     * The hero background CSS value.
     * Priority: gradient_css → primary_color → transparent (theme default).
     */
    public function heroBackground(): string
    {
        return $this->gradient_css
            ?? $this->primary_color
            ?? 'transparent';
    }

    /**
     * The accent background for buttons / borders / badges.
     * Priority: accent_gradient_css → accent_color → primary_color → transparent.
     */
    public function accentBackground(): string
    {
        return $this->accent_gradient_css
            ?? $this->accent_color
            ?? $this->primary_color
            ?? 'transparent';
    }

    /**
     * The Blade layout to render for this category's public page.
     * Falls back to 'default' if the theme is empty or unknown.
     */
    public function viewName(): string
    {
        $theme = $this->theme ?: 'default';

        // Only allow known-safe theme names — protects against path traversal
        // if theme is user-editable.
        if (!preg_match('/^[a-z0-9_-]+$/i', $theme)) {
            $theme = 'default';
        }

        return 'categories.' . $theme;
    }
}
