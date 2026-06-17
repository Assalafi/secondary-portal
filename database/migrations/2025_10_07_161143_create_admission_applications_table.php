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
        Schema::create('admission_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->unique();
            $table->foreignId('parent_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained('payments')->onDelete('set null');
            
            // Student Information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('other_name')->nullable();
            $table->date('date_of_birth');
            $table->enum('gender', ['Male', 'Female']);
            $table->string('nationality')->default('Nigerian');
            $table->string('state_of_origin')->nullable();
            $table->string('lga')->nullable();
            $table->text('home_address')->nullable();
            $table->string('religion')->nullable();
            $table->string('blood_group')->nullable();
            $table->text('medical_conditions')->nullable();
            
            // Academic Information
            $table->foreignId('proposed_class_id')->constrained('school_classes')->onDelete('cascade');
            $table->foreignId('proposed_class_arm_id')->nullable()->constrained('class_arms')->onDelete('set null');
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->onDelete('cascade');
            $table->string('previous_school')->nullable();
            $table->text('reason_for_admission')->nullable();
            
            // Guardian Information
            $table->string('guardian_name');
            $table->string('guardian_phone');
            $table->string('guardian_email');
            $table->string('guardian_occupation')->nullable();
            $table->text('guardian_address')->nullable();
            $table->enum('guardian_relationship', ['Father', 'Mother', 'Guardian', 'Uncle', 'Aunt', 'Grandparent', 'Other'])->default('Guardian');
            
            // Emergency Contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            
            // Documents
            $table->string('birth_certificate_path')->nullable();
            $table->string('passport_photo_path')->nullable();
            $table->string('previous_report_path')->nullable();
            
            // Application Status
            $table->enum('status', ['Draft', 'Pending Payment', 'Submitted', 'Under Review', 'Approved', 'Rejected'])->default('Draft');
            $table->text('admin_remarks')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_applications');
    }
};
