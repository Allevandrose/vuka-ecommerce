<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Brands are a flat list — no nesting.
     * Products reference a brand via brand_id (nullable).
     */
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();

            // Identity
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Visuals
            $table->string('logo')->nullable();     // image path

            // External
            $table->string('website')->nullable();  // brand's official website

            // Trust — admin-verified brands get a badge
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('is_active');
            $table->index('is_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
