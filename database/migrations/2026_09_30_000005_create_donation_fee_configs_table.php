<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation_fee_configs', function (Blueprint $table) {
            $table->id();
            $table->decimal('handling_charge', 10, 2)->default(1.00);
            $table->decimal('gst_rate', 5, 2)->default(18.00);   // percent, e.g. 18.00 = 18%
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_fee_configs');
    }
};
