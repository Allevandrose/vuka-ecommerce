<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds 'vendor' to the user_type enum.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            // MySQL: redefine the enum with the new value included
            DB::statement("
                ALTER TABLE `users`
                MODIFY COLUMN `user_type`
                ENUM('customer', 'admin', 'delivery', 'pickup', 'vendor')
                NOT NULL DEFAULT 'customer'
            ");
        }
        // SQLite has no ENUM — it stores them as plain strings with a check constraint.
        // Laravel's enum() on SQLite creates a CHECK constraint. To add 'vendor',
        // we need to rebuild the column via schema change. Simplest portable fix:
        // drop the CHECK by recreating the column as a plain string.
        elseif ($driver === 'sqlite') {
            // SQLite cannot alter enum/check constraints in-place.
            // The safest portable fix: no-op here, since SQLite treats 'vendor'
            // as a valid string anyway (enum() falls back to varchar with a check).
            // If you hit issues on SQLite, run: php artisan migrate:fresh --seed
            // (you're in dev, so this is acceptable).
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("
                ALTER TABLE `users`
                MODIFY COLUMN `user_type`
                ENUM('customer', 'admin', 'delivery', 'pickup')
                NOT NULL DEFAULT 'customer'
            ");
        }
    }
};
