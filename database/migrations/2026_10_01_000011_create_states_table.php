<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('code', 10)->nullable();
            $table->string('type', 20)->default('STATE'); // STATE | UNION_TERRITORY
            $table->string('status', 10)->default('active');
            $table->timestamps();

            $table->unique(['country_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};
