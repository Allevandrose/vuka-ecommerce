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
        // Email: ibrahimmulei@gmail.com
        // Password: admin@vuka
        // ============================================
        User::create([
            'name' => 'Admin User',
            'email' => 'ibrahimmulei@gmail.com',
            'password' => Hash::make('admin@vuka'),
            'user_type' => 'admin',
            'email_verified_at' => now(),
            'is_active' => true,
            'approved_at' => now(),
            'is_weekend_override' => true,
        ]);

        // ============================================
        // CUSTOMER - Fully Active
        // Email: customer@vuka.com
        // Password: customer@vuka
        // ============================================
        User::create([
            'name' => 'Customer User',
            'email' => 'customer@vuka.com',
            'password' => Hash::make('customer@vuka'),
            'user_type' => 'customer',
            'email_verified_at' => now(),
            'is_active' => true,
            'approved_at' => now(),
            'is_weekend_override' => false,
        ]);

        // ============================================
        // DELIVERY STAFF - Activated (for testing)
        // Email: delivery@vuka.com
        // Password: delivery@vuka
        // ============================================
        User::create([
            'name' => 'Delivery Staff',
            'email' => 'delivery@vuka.com',
            'password' => Hash::make('delivery@vuka'),
            'user_type' => 'delivery',
            'email_verified_at' => now(),
            'is_active' => true,
            'approved_at' => now(),
            'is_weekend_override' => false,
        ]);

        // ============================================
        // PICKUP STAFF - Activated (for testing)
        // Email: pickup@vuka.com
        // Password: pickup@vuka
        // ============================================
        User::create([
            'name' => 'Pickup Staff',
            'email' => 'pickup@vuka.com',
            'password' => Hash::make('pickup@vuka'),
            'user_type' => 'pickup',
            'email_verified_at' => now(),
            'is_active' => true,
            'approved_at' => now(),
            'is_weekend_override' => false,
        ]);

        // ============================================
        // PENDING DELIVERY (for testing approval flow)
        // Email: pending@vuka.com
        // Password: pending@vuka
        // ============================================
        User::create([
            'name' => 'Pending Staff',
            'email' => 'pending@vuka.com',
            'password' => Hash::make('pending@vuka'),
            'user_type' => 'delivery',
            'email_verified_at' => now(),
            'is_active' => false,
            'approved_at' => null,
            'is_weekend_override' => false,
        ]);

        // ============================================
        // VENDOR - Active (for testing vendor login)
        // Email: vendor@vuka.com
        // Password: vendor@vuka
        // ============================================
        User::create([
            'name' => 'Test Vendor',
            'email' => 'vendor@vuka.com',
            'password' => Hash::make('vendor@vuka'),
            'user_type' => 'vendor',
            'email_verified_at' => now(),
            'is_active' => true,
            'approved_at' => now(),
            'is_weekend_override' => false,
            'shop_name' => 'Test Vendor Shop',
            'shop_address' => '456 Test Avenue, Nairobi',
            'shop_latitude' => -1.2920660,
            'shop_longitude' => 36.8219460,
            'vendor_notes' => 'Seeded for testing — active vendor.',
        ]);

        // ============================================
        // VENDOR - Pending activation (for testing the
        // "account pending" error on login)
        // Email: pending-vendor@vuka.com
        // Password: vendor@vuka
        // ============================================
        User::create([
            'name' => 'Pending Vendor',
            'email' => 'pending-vendor@vuka.com',
            'password' => Hash::make('vendor@vuka'),
            'user_type' => 'vendor',
            'email_verified_at' => now(),
            'is_active' => false,
            'approved_at' => null,
            'is_weekend_override' => false,
            'shop_name' => 'Pending Vendor Shop',
            'shop_address' => '789 Pending Road, Mombasa',
            'shop_latitude' => -4.0434770,
            'shop_longitude' => 39.6682060,
            'vendor_notes' => 'Seeded for testing — pending activation.',
        ]);

        // ============================================
        // ADDITIONAL RANDOM USERS (Customers only)
        // ============================================
        User::factory(10)->create();
    }
}
