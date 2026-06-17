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
        Schema::table('score_batches', function (Blueprint $table) {
            $table->decimal('first_ca_max', 5, 2)->default(10)->after('term_id');
            $table->decimal('second_ca_max', 5, 2)->default(10)->after('first_ca_max');
            $table->decimal('third_ca_max', 5, 2)->default(10)->after('second_ca_max');
            $table->decimal('exam_max', 5, 2)->default(70)->after('third_ca_max');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('score_batches', function (Blueprint $table) {
            $table->dropColumn(['first_ca_max', 'second_ca_max', 'third_ca_max', 'exam_max']);
        });
    }
};
