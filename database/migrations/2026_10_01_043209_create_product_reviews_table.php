<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Only users who purchased a product (via an order) should be able to leave
     * a verified review. Reviews without an order link are allowed but marked
     * as unverified (is_verified_purchase = false).
     *
     * Schema is created now; the write UI + moderation is built after products.
     */
    public function up(): void
    {
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();    // reviews die with the product

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();    // reviews die with the user

            // Optional link to the order that qualifies this as a verified purchase.
            // Orders module not built yet — kept as a plain unsigned big integer
            // without FK constraint. A follow-up migration will add the FK once
            // orders exist.
            $table->unsignedBigInteger('order_id')->nullable();

            // Rating: 1–5 stars (validated at app level)
            $table->unsignedTinyInteger('rating');

            $table->string('title')->nullable();
            $table->text('body')->nullable();

            // Photos attached to the review — [{path, alt}]
            $table->json('images')->nullable();

            // Trust & moderation
            $table->boolean('is_verified_purchase')->default(false);
            $table->boolean('is_approved')->default(false);   // moderation gate
            $table->unsignedInteger('helpful_count')->default(0);

            $table->timestamps();

            // Indexes
            $table->index('product_id');
            $table->index('user_id');
            $table->index('rating');
            $table->index('is_approved');
            $table->index(['product_id', 'is_approved']);  // public list of approved reviews

            // One review per user per product
            $table->unique(['product_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};
