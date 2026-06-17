<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alter the ENUM to add 'Remita' option
        DB::statement("ALTER TABLE payments MODIFY payment_method ENUM('Cash', 'Bank Transfer', 'Card', 'Cheque', 'Online', 'Remita') DEFAULT 'Cash'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original ENUM values
        DB::statement("ALTER TABLE payments MODIFY payment_method ENUM('Cash', 'Bank Transfer', 'Card', 'Cheque', 'Online') DEFAULT 'Cash'");
    }
};
