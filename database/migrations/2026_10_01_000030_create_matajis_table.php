<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('matajis')) {
            return;
        }

        Schema::create('matajis', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('temple_name')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pin_code', 10)->nullable();
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->json('contact_info')->nullable();
            $table->boolean('offering_available')->default(true);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matajis');
    }
};
