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
        Schema::table('teams', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('linkedin');
            $table->string('whatsapp')->nullable()->after('phone');
            $table->string('experience')->nullable()->after('sort');
            $table->string('languages')->nullable()->after('experience');
            $table->text('areas_of_expertise')->nullable()->after('languages');
            $table->text('developers_of_expertise')->nullable()->after('areas_of_expertise');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'whatsapp',
                'experience',
                'languages',
                'areas_of_expertise',
                'developers_of_expertise'
            ]);
        });
    }
};
