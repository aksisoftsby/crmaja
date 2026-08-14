<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EstimateManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_super_admin_can_create_an_estimate_with_calculated_total(): void
    {
        $admin = User::query()->where('email', 'admin@aksisoft.test')->firstOrFail();
        $client = Client::create(['company_name' => 'PT Estimate Test', 'currency' => 'IDR', 'assigned_staff_id' => $admin->id, 'created_by' => $admin->id, 'is_active' => true]);
        $this->actingAs($admin)->post('/estimates', ['client_id' => $client->id, 'date' => now()->toDateString(), 'expiry_date' => now()->addWeek()->toDateString(), 'discount' => 5000, 'notes' => 'Quotation pengujian.', 'items' => [['item_id' => null, 'description' => 'Lisensi', 'qty' => 3, 'rate' => 25000]]])->assertRedirect();
        $this->assertDatabaseHas('estimates', ['client_id' => $client->id, 'subtotal' => 75000, 'discount' => 5000, 'total' => 70000]);
        $this->assertDatabaseHas('estimate_items', ['description' => 'Lisensi', 'qty' => 3, 'rate' => 25000, 'amount' => 75000]);
    }
}
