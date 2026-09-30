<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropUnique('services_name_unique');
            $table->dropColumn(['name', 'description', 'duration_minutes', 'price', 'is_active']);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->json('translations')->nullable()->after('id');
            $table->json('images')->nullable()->after('translations');
            $table->json('pricing')->nullable()->after('images');
            $table->string('status', 20)->default('active')->after('pricing');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['translations', 'images', 'pricing', 'status']);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('duration_minutes')->default(60);
            $table->unsignedInteger('price')->nullable();
            $table->boolean('is_active')->default(true);
        });
    }
};
