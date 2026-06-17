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
        Schema::table('admission_applications', function (Blueprint $table) {
            // Place of birth details
            $table->string('place_of_birth_town')->nullable()->after('date_of_birth');
            $table->string('place_of_birth_lga')->nullable()->after('place_of_birth_town');
            $table->string('place_of_birth_state')->nullable()->after('place_of_birth_lga');
            
            // Health information
            $table->string('health_status')->nullable()->after('medical_conditions');
            $table->text('disability_details')->nullable()->after('health_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admission_applications', function (Blueprint $table) {
            $table->dropColumn([
                'place_of_birth_town',
                'place_of_birth_lga',
                'place_of_birth_state',
                'health_status',
                'disability_details',
            ]);
        });
    }
};
