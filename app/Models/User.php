<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'google_id',
        'is_active',
        'approved_at',
        'last_activity_at',
        'is_weekend_override',
        // Vendor fields
        'shop_name',
        'shop_address',
        'shop_latitude',
        'shop_longitude',
        'vendor_notes',
        // Product/vendor limit
        'product_limit',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'approved_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'is_weekend_override' => 'boolean',
            'shop_latitude' => 'decimal:7',
            'shop_longitude' => 'decimal:7',
            'product_limit' => 'integer',
        ];
    }

    // ============================================
    // TYPE CHECK METHODS
    // ============================================

    public function isAdmin(): bool
    {
        return $this->user_type === 'admin';
    }

    public function isCustomer(): bool
    {
        return $this->user_type === 'customer';
    }

    public function isDelivery(): bool
    {
        return $this->user_type === 'delivery';
    }

    public function isPickup(): bool
    {
        return $this->user_type === 'pickup';
    }

    public function isVendor(): bool
    {
        return $this->user_type === 'vendor';
    }

    /**
     * "Staff" = internal employees (admin, delivery, pickup).
     * Vendors are external partners and deliberately excluded.
     */
    public function isStaff(): bool
    {
        return in_array($this->user_type, ['admin', 'delivery', 'pickup']);
    }

    // ============================================
    // ACCESS CONTROL
    // ============================================

    public function isAccountActive(): bool
    {
        if ($this->isStaff() || $this->isVendor()) {
            return $this->is_active && $this->approved_at !== null;
        }
        return $this->is_active;
    }

    /**
     * Check if user can access the system.
     * Uses caching for performance.
     */
    public function canAccessSystem(): bool
    {
        $cacheKey = "user_access_{$this->id}";

        return Cache::remember($cacheKey, 60, function () {
            // Customers: only need is_active
            if ($this->isCustomer()) {
                return $this->is_active;
            }

            // Vendors: need is_active + approved_at (no weekend rule)
            if ($this->isVendor()) {
                return $this->is_active && $this->approved_at !== null;
            }

            // Staff: is_active + approved_at + weekend rule
            if (!$this->is_active) {
                return false;
            }

            if ($this->approved_at === null) {
                return false;
            }

            if ($this->isWeekendDisabled()) {
                return false;
            }

            return true;
        });
    }

    /**
     * Clear access cache when user status changes.
     */
    public function clearAccessCache(): void
    {
        Cache::forget("user_access_{$this->id}");
    }

    public function isWeekendDisabled(): bool
    {
        // Only staff are subject to weekend restrictions
        if (!$this->isStaff()) {
            return false;
        }

        if ($this->is_weekend_override) {
            return false;
        }

        $today = Carbon::now();
        $dayOfWeek = $today->dayOfWeek;

        return in_array($dayOfWeek, [0, 6]);
    }

    // ============================================
    // REDIRECT ROUTES
    // ============================================

    public function getRedirectRoute(): string
    {
        if (!$this->canAccessSystem()) {
            return route('account.inactive');
        }

        return match ($this->user_type) {
            'admin' => route('admin.dashboard'),
            'customer' => route('customer.dashboard'),
            'delivery' => route('delivery.dashboard'),
            'pickup' => route('pickup.dashboard'),
            'vendor' => route('vendor.dashboard'),
            default => route('dashboard'),
        };
    }

    public function getDashboardRoute(): string
    {
        return $this->getRedirectRoute();
    }

    /**
     * Get dashboard route name (without full URL).
     */
    public function getDashboardRouteName(): string
    {
        if (!$this->canAccessSystem()) {
            return 'account.inactive';
        }

        return match ($this->user_type) {
            'admin' => 'admin.dashboard',
            'customer' => 'customer.dashboard',
            'delivery' => 'delivery.dashboard',
            'pickup' => 'pickup.dashboard',
            'vendor' => 'vendor.dashboard',
            default => 'dashboard',
        };
    }

    /**
     * Get the appropriate error message based on user status.
     */
    public function getAccessErrorMessage(): string
    {
        if (!$this->is_active) {
            return 'Your account is deactivated. Please contact the administrator.';
        }

        if ($this->isVendor() && !$this->approved_at) {
            return 'Your vendor account is pending approval. Please wait for admin confirmation.';
        }

        if ($this->isStaff() && !$this->approved_at) {
            return 'Your account is pending approval. Please wait for admin confirmation.';
        }

        if ($this->isWeekendDisabled()) {
            return 'Staff access is restricted on weekends. Please contact the administrator for override.';
        }

        return 'Access denied. Please contact the administrator.';
    }

    // ============================================
    // STATE TRANSITIONS
    // ============================================

    public function activate(): void
    {
        $this->is_active = true;
        $this->approved_at = now();
        $this->save();
        $this->clearAccessCache();
    }

    public function deactivate(): void
    {
        $this->is_active = false;
        $this->save();
        $this->clearAccessCache();
    }

    public function toggleWeekendOverride(): void
    {
        $this->is_weekend_override = !$this->is_weekend_override;
        $this->save();
        $this->clearAccessCache();
    }

    // ============================================
    // PRODUCTS
    // ============================================

    /**
     * Products where this user is the source (vendor or admin owner).
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'source_id');
    }

    /**
     * The effective product cap for this user.
     * Priority: user-level limit → site-wide default from settings → 100.
     */
    public function productLimit(): int
    {
        if ($this->product_limit !== null) {
            return $this->product_limit;
        }

        return (int) Setting::get('vendor_product_cap', 100);
    }

    /**
     * How many products has this user created?
     * Soft-deleted products do not count.
     */
    public function productCount(): int
    {
        return $this->products()->count();
    }

    /**
     * Can this user create another product?
     */
    public function canCreateProduct(): bool
    {
        // Only vendors are capped. Admins are unlimited.
        if (!$this->isVendor()) {
            return true;
        }

        return $this->productCount() < $this->productLimit();
    }
}
