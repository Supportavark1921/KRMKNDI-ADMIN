<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('products', 'vendor_id')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('vendor_id')
                ->nullable()
                ->after('mataji_id')
                ->constrained('vendors')
                ->nullOnDelete();

            $table->index('vendor_id');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Vendor::class);
            $table->dropColumn('vendor_id');
        });
    }
};
