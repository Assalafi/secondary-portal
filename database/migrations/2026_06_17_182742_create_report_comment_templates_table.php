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
        Schema::create('report_comment_templates', function (Blueprint $table) {
            $table->id();
            $table->string('category')->nullable();
            $table->decimal('min_average', 5, 2)->nullable();
            $table->decimal('max_average', 5, 2)->nullable();
            $table->text('template_text');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_comment_templates');
    }
};
