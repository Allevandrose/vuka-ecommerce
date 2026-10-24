<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Minimal cart — one row per (user, product) pair.
     * No orders, no price snapshot, no checkout. Just "what is in this
     * user's cart right now", keyed to a live product row.
     *
     * Rules:
     *   - Adding the same product twice increments `quantity` (enforced by
     *     the unique composite index — the app layer uses updateOrCreate).
     *   - Product deletion cascade-deletes cart rows (same pattern as
     *     product_reviews).
     *   - User deletion cascade-deletes cart rows.
     */
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            // Always >= 1. The controller enforces the upper bound against
            // stock/track_inventory at the app layer.
            $table->unsignedInteger('quantity')->default(1);

            $table->timestamps();

            // One row per product per user — add-to-cart is an upsert.
            $table->unique(['user_id', 'product_id']);

            // Fast "my cart" lookup (the composite index already covers
            // this, but this keeps intent explicit and helps joins).
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
