<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Attributes define what properties products in a category can have.
     * - category_id = null  → global attribute (usable by any category)
     * - category_id = X     → scoped to that category (and its descendants)
     *
     * Products store their actual attribute values in a JSON column on the products table.
     * This table is metadata: it drives the admin UI (input types, options) and the
     * public filter UI (is_filterable, label, options).
     */
    public function up(): void
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();

            // Scoping
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->cascadeOnDelete();

            // Identity
            $table->string('key');          // machine name, e.g. 'chipset', 'ram', 'color'
            $table->string('label');        // human label, e.g. 'Chipset', 'RAM', 'Color'

            // Data type
            // 'text'        → single-line text
            // 'textarea'    → multi-line text
            // 'number'      → numeric, optionally with unit
            // 'select'      → single choice from options
            // 'multiselect' → multiple choices from options
            // 'boolean'     → yes/no
            $table->enum('type', ['text', 'textarea', 'number', 'select', 'multiselect', 'boolean'])
                ->default('text');

            // For select/multiselect — JSON array of allowed values
            // e.g. ["Black","Blue","Silver"]
            $table->json('options')->nullable();

            // Optional unit for display, e.g. 'GB', 'mm', 'kg'
            $table->string('unit')->nullable();

            // UI behaviour
            $table->boolean('is_filterable')->default(false);   // show in public filter sidebar
            $table->boolean('is_required')->default(false);     // must be filled when creating a product
            $table->boolean('is_active')->default(true);

            // Ordering within its category's attribute list
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            // Indexes
            $table->index('category_id');
            $table->index('is_filterable');
            $table->index('is_active');
            $table->index(['category_id', 'is_active', 'sort_order']);

            // Same key cannot be defined twice within the same category
            // (global attributes use category_id = null, so they have their own namespace)
            $table->unique(['category_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attributes');
    }
};
