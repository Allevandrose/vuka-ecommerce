<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Complaints let customers flag products that are counterfeit, misleading,
     * damaged, restricted, etc. High complaint counts trigger admin review,
     * which may lead to disabling the product.
     *
     * user_id is nullable + nullOnDelete so that complaints are preserved even
     * if the reporting user deletes their account. This keeps a clean audit
     * trail for spotting abuse patterns or repeat offenders.
     *
     * Schema is created now; the write form + admin queue come later.
     */
    public function up(): void
    {
        Schema::create('product_complaints', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            // Nullable + nullOnDelete — complaint survives user deletion
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Categorised reason
            $table->enum('reason', [
                'counterfeit',      // fake / replica
                'misleading',       // description or images don't match
                'damaged',          // arrived broken
                'not_received',     // never delivered
                'restricted',       // falls under restricted goods
                'offensive',        // inappropriate content
                'other',
            ]);

            $table->text('details')->nullable();

            // Evidence photos — [{path, alt}]
            $table->json('images')->nullable();

            // Resolution workflow
            $table->enum('status', ['open', 'reviewing', 'resolved', 'dismissed'])
                ->default('open');

            $table->foreignId('resolved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('resolved_at')->nullable();
            $table->text('admin_notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('product_id');
            $table->index('user_id');
            $table->index('status');
            $table->index('reason');
            $table->index(['product_id', 'status']);  // count open complaints for a product
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_complaints');
    }
};
