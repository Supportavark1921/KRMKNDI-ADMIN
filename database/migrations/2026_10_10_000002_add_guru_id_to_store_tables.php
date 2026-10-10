<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('guru_id')->nullable()->after('vendor_id')
                ->constrained()->nullOnDelete();
        });

        Schema::table('product_categories', function (Blueprint $table) {
            $table->foreignId('guru_id')->nullable()->after('parent_id')
                ->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Guru::class);
            $table->dropColumn('guru_id');
        });

        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Guru::class);
            $table->dropColumn('guru_id');
        });
    }
};
