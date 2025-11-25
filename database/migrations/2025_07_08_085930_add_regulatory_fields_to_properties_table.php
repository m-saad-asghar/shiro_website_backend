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

            $table->string('broker_license')->nullable();
            $table->string('dld_permit_number')->nullable();
            $table->string('agent_license')->nullable();
            $table->string('qr_code')->nullable();
            $table->string('reference_id')->unique()->nullable();
            $table->string('dubailand_link')->nullable();
            $table->string('zone_name')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'listing_date',
                'broker_license',
                'dld_permit_number',
                'agent_license',
                'qr_code',
                'reference_id',
                'dubailand_link',
                'zone_name',
            ]);
        });
    }
};
