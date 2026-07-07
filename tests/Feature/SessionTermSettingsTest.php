<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\SchoolSettings;
use App\Models\SessionTerm;
use App\Models\Term;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionTermSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_session_term_and_it_syncs_filter_tables(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->postJson(route('admin.settings.session-term.store'), [
                'academic_year' => '2026/2027',
                'term_name' => 'First Term',
                'start_date' => '2026-09-01',
                'end_date' => '2026-12-15',
                'is_current' => '1',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('session_terms', [
            'academic_year' => '2026/2027',
            'term_name' => '1st Term',
            'is_current' => true,
        ]);

        $this->assertDatabaseHas('academic_sessions', [
            'name' => '2026/2027',
            'is_current' => true,
        ]);

        $this->assertDatabaseHas('terms', [
            'name' => '1st Term',
            'number' => 1,
            'is_current' => true,
        ]);

        $settings = SchoolSettings::firstOrFail();
        $this->assertSame('2026/2027', $settings->academic_session);
        $this->assertSame('1st Term', $settings->current_term);
    }

    public function test_admin_can_update_set_current_and_delete_non_current_session_term(): void
    {
        $admin = User::factory()->create();
        $old = SessionTerm::create([
            'academic_year' => '2025/2026',
            'term_name' => '1st Term',
            'start_date' => '2025-09-01',
            'end_date' => '2025-12-15',
            'is_current' => true,
            'status' => 'Active',
        ]);
        $new = SessionTerm::create([
            'academic_year' => '2026/2027',
            'term_name' => '2nd Term',
            'start_date' => '2027-01-10',
            'end_date' => '2027-04-10',
            'is_current' => false,
            'status' => 'Active',
        ]);

        $this->actingAs($admin)
            ->putJson(route('admin.settings.session-term.update', $new), [
                'academic_year' => '2026/2027',
                'term_name' => 'Third Term',
                'start_date' => '2027-04-25',
                'end_date' => '2027-07-20',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('session_terms', [
            'id' => $new->id,
            'term_name' => '3rd Term',
        ]);
        $this->assertDatabaseHas('terms', [
            'name' => '3rd Term',
            'number' => 3,
        ]);

        $this->actingAs($admin)
            ->postJson(route('admin.settings.session-term.set-current', $new->fresh()))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertFalse($old->fresh()->is_current);
        $this->assertTrue($new->fresh()->is_current);
        $this->assertSame('2026/2027', AcademicSession::where('is_current', true)->first()?->name);
        $this->assertSame('3rd Term', Term::where('is_current', true)->first()?->name);

        $this->actingAs($admin)
            ->deleteJson(route('admin.settings.session-term.delete', $new->fresh()))
            ->assertStatus(422);

        $this->actingAs($admin)
            ->deleteJson(route('admin.settings.session-term.delete', $old->fresh()))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('session_terms', ['id' => $old->id]);
    }
}
