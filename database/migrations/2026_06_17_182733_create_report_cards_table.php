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
        Schema::create('report_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained('school_classes')->onDelete('cascade');
            $table->foreignId('academic_session_id')->nullable()->constrained('session_terms')->onDelete('set null');
            $table->foreignId('term_id')->nullable()->constrained('session_terms')->onDelete('set null');
            $table->foreignId('result_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('report_type', ['termly', 'annual', 'mock', 'entrance', 'cumulative'])->default('termly');
            $table->enum('status', ['draft', 'computed', 'awaiting_review', 'approved', 'published', 'archived', 'reopened'])->default('draft');
            $table->decimal('total_score', 8, 2)->nullable();
            $table->integer('maximum_score')->nullable();
            $table->decimal('average_score', 5, 2)->nullable();
            $table->string('final_grade')->nullable();
            $table->string('final_remark')->nullable();
            $table->integer('class_position')->nullable();
            $table->integer('number_in_class')->nullable();
            $table->decimal('class_highest_average', 5, 2)->nullable();
            $table->decimal('class_lowest_average', 5, 2)->nullable();
            $table->decimal('class_average', 5, 2)->nullable();
            $table->integer('attendance_opened')->default(0);
            $table->integer('attendance_present')->default(0);
            $table->integer('attendance_absent')->default(0);
            $table->integer('attendance_late')->default(0);
            $table->decimal('attendance_percentage', 5, 2)->nullable();
            $table->enum('promotion_decision', ['Promoted', 'Promoted on Trial', 'Repeated', 'Withdrawn', 'Transferred', 'Graduated', 'Not Applicable'])->nullable();
            $table->foreignId('next_class_id')->nullable()->constrained('school_classes')->onDelete('set null');
            $table->date('vacation_date')->nullable();
            $table->date('next_term_begins')->nullable();
            $table->decimal('next_term_fee', 12, 2)->nullable();
            $table->decimal('outstanding_balance', 12, 2)->nullable();
            $table->text('class_teacher_comment')->nullable();
            $table->text('principal_comment')->nullable();
            $table->text('parent_comment')->nullable();
            $table->foreignId('class_teacher_id')->nullable()->constrained('staff')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('published_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('published_at')->nullable();
            $table->string('pdf_url')->nullable();
            $table->string('verification_code')->nullable()->unique();
            $table->string('verification_url')->nullable();
            $table->string('qr_code_url')->nullable();
            $table->timestamps();
            
            $table->index(['student_id', 'class_id', 'academic_session_id', 'term_id'], 'report_card_index');
            $table->index('status');
            $table->index('verification_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_cards');
    }
};
