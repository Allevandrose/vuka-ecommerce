<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        ];
    }

    // Type check methods
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

    public function isStaff(): bool
    {
        return in_array($this->user_type, ['admin', 'delivery', 'pickup']);
    }

    public function isAccountActive(): bool
    {
        if ($this->isStaff()) {
            return $this->is_active && $this->approved_at !== null;
        }
        return $this->is_active;
    }

    /**
     * Check if user can access the system
     * Uses caching for performance
     */
    public function canAccessSystem(): bool
    {
        $cacheKey = "user_access_{$this->id}";

        return Cache::remember($cacheKey, 60, function () {
            if ($this->isCustomer()) {
                return $this->is_active;
            }

            // Staff validation
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
     * Clear access cache when user status changes
     */
    public function clearAccessCache(): void
    {
        Cache::forget("user_access_{$this->id}");
    }

    public function isWeekendDisabled(): bool
    {
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

    public function getRedirectRoute(): string
    {
        if (!$this->canAccessSystem()) {
            return route('account.inactive');
        }

        // Use route names instead of URLs for consistency
        return match ($this->user_type) {
            'admin' => route('admin.dashboard'),
            'customer' => route('customer.dashboard'),
            'delivery' => route('delivery.dashboard'),
            'pickup' => route('pickup.dashboard'),
            default => route('dashboard'),
        };
    }

    public function getDashboardRoute(): string
    {
        return $this->getRedirectRoute();
    }

    /**
     * Get the appropriate error message based on user status
     */
    public function getAccessErrorMessage(): string
    {
        if (!$this->is_active) {
            return 'Your account is deactivated. Please contact the administrator.';
        }

        if ($this->isStaff() && !$this->approved_at) {
            return 'Your account is pending approval. Please wait for admin confirmation.';
        }

        if ($this->isWeekendDisabled()) {
            return 'Staff access is restricted on weekends. Please contact the administrator for override.';
        }

        return 'Access denied. Please contact the administrator.';
    }

    /**
     * Get dashboard route name (without full URL)
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
            default => 'dashboard',
        };
    }

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
}
