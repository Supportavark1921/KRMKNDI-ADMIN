<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('state_id')->constrained('states')->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('code', 20)->nullable();
            $table->string('status', 10)->default('active');
            $table->timestamps();

            $table->unique(['state_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};
