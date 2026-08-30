<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        return true;
    }

    public function canAccessSystem(): bool
    {
        if ($this->isCustomer()) {
            return true;
        }

        return $this->isAccountActive() && !$this->isWeekendDisabled();
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

        return match ($this->user_type) {
            'admin' => '/admin/dashboard',
            'customer' => '/customer/dashboard',
            'delivery' => '/delivery/dashboard',
            'pickup' => '/pickup/dashboard',
            default => '/dashboard',
        };
    }

    public function getDashboardRoute(): string
    {
        return $this->getRedirectRoute();
    }

    public function activate(): void
    {
        $this->is_active = true;
        $this->approved_at = now();
        $this->save();
    }

    public function deactivate(): void
    {
        $this->is_active = false;
        $this->save();
    }

    public function toggleWeekendOverride(): void
    {
        $this->is_weekend_override = !$this->is_weekend_override;
        $this->save();
    }
}
