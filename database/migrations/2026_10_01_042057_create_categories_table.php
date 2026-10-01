<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Categories support unlimited nesting via parent_id (adjacency list).
     * Top-level categories have parent_id = null.
     * Themes are stored as CSS gradients + solid color fallbacks.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            // Tree structure
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            // Identity
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Visual identity (icons, images)
            $table->string('icon')->nullable();          // FontAwesome class, e.g. 'fa-mobile-alt'
            $table->string('hero_image')->nullable();    // banner path

            // Theming — solid color fallbacks
            $table->string('primary_color', 32)->nullable();   // e.g. '#1E3C2C'
            $table->string('accent_color', 32)->nullable();    // e.g. '#E7A93B'

            // Theming — CSS gradients (full gradient strings)
            $table->text('gradient_css')->nullable();          // hero background
            $table->text('accent_gradient_css')->nullable();   // buttons/borders/accents

            // Theme key — maps to a Blade layout variant
            // e.g. 'default', 'electronics', 'cosmetics', 'adult', 'agriculture'
            $table->string('theme')->default('default');

            // Visibility & ordering
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('parent_id');
            $table->index('is_active');
            $table->index('sort_order');
            $table->index(['parent_id', 'is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
