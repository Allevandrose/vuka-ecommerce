<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(false)->after('google_id');
            $table->timestamp('approved_at')->nullable()->after('is_active');
            $table->timestamp('last_activity_at')->nullable()->after('approved_at');
            $table->boolean('is_weekend_override')->default(false)->after('last_activity_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'approved_at', 'last_activity_at', 'is_weekend_override']);
        });
    }
};
