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
        Schema::table('grading_systems', function (Blueprint $table) {
            $table->decimal('gpa_point', 3, 2)->nullable()->after('max_score');
            $table->string('description')->nullable()->after('gpa_point');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grading_systems', function (Blueprint $table) {
            $table->dropColumn(['gpa_point', 'description']);
        });
    }
};
