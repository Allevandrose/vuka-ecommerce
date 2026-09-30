<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Public vendor applications land here BEFORE they become a User.
     * Admin reviews, then approves -> user is created -> invite sent.
     */
    public function up(): void
    {
        Schema::create('vendor_applications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('shop_name');
            $table->string('shop_address');
            $table->decimal('shop_latitude', 10, 7)->nullable();
            $table->decimal('shop_longitude', 10, 7)->nullable();
            $table->text('product_categories')->nullable(); // free-text list of what they sell
            $table->text('message')->nullable();            // optional note to admin

            // Workflow
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_notes')->nullable();

            $table->timestamps();

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_applications');
    }
};
