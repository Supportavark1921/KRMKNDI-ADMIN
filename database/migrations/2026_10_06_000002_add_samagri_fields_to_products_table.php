<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('unit')->nullable()->after('short_description');
            $table->string('badge')->nullable()->after('unit');
            $table->decimal('rating', 3, 2)->default(0)->after('badge');
            $table->unsignedInteger('reviews_count')->default(0)->after('rating');
            $table->json('uses')->nullable()->after('reviews_count');
            $table->json('contents')->nullable()->after('uses');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['unit', 'badge', 'rating', 'reviews_count', 'uses', 'contents']);
        });
    }
};
