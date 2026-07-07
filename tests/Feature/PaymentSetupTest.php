<?php

namespace Tests\Feature;

use App\Models\PaymentSetup;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentSetupTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_update_school_fee_setup_for_actual_class_levels(): void
    {
        $admin = User::factory()->create();

        SchoolClass::create([
            'level' => 'SS',
            'name' => 'SS 3',
            'numeric_level' => 3,
        ]);

        $this->actingAs($admin)
            ->postJson(route('admin.payments.setup.store'), [
                'payment_type' => 'School Fees',
                'level' => 'SS',
                'term' => '3rd Term',
                'amount' => 45000,
                'effective_date' => '2026-07-07',
                'status' => 'Active',
                'description' => 'Senior secondary third term fees',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $setup = PaymentSetup::firstOrFail();

        $this->assertSame('SS', $setup->level);
        $this->assertSame('3rd Term', $setup->term);
        $this->assertSame('45000.00', $setup->amount);

        $this->actingAs($admin)
            ->putJson(route('admin.payments.setup.update', $setup), [
                'payment_type' => 'School Fees',
                'level' => 'SS',
                'term' => 'Term 3',
                'amount' => 50000,
                'effective_date' => '2026-07-07',
                'status' => 'Active',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $setup->refresh();

        $this->assertSame('3rd Term', $setup->term);
        $this->assertSame('50000.00', $setup->amount);
    }

    public function test_school_fee_resolution_prefers_exact_level_and_term_then_falls_back(): void
    {
        PaymentSetup::create([
            'payment_type' => 'School Fees',
            'level' => 'All',
            'term' => 'All',
            'amount' => 20000,
            'effective_date' => '2026-07-07',
            'last_updated' => now(),
            'status' => 'Active',
        ]);

        PaymentSetup::create([
            'payment_type' => 'School Fees',
            'level' => 'Secondary',
            'term' => 'All',
            'amount' => 30000,
            'effective_date' => '2026-07-07',
            'last_updated' => now(),
            'status' => 'Active',
        ]);

        PaymentSetup::create([
            'payment_type' => 'School Fees',
            'level' => 'SS',
            'term' => '3rd Term',
            'amount' => 45000,
            'effective_date' => '2026-07-07',
            'last_updated' => now(),
            'status' => 'Active',
        ]);

        $this->assertSame('45000.00', PaymentSetup::schoolFeeFor('SS', 'Term 3')->amount);
        $this->assertSame('30000.00', PaymentSetup::schoolFeeFor('JSS', '3rd Term')->amount);
        $this->assertSame('20000.00', PaymentSetup::schoolFeeFor('Primary', '1st Term')->amount);
    }
}
