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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained('students')->onDelete('set null');
            $table->string('transaction_type'); // Income, Expense
            $table->string('payment_type'); // School Fees, ID card, Uniform, Salary, etc.
            $table->string('level')->nullable(); // Nursery, Primary, Secondary
            $table->string('term')->nullable(); // Term 1, Term 2, Term 3
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['Pending', 'Paid', 'Cancelled'])->default('Pending');
            $table->date('payment_date');
            $table->date('due_date')->nullable();
            $table->string('payment_method')->nullable(); // Cash, Bank Transfer, Card, etc.
            $table->string('reference_number')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
