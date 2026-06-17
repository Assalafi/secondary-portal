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
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('score_batch_id')->constrained('score_batches')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->decimal('first_ca', 5, 2)->default(0);
            $table->decimal('second_ca', 5, 2)->default(0);
            $table->decimal('third_ca', 5, 2)->default(0);
            $table->decimal('exam', 5, 2)->default(0);
            $table->decimal('total', 5, 2)->default(0);
            $table->string('grade')->nullable();
            $table->text('remark')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};
