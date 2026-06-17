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
        Schema::table('school_settings', function (Blueprint $table) {
            $table->string('favicon')->nullable()->after('school_logo');
            $table->text('meta_description')->nullable()->after('email');
            $table->text('meta_keywords')->nullable()->after('meta_description');
            $table->text('meta_author')->nullable()->after('meta_keywords');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn(['favicon', 'meta_description', 'meta_keywords', 'meta_author']);
        });
    }
};
