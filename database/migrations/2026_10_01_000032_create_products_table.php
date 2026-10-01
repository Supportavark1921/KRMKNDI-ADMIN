<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('products')) {
            return;
        }

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code')->unique();
            $table->string('sku')->unique()->nullable();
            $table->foreignId('category_id')->constrained('product_categories')->restrictOnDelete();
            $table->foreignId('mataji_id')->nullable()->constrained('matajis')->nullOnDelete();
            $table->string('name');
            $table->string('short_description')->nullable();
            $table->text('description')->nullable();
            $table->string('brand_source')->nullable();
            $table->enum('product_type', ['NORMAL', 'MATAJI_OFFERING', 'MATAJI_OFFERED_RESALE'])->default('NORMAL');
            $table->decimal('price', 10, 2);
            $table->decimal('compare_at_price', 10, 2)->nullable();
            $table->enum('status', ['draft', 'active', 'inactive', 'sold_out'])->default('draft');
            $table->boolean('offering_eligible')->default(false);
            $table->boolean('resale_eligible')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
