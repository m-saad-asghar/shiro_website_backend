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
        Schema::table('property_types', function (Blueprint $table) {
            // Change the 'name' column to JSON to store translations
            $table->json('name')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_types', function (Blueprint $table) {
            // Revert back to string column
            $table->string('name')->change();
        });
    }
};
