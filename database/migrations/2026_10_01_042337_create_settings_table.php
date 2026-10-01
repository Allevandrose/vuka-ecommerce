<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Simple key-value settings store for site-wide configuration.
     * Examples of keys we'll seed:
     *   - default_currency          (string, e.g. 'KES')
     *   - supported_currencies      (json, e.g. ["KES","USD","EUR"])
     *   - vendor_product_cap        (int,    e.g. 100)
     *   - restricted_keywords       (json,   e.g. ["firearms","ammunition",...])
     *   - gradient_presets          (json,   e.g. [{"name":"Silver Metallic","css":"linear-gradient(...)"},...])
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            $table->string('key')->unique();
            $table->text('value')->nullable();

            // Type hint for casting when read via the Setting model
            // 'string' | 'int' | 'bool' | 'json'
            $table->string('type')->default('string');

            $table->timestamps();

            $table->index('key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
