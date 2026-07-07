<?php

namespace Tests\Feature;

use App\Models\ClassArm;
use App\Models\ReportCard;
use App\Models\ReportSettings;
use App\Models\SchoolClass;
use App\Models\SessionTerm;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportCardBulkActionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private SchoolClass $class;
    private SessionTerm $sessionTerm;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->class = SchoolClass::create([
            'level' => 'SS',
            'name' => 'SS 3',
            'numeric_level' => 3,
        ]);

        ClassArm::create([
            'school_class_id' => $this->class->id,
            'name' => 'B',
        ]);

        $this->sessionTerm = SessionTerm::create([
            'academic_year' => '2024/2025',
            'term_name' => '3rd Term',
            'start_date' => '2025-04-21',
            'end_date' => '2025-07-18',
            'is_current' => true,
            'status' => 'Active',
        ]);
    }

    public function test_bulk_approve_only_approves_draft_report_cards(): void
    {
        $draft = $this->reportCard('draft', 'Garba', 'Adebayo');
        $approved = $this->reportCard('approved', 'Onwueme', 'Mercy');

        $this->actingAs($this->admin)
            ->post(route('admin.academic-management.report-cards.bulk-action'), [
                'action' => 'approve',
                'report_card_ids' => [$draft->id, $approved->id],
            ])
            ->assertRedirect(route('admin.academic-management.report-cards.index'))
            ->assertSessionHas('success')
            ->assertSessionHas('bulk_report_card_warnings');

        $this->assertSame('approved', $draft->fresh()->status);
        $this->assertSame($this->admin->id, $draft->fresh()->approved_by);
        $this->assertSame('approved', $approved->fresh()->status);
    }

    public function test_bulk_publish_requires_approval_when_report_settings_require_it(): void
    {
        ReportSettings::getSettings()->update(['require_principal_approval' => true]);

        $draft = $this->reportCard('draft', 'Garba', 'Adebayo');
        $approved = $this->reportCard('approved', 'Onwueme', 'Mercy');

        $this->actingAs($this->admin)
            ->post(route('admin.academic-management.report-cards.bulk-action'), [
                'action' => 'publish',
                'report_card_ids' => [$draft->id, $approved->id],
            ])
            ->assertRedirect(route('admin.academic-management.report-cards.index'))
            ->assertSessionHas('success')
            ->assertSessionHas('bulk_report_card_warnings');

        $this->assertSame('draft', $draft->fresh()->status);
        $this->assertSame('published', $approved->fresh()->status);
        $this->assertNotNull($approved->fresh()->published_at);
        $this->assertNotNull($approved->fresh()->verification_code);
    }

    public function test_bulk_approve_and_publish_handles_draft_and_approved_cards_together(): void
    {
        $draft = $this->reportCard('draft', 'Garba', 'Adebayo');
        $approved = $this->reportCard('approved', 'Onwueme', 'Mercy');
        $published = $this->reportCard('published', 'Uzoma', 'Somtochukwu');

        $this->actingAs($this->admin)
            ->post(route('admin.academic-management.report-cards.bulk-action'), [
                'action' => 'approve_publish',
                'report_card_ids' => [$draft->id, $approved->id, $published->id],
            ])
            ->assertRedirect(route('admin.academic-management.report-cards.index'))
            ->assertSessionHas('success')
            ->assertSessionHas('bulk_report_card_warnings');

        $this->assertSame('published', $draft->fresh()->status);
        $this->assertSame($this->admin->id, $draft->fresh()->approved_by);
        $this->assertSame($this->admin->id, $draft->fresh()->published_by);
        $this->assertSame('published', $approved->fresh()->status);
        $this->assertSame('published', $published->fresh()->status);
    }

    private function reportCard(string $status, string $surname, string $firstName): ReportCard
    {
        $student = Student::create([
            'admission_no' => 'SSP/' . fake()->unique()->numberBetween(1000, 9999),
            'admission_date' => '2024-09-01',
            'surname' => $surname,
            'first_name' => $firstName,
            'gender' => 'Male',
            'dob' => '2008-06-10',
        ]);

        return ReportCard::create([
            'student_id' => $student->id,
            'class_id' => $this->class->id,
            'academic_session_id' => $this->sessionTerm->id,
            'term_id' => $this->sessionTerm->id,
            'report_type' => 'termly',
            'status' => $status,
            'average_score' => 65,
            'final_grade' => 'B3',
            'verification_code' => $status === 'published' ? 'RPT-' . fake()->unique()->bothify('????####') : null,
            'published_at' => $status === 'published' ? now() : null,
        ]);
    }
}
