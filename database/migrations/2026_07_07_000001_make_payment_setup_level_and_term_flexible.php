<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE payment_setups MODIFY level VARCHAR(50) NOT NULL DEFAULT 'All'");
            DB::statement("ALTER TABLE payment_setups MODIFY term VARCHAR(50) NOT NULL DEFAULT 'All'");
        }

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF');

            Schema::create('payment_setups_flexible', function (Blueprint $table) {
                $table->id();
                $table->string('payment_type');
                $table->string('level', 50)->default('All');
                $table->string('term', 50)->default('All');
                $table->decimal('amount', 10, 2);
                $table->date('effective_date');
                $table->date('last_updated');
                $table->string('status')->default('Active');
                $table->text('description')->nullable();
                $table->timestamps();
            });

            DB::statement('
                INSERT INTO payment_setups_flexible (
                    id, payment_type, level, term, amount, effective_date,
                    last_updated, status, description, created_at, updated_at
                )
                SELECT
                    id, payment_type, level, term, amount, effective_date,
                    last_updated, status, description, created_at, updated_at
                FROM payment_setups
            ');

            Schema::drop('payment_setups');
            Schema::rename('payment_setups_flexible', 'payment_setups');

            DB::statement('PRAGMA foreign_keys=ON');
        }

        DB::table('payment_setups')->where('term', 'Term 1')->update(['term' => '1st Term']);
        DB::table('payment_setups')->where('term', 'Term 2')->update(['term' => '2nd Term']);
        DB::table('payment_setups')->where('term', 'Term 3')->update(['term' => '3rd Term']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('payment_setups')->where('term', '1st Term')->update(['term' => 'Term 1']);
        DB::table('payment_setups')->where('term', '2nd Term')->update(['term' => 'Term 2']);
        DB::table('payment_setups')->where('term', '3rd Term')->update(['term' => 'Term 3']);
    }
};
