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
        Schema::table('property_amenities', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
        });

        Schema::table('property_floorplans', function (Blueprint $table) {
            $table->string('type')->nullable()->change();
            $table->string('plan_image_url')->nullable()->change();
        });

        Schema::table('property_nearby_places', function (Blueprint $table) {
            $table->string('place_name')->nullable()->change();
        });

        Schema::table('property_unique_points', function (Blueprint $table) {
            $table->text('point_text')->nullable()->change();
        });

        Schema::table('property_payment_schedules', function (Blueprint $table) {
            $table->string('phase_name')->nullable()->change();
            $table->decimal('percentage', 5, 2)->nullable()->change();
        });

        Schema::table('property_faqs', function (Blueprint $table) {
            $table->text('question')->nullable()->change();
            $table->longText('answer')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_amenities', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
        });

        Schema::table('property_floorplans', function (Blueprint $table) {
            $table->string('type')->nullable(false)->change();
            $table->string('plan_image_url')->nullable(false)->change();
        });

        Schema::table('property_nearby_places', function (Blueprint $table) {
            $table->string('place_name')->nullable(false)->change();
        });

        Schema::table('property_unique_points', function (Blueprint $table) {
            $table->text('point_text')->nullable(false)->change();
        });

        Schema::table('property_payment_schedules', function (Blueprint $table) {
            $table->string('phase_name')->nullable(false)->change();
            $table->decimal('percentage', 5, 2)->nullable(false)->change();
        });

        Schema::table('property_faqs', function (Blueprint $table) {
            $table->text('question')->nullable(false)->change();
            $table->longText('answer')->nullable(false)->change();
        });
    }
};
