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
        Schema::create('property_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->string('name'); 
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('message')->nullable(); 
            $table->string('interest_type')->default('general');
            $table->string('status')->default('new');
            $table->timestamp('contacted_at')->nullable();
            $table->timestamps();
            
            $table->index(['property_id', 'status']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_leads');
    }
};
