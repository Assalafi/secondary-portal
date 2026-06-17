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
            // Make term_id nullable for admission applications (no term applicable)
            $table->foreignId('term_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Update null term_ids to a valid value before making non-nullable
            DB::statement('UPDATE invoices SET term_id = (SELECT id FROM terms LIMIT 1) WHERE term_id IS NULL');
            // Revert to non-nullable
            $table->foreignId('term_id')->nullable(false)->change();
        });
    }
};
