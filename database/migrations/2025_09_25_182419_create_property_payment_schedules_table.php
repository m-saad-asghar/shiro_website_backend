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
        Schema::create('property_payment_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->string('phase_name');
            $table->decimal('percentage', 5, 2);
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
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
        Schema::dropIfExists('property_payment_schedules');
    }
};
