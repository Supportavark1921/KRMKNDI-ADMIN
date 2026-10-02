<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mataji_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guruji_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('mataji_id')->constrained('matajis')->cascadeOnDelete();

            // Customer — either a registered user OR a walk-in (name+phone)
            $table->foreignId('customer_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone', 20)->nullable();

            $table->string('type')->default('sale');   // sale | purchase
            $table->string('status')->default('draft'); // draft|confirmed|paid|delivered|cancelled

            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            $table->text('notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['guruji_id', 'status']);
            $table->index(['mataji_id']);
        });

        Schema::create('mataji_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mataji_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 10, 2);  // snapshot at time of order
            $table->decimal('line_total', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mataji_order_items');
        Schema::dropIfExists('mataji_orders');
    }
};
