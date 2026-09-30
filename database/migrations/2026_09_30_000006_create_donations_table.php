<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->string('donation_id', 20)->unique();         // DON-XXXXX
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('guru_id')->constrained('gurus'); // never deleted
            $table->foreignId('category_id')->constrained('donation_categories');
            $table->decimal('donation_amount', 12, 2);
            $table->decimal('handling_charge', 10, 2);
            $table->decimal('gst_rate', 5, 2);                  // rate stored at time of transaction
            $table->decimal('gst_amount', 10, 2);
            $table->decimal('total_amount', 12, 2);
            $table->string('currency', 10)->default('INR');
            $table->string('payment_status', 20)->default('pending');
            // pending / success / failed / cancelled / refunded
            $table->string('payment_id')->nullable();
            $table->string('transaction_id')->nullable()->unique(); // idempotency key
            $table->string('payment_method', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
