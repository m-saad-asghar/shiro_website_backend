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
            if (!Schema::hasColumn('users', 'register_method')) {
                $table->string('register_method')
                    ->nullable()
                    ->after('register_id')
                    ->comment('register_method: email, google, facebook, apple, phone etc.');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'register_method')) {
                $table->dropColumn('register_method');
            }
        });
    }

};
