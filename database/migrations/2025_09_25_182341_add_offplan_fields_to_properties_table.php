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
        Schema::table('properties', function (Blueprint $table) {
            
            $table->string('slug')->unique()->nullable()->after('title');
            $table->decimal('starting_price', 12, 2)->nullable()->after('price');
            $table->year('handover_year')->nullable()->after('completion');
            $table->string('payment_plan')->nullable()->after('handover_year');
            $table->text('property_mix')->nullable()->after('payment_plan'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'slug',
                'starting_price',
                'handover_year',
                'payment_plan',
                'property_mix'
            ]);
        });
    }
};
