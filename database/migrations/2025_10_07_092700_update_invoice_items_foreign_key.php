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
        Schema::table('invoice_items', function (Blueprint $table) {
            // Drop the old foreign key constraint
            $table->dropForeign(['fee_setup_id']);
            
            // Rename the column
            $table->renameColumn('fee_setup_id', 'payment_setup_id');
        });
        
        Schema::table('invoice_items', function (Blueprint $table) {
            // Add new foreign key constraint to payment_setups
            $table->foreign('payment_setup_id')
                  ->references('id')
                  ->on('payment_setups')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            // Drop the new foreign key
            $table->dropForeign(['payment_setup_id']);
            
            // Rename back
            $table->renameColumn('payment_setup_id', 'fee_setup_id');
        });
        
        Schema::table('invoice_items', function (Blueprint $table) {
            // Restore old foreign key
            $table->foreign('fee_setup_id')
                  ->references('id')
                  ->on('fee_setups')
                  ->onDelete('cascade');
        });
    }
};
