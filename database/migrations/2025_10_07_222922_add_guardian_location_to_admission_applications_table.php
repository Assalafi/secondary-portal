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
            // Guardian location details to match admin enrollment form
            $table->string('guardian_city')->nullable()->after('guardian_address');
            $table->string('guardian_state')->nullable()->after('guardian_city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admission_applications', function (Blueprint $table) {
            $table->dropColumn(['guardian_city', 'guardian_state']);
        });
    }
};
