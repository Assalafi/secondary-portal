<?php

namespace Tests\Feature;

use App\Models\ClassArm;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ParentDependentAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_can_assign_an_available_student_to_their_account(): void
    {
        $parent = User::factory()->create(['name' => 'Mrs. Parent']);
        $student = $this->createActiveStudent('Adebayo Garba');

        $this->actingAs($parent)
            ->post(route('parent.dependents.assign'), [
                'student_id' => $student->id,
                'relationship' => 'Mother',
                'is_primary' => '1',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('parent_student', [
            'parent_id' => $parent->id,
            'student_id' => $student->id,
            'relationship' => 'Mother',
            'is_primary' => true,
        ]);

        $this->actingAs($parent)
            ->get(route('parent.dependents.index'))
            ->assertOk()
            ->assertSee('Adebayo Garba')
            ->assertSee('Linked to you')
            ->assertSee('Already Assigned');
    }

    public function test_parent_cannot_assign_the_same_student_twice(): void
    {
        $parent = User::factory()->create(['name' => 'Mrs. Parent']);
        $student = $this->createActiveStudent('Adebayo Garba');

        DB::table('parent_student')->insert([
            'parent_id' => $parent->id,
            'student_id' => $student->id,
            'relationship' => 'Guardian',
            'date_added' => now()->toDateString(),
            'is_primary' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($parent)
            ->post(route('parent.dependents.assign'), [
                'student_id' => $student->id,
                'relationship' => 'Mother',
            ])
            ->assertRedirect()
            ->assertSessionHas('info');

        $this->assertSame(1, DB::table('parent_student')
            ->where('parent_id', $parent->id)
            ->where('student_id', $student->id)
            ->count());
    }

    public function test_parent_can_unassign_their_own_dependent_from_a_form_post(): void
    {
        $parent = User::factory()->create(['name' => 'Mrs. Parent']);
        $student = $this->createActiveStudent('Adebayo Garba');

        DB::table('parent_student')->insert([
            'parent_id' => $parent->id,
            'student_id' => $student->id,
            'relationship' => 'Guardian',
            'date_added' => now()->toDateString(),
            'is_primary' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($parent)
            ->post(route('parent.dependents.remove', $student->id))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('parent_student', [
            'parent_id' => $parent->id,
            'student_id' => $student->id,
        ]);
    }

    private function createActiveStudent(string $name): Student
    {
        [$firstName, $surname] = explode(' ', $name, 2);

        $studentUser = User::factory()->create(['name' => $name]);
        $class = SchoolClass::create([
            'level' => 'SS',
            'name' => 'SS 3',
            'numeric_level' => 3,
        ]);
        $classArm = ClassArm::create([
            'school_class_id' => $class->id,
            'name' => 'B',
        ]);

        return Student::create([
            'user_id' => $studentUser->id,
            'admission_no' => 'SSP/2026/' . str_pad((string) random_int(1, 999), 4, '0', STR_PAD_LEFT),
            'admission_date' => '2024-09-01',
            'surname' => $surname,
            'first_name' => $firstName,
            'gender' => 'Male',
            'dob' => '2008-06-10',
            'current_class_arm_id' => $classArm->id,
            'status' => 'Active',
        ]);
    }
}
