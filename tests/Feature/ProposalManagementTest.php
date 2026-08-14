<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProposalManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_super_admin_can_create_a_proposal_with_calculated_total(): void
    {
        $admin = User::query()->where('email', 'admin@aksisoft.test')->firstOrFail();
        $client = Client::create(['company_name' => 'PT Proposal Test', 'currency' => 'IDR', 'assigned_staff_id' => $admin->id, 'created_by' => $admin->id, 'is_active' => true]);

        $this->actingAs($admin)->post('/proposals', [
            'client_id' => $client->id,
            'lead_id' => null,
            'subject' => 'Penawaran CRM',
            'content' => 'Ruang lingkup implementasi.',
            'date' => now()->toDateString(),
            'open_till' => now()->addWeek()->toDateString(),
            'discount' => 10000,
            'items' => [['item_id' => null, 'description' => 'Implementasi', 'qty' => 2, 'rate' => 50000]],
        ])->assertRedirect();

        $this->assertDatabaseHas('proposals', ['client_id' => $client->id, 'subject' => 'Penawaran CRM', 'subtotal' => 100000, 'discount' => 10000, 'total' => 90000]);
        $this->assertDatabaseHas('proposal_items', ['description' => 'Implementasi', 'qty' => 2, 'rate' => 50000, 'amount' => 100000]);
    }
}
