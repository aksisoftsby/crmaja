<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_super_admin_can_create_a_customer(): void
    {
        $admin = User::query()->where('email', 'admin@aksisoft.test')->firstOrFail();

        $response = $this->actingAs($admin)->post('/clients', [
            'company_name' => 'PT Aksisoft Nusantara',
            'vat_number' => '12.345.678.9-012.000',
            'phone' => '031-555-0100',
            'website' => 'https://aksisoft.example',
            'address' => 'Jl. Teknologi No. 1',
            'city' => 'Surabaya',
            'state' => 'Jawa Timur',
            'zip' => '60293',
            'country' => 'Indonesia',
            'currency' => 'IDR',
            'customer_group_id' => null,
            'assigned_staff_id' => $admin->id,
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('clients', [
            'company_name' => 'PT Aksisoft Nusantara',
            'assigned_staff_id' => $admin->id,
            'created_by' => $admin->id,
            'is_active' => 1,
        ]);
    }

    public function test_only_one_contact_is_primary_for_a_customer(): void
    {
        $admin = User::query()->where('email', 'admin@aksisoft.test')->firstOrFail();
        $client = Client::create([
            'company_name' => 'PT Contoh Prima',
            'currency' => 'IDR',
            'assigned_staff_id' => $admin->id,
            'created_by' => $admin->id,
            'is_active' => true,
        ]);

        $firstContact = $client->contacts()->create([
            'first_name' => 'Rina',
            'email' => 'rina@example.test',
            'is_primary' => true,
            'is_active' => true,
        ]);

        $this->actingAs($admin)->post(route('clients.contacts.store', $client), [
            'first_name' => 'Budi',
            'last_name' => 'Santoso',
            'email' => 'budi@example.test',
            'phone' => '08123456789',
            'title' => 'Direktur',
            'is_primary' => true,
            'is_active' => true,
        ])->assertRedirect(route('clients.show', $client));

        $this->assertFalse($firstContact->fresh()->is_primary);
        $this->assertDatabaseHas('contacts', [
            'client_id' => $client->id,
            'email' => 'budi@example.test',
            'is_primary' => 1,
        ]);
    }

    public function test_user_without_customer_permissions_cannot_open_customer_list(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/clients')
            ->assertForbidden();
    }
}
