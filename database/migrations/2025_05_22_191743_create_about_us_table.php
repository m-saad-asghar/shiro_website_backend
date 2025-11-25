<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('about_us', function (Blueprint $table) {
            $table->id();
            $table->string('manager_name');
            $table->string('manager_position')->nullable();
            $table->string('manager_image');
            $table->text('manager_description');
            $table->string('video_url')->nullable();
            $table->string('title')->nullable();
            $table->longText('sub_description')->nullable();
            $table->longText('description');
            $table->longText('content')->nullable();
            $table->longText('vision')->nullable();
            $table->longText('mission')->nullable();
            $table->longText('apart')->nullable();
            $table->json('Our_value')->nullable();
            $table->longText('approach')->nullable();
            $table->json('target')->nullable();
            $table->longText('philosophy')->nullable();
            $table->longText('text_partner')->nullable();
            $table->longText('text_services')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_us');
    }
};
