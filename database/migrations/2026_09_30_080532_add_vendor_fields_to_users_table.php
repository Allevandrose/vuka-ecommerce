<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds vendor-specific profile fields to the users table.
     * All columns are nullable so existing customers/staff/admin rows are unaffected.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('shop_name')->nullable()->after('is_weekend_override');
            $table->string('shop_address')->nullable()->after('shop_name');
            $table->decimal('shop_latitude', 10, 7)->nullable()->after('shop_address');
            $table->decimal('shop_longitude', 10, 7)->nullable()->after('shop_latitude');
            $table->text('vendor_notes')->nullable()->after('shop_longitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'shop_name',
                'shop_address',
                'shop_latitude',
                'shop_longitude',
                'vendor_notes',
            ]);
        });
    }
};
