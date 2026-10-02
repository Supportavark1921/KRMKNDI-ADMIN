<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image')->nullable();           // primary image path
            $table->json('gallery')->nullable();           // additional images

            $table->string('type')->default('banner');     // banner|promotion|announcement|offer
            $table->string('placement')->default('home_top'); // home_top|home_middle|shop|pooja|popup
            $table->string('status')->default('draft');    // draft|active|inactive

            // Call-to-action
            $table->string('cta_type')->default('none');   // none|product|category|mataji|pooja|url
            $table->string('cta_value')->nullable();       // target id or URL

            // Scheduling
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            $table->unsignedInteger('sort_order')->default(0);
            $table->string('audience')->default('all');    // all|user|guruji|vendor

            // Multilingual (same pattern as Service)
            $table->json('translations')->nullable();      // {hi:{title,description}, ...}

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'placement', 'sort_order']);
            $table->index(['starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
