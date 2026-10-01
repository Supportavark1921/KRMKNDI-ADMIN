<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('navamsha_api_logs')) {
            return;
        }

        Schema::create('navamsha_api_logs', function (Blueprint $table) {
            $table->id();
            $table->string('endpoint', 120);
            $table->string('cache_key', 120)->nullable();
            $table->string('feature', 40)->default('panchang_full');
            $table->date('request_date');
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->decimal('timezone', 5, 2)->nullable();
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->unsignedInteger('response_time_ms')->nullable();
            $table->boolean('success')->default(false);
            $table->text('error_message')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('request_date');
            $table->index('feature');
            $table->index('success');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('navamsha_api_logs');
    }
};
