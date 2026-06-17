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
        Schema::table('staff', function (Blueprint $table) {
            // Rename columns to match our controller expectations
            if (Schema::hasColumn('staff', 'staff_id_no')) {
                $table->renameColumn('staff_id_no', 'staff_id');
            }
            if (Schema::hasColumn('staff', 'date_joined')) {
                $table->renameColumn('date_joined', 'date_of_employment');
            }
            if (Schema::hasColumn('staff', 'date_left')) {
                $table->renameColumn('date_left', 'date_of_retirement');
            }
            
            // Update employment_type enum to include 'Temporary'
            $table->enum('employment_type', ['Full-time', 'Part-time', 'Contract', 'Temporary'])->default('Full-time')->change();
            
            // Update status enum to include 'Suspended'
            $table->enum('status', ['Active', 'Inactive', 'Suspended', 'Terminated'])->default('Active')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            // Revert column names
            if (Schema::hasColumn('staff', 'staff_id')) {
                $table->renameColumn('staff_id', 'staff_id_no');
            }
            if (Schema::hasColumn('staff', 'date_of_employment')) {
                $table->renameColumn('date_of_employment', 'date_joined');
            }
            if (Schema::hasColumn('staff', 'date_of_retirement')) {
                $table->renameColumn('date_of_retirement', 'date_left');
            }
            
            // Revert enums
            $table->enum('employment_type', ['Full-time', 'Part-time', 'Contract'])->default('Full-time')->change();
            $table->enum('status', ['Active', 'Inactive', 'Terminated'])->default('Active')->change();
        });
    }
};
