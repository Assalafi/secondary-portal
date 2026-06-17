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
        Schema::create('report_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('default_grading_profile_id')->nullable()->constrained('grading_profiles')->onDelete('set null');
            $table->integer('ca_max_score')->default(30);
            $table->integer('exam_max_score')->default(70);
            $table->boolean('show_subject_position')->default(true);
            $table->boolean('show_class_average')->default(true);
            $table->boolean('show_highest_lowest')->default(true);
            $table->boolean('show_affective_domain')->default(true);
            $table->boolean('show_psychomotor_domain')->default(true);
            $table->boolean('show_attendance')->default(true);
            $table->boolean('show_next_term_fee')->default(true);
            $table->boolean('show_outstanding_balance')->default(true);
            $table->boolean('show_parent_signature')->default(true);
            $table->boolean('show_qr_verification')->default(true);
            $table->boolean('require_principal_approval')->default(true);
            $table->boolean('allow_teacher_comment')->default(true);
            $table->boolean('allow_parent_download')->default(true);
            $table->string('pdf_template_name')->default('nigerian_standard');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_settings');
    }
};
