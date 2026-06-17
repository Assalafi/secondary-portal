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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('admission_no')->unique();
            $table->date('admission_date');
            $table->string('surname');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->enum('gender', ['Male', 'Female']);
            $table->date('dob'); // Date of Birth
            $table->string('place_of_birth_town')->nullable();
            $table->string('place_of_birth_lga')->nullable();
            $table->string('place_of_birth_state')->nullable();
            $table->string('nationality')->default('Nigerian');
            $table->string('state_of_origin')->nullable();
            $table->string('lga')->nullable();
            $table->enum('health_status', ['Normal', 'Disable'])->default('Normal');
            $table->text('disability_details')->nullable();
            $table->text('previous_school_details')->nullable();
            $table->foreignId('current_class_arm_id')->nullable()->constrained('class_arms')->onDelete('set null');
            $table->foreignId('academic_session_id')->nullable()->constrained('academic_sessions')->onDelete('set null');
            $table->enum('status', ['Active', 'Inactive', 'Graduated'])->default('Active');
            $table->string('photo_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
