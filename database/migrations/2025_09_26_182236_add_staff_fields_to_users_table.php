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
            // Add fields needed for staff management
            $table->enum('gender', ['Male', 'Female'])->nullable()->after('email');
            $table->date('date_of_birth')->nullable()->after('gender');
            $table->string('nationality')->nullable()->after('date_of_birth');
            $table->string('state_of_origin')->nullable()->after('nationality');
            $table->string('lga')->nullable()->after('state_of_origin');
            $table->string('photo_path')->nullable()->after('lga');
            $table->timestamp('last_login_at')->nullable()->after('photo_path');
            
            // Update status enum to match our controller expectations
            $table->enum('status', ['Active', 'Inactive', 'Suspended', 'Terminated'])->default('Active')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove added columns
            $table->dropColumn([
                'gender', 
                'date_of_birth', 
                'nationality', 
                'state_of_origin', 
                'lga', 
                'photo_path', 
                'last_login_at'
            ]);
            
            // Revert status enum
            $table->enum('status', ['active', 'inactive'])->default('active')->change();
        });
    }
};
