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
        Schema::table('payments', function (Blueprint $table) {
            // Drop the foreign key first
            $table->dropForeign(['invoice_id']);
            
            // Modify the column to be nullable
            $table->foreignId('invoice_id')->nullable()->change();
            
            // Re-add the foreign key constraint
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Drop the foreign key
            $table->dropForeign(['invoice_id']);
            
            // Make the column non-nullable again
            $table->foreignId('invoice_id')->nullable(false)->change();
            
            // Re-add the foreign key constraint
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
        });
    }
};
