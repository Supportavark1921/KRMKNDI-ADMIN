<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('platform_fee', 10, 2)->default(0)->after('subtotal');
            $table->decimal('gst_amount', 10, 2)->default(0)->after('platform_fee');
            $table->string('payment_method', 20)->default('cod')->after('payment_id');
            // payment_method: cod | online
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });
    }
};
