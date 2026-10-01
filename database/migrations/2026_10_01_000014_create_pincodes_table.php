<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pincodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->foreignId('state_id')->constrained('states')->cascadeOnDelete();
            $table->foreignId('district_id')->constrained('districts')->cascadeOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->string('pincode', 10);
            $table->string('post_office_name', 200);
            $table->string('office_type', 50)->nullable();   // POST_OFFICE | SUB_POST_OFFICE | BRANCH_POST_OFFICE
            $table->string('delivery_status', 20)->nullable(); // Delivery | Non-Delivery
            $table->string('division', 100)->nullable();
            $table->string('region', 100)->nullable();
            $table->string('circle', 100)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('status', 10)->default('active');
            $table->timestamps();

            $table->index(['pincode']);
            $table->index(['state_id', 'district_id']);
            $table->unique(['pincode', 'post_office_name', 'state_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pincodes');
    }
};
