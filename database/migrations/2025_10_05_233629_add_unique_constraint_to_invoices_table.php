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
        Schema::table('invoices', function (Blueprint $table) {
            // Add composite index for faster queries when checking for duplicate invoices
            // Note: Not using unique constraint to allow flexibility (e.g., cancelled invoices)
            $table->index(['student_id', 'academic_session_id', 'term_id', 'status'], 'invoices_student_session_term_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Drop the composite index
            $table->dropIndex('invoices_student_session_term_status_index');
        });
    }
};
