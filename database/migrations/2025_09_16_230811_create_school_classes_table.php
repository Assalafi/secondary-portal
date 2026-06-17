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
        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();
            $table->enum('level', ['Nursery', 'Primary', 'JSS', 'SS']);
            $table->string('name'); // e.g., "JSS 1", "Primary 4", "SS 2"
            $table->integer('numeric_level'); // 1, 2, 3, 4, 5, 6
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_classes');
    }
};
