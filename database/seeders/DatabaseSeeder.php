<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ============================================
        // ADMIN - Fully Active
        // Email: admin@vuka.com
        // Password: admin
        // ============================================
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@vuka.com',
            'password' => Hash::make('admin'),
            'user_type' => 'admin',
            'email_verified_at' => now(),
            'is_active' => true,
            'approved_at' => now(),
            'is_weekend_override' => true,
        ]);

        // ============================================
        // CUSTOMER - Fully Active
        // Email: customer@vuka.com
        // Password: customer
        // ============================================
        User::create([
            'name' => 'Customer User',
            'email' => 'customer@vuka.com',
            'password' => Hash::make('customer'),
            'user_type' => 'customer',
            'email_verified_at' => now(),
            'is_active' => true,
            'approved_at' => now(),
            'is_weekend_override' => false,
        ]);

        // ============================================
        // DELIVERY STAFF - Activated (for testing)
        // Email: delivery@vuka.com
        // Password: delivery
        // ============================================
        User::create([
            'name' => 'Delivery Staff',
            'email' => 'delivery@vuka.com',
            'password' => Hash::make('delivery'),
            'user_type' => 'delivery',
            'email_verified_at' => now(),
            'is_active' => true,        // Active
            'approved_at' => now(),     // Approved
            'is_weekend_override' => false,
        ]);

        // ============================================
        // PICKUP STAFF - Activated (for testing)
        // Email: pickup@vuka.com
        // Password: pickup
        // ============================================
        User::create([
            'name' => 'Pickup Staff',
            'email' => 'pickup@vuka.com',
            'password' => Hash::make('pickup'),
            'user_type' => 'pickup',
            'email_verified_at' => now(),
            'is_active' => true,        // Active
            'approved_at' => now(),     // Approved
            'is_weekend_override' => false,
        ]);

        // ============================================
        // PENDING DELIVERY (for testing approval flow)
        // Email: pending@vuka.com
        // Password: pending
        // ============================================
        User::create([
            'name' => 'Pending Staff',
            'email' => 'pending@vuka.com',
            'password' => Hash::make('pending'),
            'user_type' => 'delivery',
            'email_verified_at' => now(),
            'is_active' => false,
            'approved_at' => null,
            'is_weekend_override' => false,
        ]);

        // ============================================
        // ADDITIONAL RANDOM USERS (Customers only)
        // ============================================
        User::factory(10)->create();
    }
}
