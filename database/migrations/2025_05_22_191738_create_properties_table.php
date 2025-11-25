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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('type_id')->nullable()->constrained('types')->nullOnDelete();
            $table->longText('purpose')->nullable();
            $table->boolean('is_finish')->default(false);
            $table->longText('completion')->nullable();
            $table->longText('description')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->string('rental_period')->nullable();
            $table->text('location')->nullable();
            $table->json('images')->nullable();
            $table->float('area')->nullable();
            $table->foreignId('region_id')->nullable()->constrained('regions')->nullOnDelete();
            $table->integer('num_bathroom')->default(0);
            $table->integer('num_bedroom')->default(0);
            $table->foreignId('agent_id')->nullable()->constrained('agents')->nullOnDelete();
            $table->foreignId('developer_id')->nullable()->constrained('developers')->nullOnDelete();
            $table->longText('profile')->nullable();
            $table->json('contact')->nullable();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->boolean('is_sale')->default(false);
            $table->date('date_sale')->nullable();
            $table->boolean('is_home')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
