<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Wishlist — one row per (user, product) pair.
     * No quantity column; a wishlist is a set, not a multiset.
     * A user either wants the product or they don't.
     *
     * Rules:
     *   - Adding the same product twice is a no-op (upsert via
     *     the unique composite index — the controller uses
     *     firstOrCreate).
     *   - Product deletion cascade-deletes wishlist rows (same
     *     pattern as cart_items and product_reviews).
     *   - User deletion cascade-deletes wishlist rows.
     */
    public function up(): void
    {
        Schema::create('wishlist_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->timestamps();

            // One row per product per user — add-to-wishlist is idempotent.
            $table->unique(['user_id', 'product_id']);

            // Fast "my wishlist" lookup and join helper.
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlist_items');
    }
};
