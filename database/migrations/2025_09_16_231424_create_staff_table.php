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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('staff_id')->unique();
            $table->string('designation'); // e.g., "Mathematics Teacher", "Principal", "Vice Principal"
            $table->string('department'); // e.g., "Academic", "Administration", "Science", "Arts"
            $table->date('date_of_employment');
            $table->date('date_of_retirement')->nullable();
            $table->decimal('salary', 10, 2)->nullable();
            $table->text('qualifications')->nullable();
            $table->enum('employment_type', ['Full-time', 'Part-time', 'Contract', 'Temporary'])->default('Full-time');
            $table->enum('status', ['Active', 'Inactive', 'Suspended', 'Terminated'])->default('Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
