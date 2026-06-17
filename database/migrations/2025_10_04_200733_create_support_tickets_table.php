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
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Parent/user who created ticket
            $table->string('title');
            $table->enum('category', ['Academic', 'Payment', 'Technical', 'Account', 'Other'])->default('Other');
            $table->enum('status', ['Open', 'Awaiting Response', 'Resolved', 'Closed'])->default('Open');
            $table->enum('priority', ['Low', 'Medium', 'High'])->default('Medium');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null'); // Admin/staff assigned
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
