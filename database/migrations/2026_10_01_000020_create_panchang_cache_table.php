<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('panchang_cache')) {
            return;
        }

        Schema::create('panchang_cache', function (Blueprint $table) {
            $table->id();
            $table->string('cache_key', 120)->unique();
            $table->date('date');
            $table->decimal('latitude', 10, 6);
            $table->decimal('longitude', 10, 6);
            $table->decimal('normalized_latitude', 8, 4);
            $table->decimal('normalized_longitude', 8, 4);
            $table->decimal('timezone', 5, 2);
            $table->string('location_name')->nullable();
            $table->string('feature', 40)->default('panchang_full');
            $table->string('api_endpoint', 120)->nullable();
            $table->json('api_response')->nullable();
            $table->json('panchang_data')->nullable();
            $table->string('api_status', 20)->default('success');
            $table->string('api_version', 20)->nullable();
            $table->timestamp('fetched_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('date');
            $table->index(['normalized_latitude', 'normalized_longitude', 'timezone']);
            $table->index('expires_at');
            $table->index('feature');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('panchang_cache');
    }
};
