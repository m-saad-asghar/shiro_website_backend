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
        Schema::create('property_nearby_places', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->string('place_name');
            $table->integer('time_minutes')->nullable();
            $table->string('distance')->nullable(); 
            $table->string('transport_type')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['property_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_nearby_places');
    }
};
