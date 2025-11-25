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
        Schema::table('users', function (Blueprint $table) {
            // Drop the old foreignId.
            $table->dropColumn('register_id');
        });

        Schema::table('users', function (Blueprint $table) {
            // Add register_id again as a string.
            $table->string('register_id')->nullable()->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('register_id');
            // You can re-add it as a foreignId if you’d like.
            // $table->foreignId('register_id')->nullable()->after('password');
        });
    }
};
