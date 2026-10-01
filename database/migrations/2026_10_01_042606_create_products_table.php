<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Single products table — both admin and vendor products live here.
     * Differentiated by source_type + source_id:
     *   - source_type = 'admin'  → source_id = admin user id (or null for system-owned)
     *   - source_type = 'vendor' → source_id = vendor user id
     *
     * Workflow status drives visibility:
     *   draft → pending_review → active → (rejected | disabled | archived)
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Identity
            $table->uuid('uuid')->unique();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('sku')->unique();

            // Short + long descriptions
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();

            // Relationships
            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete();

            $table->foreignId('brand_id')
                ->nullable()
                ->constrained('brands')
                ->nullOnDelete();

            // Source
            $table->enum('source_type', ['admin', 'vendor'])->default('admin');
            $table->foreignId('source_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Pricing
            $table->decimal('cost_price', 12, 2)->nullable();
            $table->decimal('price', 12, 2);
            $table->decimal('compare_at_price', 12, 2)->nullable();
            $table->char('currency', 3)->default('KES');

            // Inventory
            $table->integer('stock_quantity')->default(0);
            $table->boolean('track_inventory')->default(true);

            // Shipping / logistics
            $table->decimal('weight', 8, 3)->nullable();
            $table->decimal('length', 8, 2)->nullable();
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            $table->boolean('is_local')->default(true);
            $table->boolean('is_international')->default(false);
            $table->integer('lead_time_days')->nullable();

            // Trust signals
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_certified')->default(false);
            $table->enum('condition', ['new', 'used', 'refurbished'])->default('new');

            // Flexible attributes
            $table->json('attributes')->nullable();

            // Media
            $table->json('images')->nullable();
            $table->string('primary_image')->nullable();
            $table->string('thumbnail')->nullable();

            // Workflow & visibility
            $table->enum('status', [
                'draft',
                'pending_review',
                'active',
                'rejected',
                'disabled',
                'archived',
            ])->default('draft');

            $table->text('rejection_reason')->nullable();
            $table->text('disabled_reason')->nullable();
            $table->foreignId('disabled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('disabled_at')->nullable();

            // Marketing
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();

            // Denormalized counters
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('sales_count')->default(0);
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->unsignedInteger('complaint_count')->default(0);

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Core indexes
            $table->index('status');
            $table->index('is_featured');
            $table->index('published_at');
            $table->index(['status', 'is_featured']);
            $table->index(['category_id', 'status']);
            $table->index(['brand_id', 'status']);
            $table->index(['source_type', 'source_id']);
            $table->index('price');
        });

        // Full-text index for basic search (MySQL only)
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `products` ADD FULLTEXT `products_search_fulltext` (`name`, `short_description`, `description`)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
