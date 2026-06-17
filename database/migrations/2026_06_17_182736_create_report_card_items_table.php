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
        Schema::create('report_card_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_card_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->string('subject_name');
            $table->decimal('ca_score', 5, 2)->nullable();
            $table->decimal('exam_score', 5, 2)->nullable();
            $table->decimal('total_score', 5, 2)->nullable();
            $table->string('grade')->nullable();
            $table->decimal('grade_point', 3, 2)->nullable();
            $table->string('remark')->nullable();
            $table->integer('subject_position')->nullable();
            $table->decimal('class_average', 5, 2)->nullable();
            $table->decimal('highest_score', 5, 2)->nullable();
            $table->decimal('lowest_score', 5, 2)->nullable();
            $table->foreignId('teacher_id')->nullable()->constrained('staff')->onDelete('set null');
            $table->string('teacher_initial')->nullable();
            $table->timestamps();
            
            $table->index(['report_card_id', 'subject_id']);
            $table->index('report_card_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_card_items');
    }
};
