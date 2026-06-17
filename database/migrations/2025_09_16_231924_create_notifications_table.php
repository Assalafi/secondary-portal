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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['General', 'Academic', 'Financial', 'Attendance', 'Assessment', 'Emergency'])->default('General');
            $table->enum('priority', ['Low', 'Medium', 'High', 'Urgent'])->default('Medium');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->json('recipient_ids')->nullable(); // For specific users
            $table->json('recipient_roles')->nullable(); // For role-based notifications
            $table->json('recipient_classes')->nullable(); // For class-based notifications
            $table->boolean('is_global')->default(false); // For all users
            $table->timestamp('scheduled_at')->nullable();
            $table->enum('status', ['Draft', 'Scheduled', 'Sent'])->default('Draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
