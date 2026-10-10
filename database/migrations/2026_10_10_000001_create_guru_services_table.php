<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guru_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->json('pricing')->nullable();        // {amount, currency, discount_amount}
            $table->json('pooja_samagri')->nullable();  // [{name, price}, ...]
            $table->string('status', 20)->default('active');
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['guru_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru_services');
    }
};
