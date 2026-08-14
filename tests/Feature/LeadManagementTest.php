<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_super_admin_can_create_a_lead(): void
    {
        $admin = User::query()->where('email', 'admin@aksisoft.test')->firstOrFail();
        $status = LeadStatus::query()->where('is_default', true)->firstOrFail();

        $this->actingAs($admin)->post('/leads', [
            'name' => 'Dewi Kurniawan',
            'company_name' => 'PT Potensial Maju',
            'email' => 'dewi@example.test',
            'phone' => '08121234567',
            'source_id' => null,
            'status_id' => $status->id,
            'assigned_to' => $admin->id,
            'lead_value' => 15000000,
            'notes' => 'Memerlukan demo CRM.',
        ])->assertRedirect();

        $this->assertDatabaseHas('leads', [
            'name' => 'Dewi Kurniawan',
            'status_id' => $status->id,
            'assigned_to' => $admin->id,
            'created_by' => $admin->id,
        ]);
    }

    public function test_super_admin_can_view_the_lead_index(): void
    {
        $admin = User::query()->where('email', 'admin@aksisoft.test')->firstOrFail();
        $status = LeadStatus::query()->where('is_default', true)->firstOrFail();

        Lead::create([
            'name' => 'Prospek Uji',
            'status_id' => $status->id,
            'assigned_to' => $admin->id,
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)->get('/leads')
            ->assertOk()
            ->assertSee('Prospek Uji');
    }
}
