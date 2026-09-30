<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guru_donation_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('donation_categories');
            $table->string('status', 20)->default('active');
            $table->timestamps();
            $table->unique(['guru_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru_donation_categories');
    }
};
