<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParentStudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the parent user
        $parent = User::where('email', 'parent@school.com')->first();
        
        if (!$parent) {
            $this->command->error('Parent user not found. Please run ParentRoleSeeder first.');
            return;
        }

        // Get some students to link (first 3 students in the database)
        $students = Student::with('user')->limit(3)->get();
        
        if ($students->isEmpty()) {
            $this->command->warn('No students found in database. Please create some students first.');
            return;
        }

        // Link students to parent
        foreach ($students as $index => $student) {
            DB::table('parent_student')->updateOrInsert(
                [
                    'parent_id' => $parent->id,
                    'student_id' => $student->id,
                ],
                [
                    'relationship' => $index === 0 ? 'Father' : ($index === 1 ? 'Mother' : 'Guardian'),
                    'date_added' => now(),
                    'is_primary' => $index === 0, // First student is primary contact
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            
            $this->command->info("Linked {$student->user->name} to parent as dependent");
        }
        
        $this->command->info("\n✓ Successfully linked {$students->count()} student(s) to parent@school.com");
        $this->command->info("You can now login as parent@school.com to view dependents.");
    }
}
