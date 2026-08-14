<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerNoteManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_super_admin_can_add_and_archive_customer_note(): void
    {
        $admin = User::query()->where('email', 'admin@aksisoft.test')->firstOrFail();
        $client = Client::create(['company_name' => 'PT Note Test', 'currency' => 'IDR', 'assigned_staff_id' => $admin->id, 'created_by' => $admin->id, 'is_active' => true]);

        $this->actingAs($admin)->post("/clients/{$client->id}/notes", ['content' => 'Catatan tindak lanjut pelanggan.'])->assertRedirect();
        $this->assertDatabaseHas('notes', ['related_type' => Client::class, 'related_id' => $client->id, 'created_by' => $admin->id, 'content' => 'Catatan tindak lanjut pelanggan.']);

        $noteId = (int) $client->notes()->value('id');
        $this->actingAs($admin)->delete("/clients/{$client->id}/notes/{$noteId}")->assertRedirect();
        $this->assertSoftDeleted('notes', ['id' => $noteId]);
    }
}
