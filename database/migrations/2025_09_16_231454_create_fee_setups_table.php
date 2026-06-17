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
        Schema::create('fee_setups', function (Blueprint $table) {
            $table->id();
            $table->string('payment_type'); // e.g., "School Fees", "Uniform", "ID Card", "Exam Fee"
            $table->string('level'); // e.g., "All", "Nursery", "Primary", "JSS", "SS"
            $table->decimal('amount', 10, 2);
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->onDelete('cascade');
            $table->foreignId('term_id')->nullable()->constrained('terms')->onDelete('set null');
            $table->text('description')->nullable();
            $table->boolean('is_compulsory')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_setups');
    }
};
