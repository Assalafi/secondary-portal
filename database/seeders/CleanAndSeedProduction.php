<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CleanAndSeedProduction extends Seeder
{
    /**
     * Clean all existing records except admin users, then seed fresh data.
     */
    public function run(): void
    {
        $this->command->info('🧹 Cleaning existing records...');

        // Get admin-related role IDs (Super Admin, Admin, Principal)
        $adminRoleIds = Role::whereIn('name', ['Super Admin', 'Admin', 'Principal'])->pluck('id')->toArray();
        $adminUserIds = User::whereIn('role_id', $adminRoleIds)->pluck('id')->toArray();

        // Disable foreign key checks for cleanup
        Schema::disableForeignKeyConstraints();

        // Clear tables in proper order (reverse dependency)
        $tablesToClear = [
            'ticket_attachments',
            'ticket_messages',
            'support_tickets',
            'attendances',
            'scores',
            'score_batches',
            'assessment_results',
            'assessments',
            'assessment_schedules',
            'timetables',
            'class_subject',
            'parent_student',
            'student_parent',
            'invoices',
            'invoice_items',
            'payments',
            'students',
            'parents_guardians',
            'staff',
            'report_cards',
            'report_card_items',
        ];

        foreach ($tablesToClear as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->command->info("   Cleared: {$table}");
            }
        }

        // Clear non-admin users
        User::whereNotIn('id', $adminUserIds)->delete();
        $this->command->info('   Cleared non-admin users');

        // Clear other reference tables
        $refTables = [
            'class_arms',
            'school_classes',
            'subjects',
            'academic_sessions',
            'terms',
            'session_terms',
        ];

        foreach ($refTables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->command->info("   Cleared: {$table}");
            }
        }

        Schema::enableForeignKeyConstraints();

        $this->command->info('');
        $this->command->info('🌱 Starting fresh data seeding...');
        $this->command->info('');

        // Run the production seeder
        $this->call(ProductionSeeder::class);
    }
}
