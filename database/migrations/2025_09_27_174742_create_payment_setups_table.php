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
        Schema::create('payment_setups', function (Blueprint $table) {
            $table->id();
            $table->string('payment_type'); // School Fees, ID card, Uniform, etc.
            $table->enum('level', ['All', 'Nursery', 'Primary', 'Secondary'])->default('All');
            $table->enum('term', ['All', 'Term 1', 'Term 2', 'Term 3'])->default('All');
            $table->decimal('amount', 10, 2);
            $table->date('effective_date');
            $table->date('last_updated');
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_setups');
    }
};
