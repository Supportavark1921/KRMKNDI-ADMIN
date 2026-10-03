<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_fee_configs', function (Blueprint $table) {
            $table->id();
            $table->decimal('platform_fee', 8, 2)->default(1.00);
            $table->decimal('gst_rate', 5, 2)->default(18.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_fee_configs');
    }
};
