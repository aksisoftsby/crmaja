<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Ticket;
use App\Models\TicketDepartment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_super_admin_can_create_and_reply_to_a_ticket(): void
    {
        $admin = User::query()->where('email', 'admin@aksisoft.test')->firstOrFail();
        $client = Client::create(['company_name' => 'PT Support Test', 'currency' => 'IDR', 'assigned_staff_id' => $admin->id, 'created_by' => $admin->id, 'is_active' => true]);
        $department = TicketDepartment::query()->firstOrFail();

        $this->actingAs($admin)->post('/tickets', [
            'subject' => 'Butuh bantuan konfigurasi',
            'client_id' => $client->id,
            'department_id' => $department->id,
            'priority' => 'high',
            'status' => 'open',
            'assigned_to' => $admin->id,
            'message' => 'Mohon bantuan untuk konfigurasi awal.',
        ])->assertRedirect();

        $ticket = Ticket::query()->where('subject', 'Butuh bantuan konfigurasi')->firstOrFail();
        $this->assertDatabaseHas('ticket_replies', ['ticket_id' => $ticket->id, 'user_type' => 'staff', 'message' => 'Mohon bantuan untuk konfigurasi awal.']);

        $this->actingAs($admin)->post("/tickets/{$ticket->id}/replies", [
            'message' => 'Konfigurasi sedang kami periksa.',
        ])->assertRedirect();

        $this->assertDatabaseHas('ticket_replies', ['ticket_id' => $ticket->id, 'message' => 'Konfigurasi sedang kami periksa.', 'is_internal_note' => 0]);
        $this->assertDatabaseHas('tickets', ['id' => $ticket->id, 'status' => 'answered']);
    }
}
