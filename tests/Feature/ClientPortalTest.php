<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ClientPortalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_contact_can_log_in_and_only_see_its_company_data(): void
    {
        $admin = User::query()->where('email', 'admin@aksisoft.test')->firstOrFail();
        $client = Client::create(['company_name' => 'PT Portal Utama', 'currency' => 'IDR', 'assigned_staff_id' => $admin->id, 'created_by' => $admin->id, 'is_active' => true]);
        $otherClient = Client::create(['company_name' => 'PT Lain', 'currency' => 'IDR', 'assigned_staff_id' => $admin->id, 'created_by' => $admin->id, 'is_active' => true]);
        $contact = Contact::create(['client_id' => $client->id, 'first_name' => 'Portal', 'last_name' => 'User', 'email' => 'portal@example.test', 'is_primary' => true, 'is_active' => true, 'password' => Hash::make('PortalPass123!')]);
        Invoice::create(['client_id' => $client->id, 'number' => 'INV-PORTAL-OWN', 'date' => now(), 'status' => 'unpaid', 'subtotal' => 1000, 'discount' => 0, 'total' => 1000, 'paid_amount' => 0, 'created_by' => $admin->id]);
        Invoice::create(['client_id' => $otherClient->id, 'number' => 'INV-PORTAL-OTHER', 'date' => now(), 'status' => 'unpaid', 'subtotal' => 2000, 'discount' => 0, 'total' => 2000, 'paid_amount' => 0, 'created_by' => $admin->id]);

        $this->get('/portal/login')->assertOk();
        $this->post('/portal/login', ['email' => $contact->email, 'password' => 'PortalPass123!'])->assertRedirect('/portal');
        $this->get('/portal/invoices')->assertOk()->assertSee('INV-PORTAL-OWN')->assertDontSee('INV-PORTAL-OTHER');
    }

    public function test_portal_contact_can_submit_ticket_for_own_company(): void
    {
        $admin = User::query()->where('email', 'admin@aksisoft.test')->firstOrFail();
        $client = Client::create(['company_name' => 'PT Ticket Portal', 'currency' => 'IDR', 'assigned_staff_id' => $admin->id, 'created_by' => $admin->id, 'is_active' => true]);
        $contact = Contact::create(['client_id' => $client->id, 'first_name' => 'Ticket', 'last_name' => 'Portal', 'email' => 'ticket.portal@example.test', 'is_primary' => true, 'is_active' => true, 'password' => Hash::make('PortalPass123!')]);

        $this->post('/portal/login', ['email' => $contact->email, 'password' => 'PortalPass123!']);
        $this->post('/portal/tickets', ['subject' => 'Bantuan portal', 'priority' => 'medium', 'message' => 'Mohon bantuan akses.'])->assertRedirect('/portal/tickets');

        $this->assertDatabaseHas('tickets', ['subject' => 'Bantuan portal', 'client_id' => $client->id, 'contact_id' => $contact->id, 'source' => 'portal']);
        $this->assertDatabaseHas('ticket_replies', ['user_type' => 'contact', 'user_id' => $contact->id, 'message' => 'Mohon bantuan akses.']);
    }
}
