<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('guru_id')->nullable()->after('user_id')->constrained('gurus')->nullOnDelete();
            $table->foreignId('service_id')->nullable()->after('guru_id')->constrained('services')->nullOnDelete();
            $table->decimal('total_amount', 10, 2)->nullable()->after('notes');
            $table->string('payment_id', 100)->nullable()->after('total_amount');
            $table->json('samagri')->nullable()->after('payment_id');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['guru_id']);
            $table->dropForeign(['service_id']);
            $table->dropColumn(['guru_id', 'service_id', 'total_amount', 'payment_id', 'samagri']);
        });
    }
};
