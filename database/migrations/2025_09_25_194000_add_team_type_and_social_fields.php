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
            $table->enum('team_type', ['management', 'brokers'])->default('brokers')->after('name');
            
            $table->string('instagram')->nullable()->after('linkedin');
            
            $table->json('languages')->nullable()->change();
            
            $table->index('team_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropIndex(['team_type']);
            $table->dropColumn([
                'team_type',
                'instagram'
            ]);
            
            $table->text('languages')->nullable()->change();
        });
    }
};
