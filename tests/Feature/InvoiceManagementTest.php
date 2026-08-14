<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_super_admin_can_create_an_invoice_with_calculated_total(): void
    {
        $admin = User::query()->where('email', 'admin@aksisoft.test')->firstOrFail();
        $client = Client::create(['company_name' => 'PT Invoice Test', 'currency' => 'IDR', 'assigned_staff_id' => $admin->id, 'created_by' => $admin->id, 'is_active' => true]);

        $this->actingAs($admin)->post('/invoices', [
            'client_id' => $client->id,
            'date' => now()->toDateString(),
            'due_date' => now()->addWeek()->toDateString(),
            'discount' => 2500,
            'notes' => 'Tagihan pengujian.',
            'items' => [['item_id' => null, 'description' => 'Implementasi', 'qty' => 2, 'rate' => 40000]],
        ])->assertRedirect();

        $this->assertDatabaseHas('invoices', ['client_id' => $client->id, 'status' => 'unpaid', 'subtotal' => 80000, 'discount' => 2500, 'total' => 77500]);
        $this->assertDatabaseHas('invoice_items', ['description' => 'Implementasi', 'qty' => 2, 'rate' => 40000, 'amount' => 80000]);
    }

    public function test_super_admin_can_download_an_invoice_pdf(): void
    {
        $admin = User::query()->where('email', 'admin@aksisoft.test')->firstOrFail();
        $client = Client::create(['company_name' => 'PT PDF Test', 'currency' => 'IDR', 'assigned_staff_id' => $admin->id, 'created_by' => $admin->id, 'is_active' => true]);
        $invoice = Invoice::create(['client_id' => $client->id, 'number' => 'INV-PDF-001', 'date' => now(), 'status' => 'unpaid', 'subtotal' => 50000, 'discount' => 0, 'total' => 50000, 'paid_amount' => 0, 'created_by' => $admin->id]);
        $invoice->items()->create(['description' => 'Layanan PDF', 'qty' => 1, 'rate' => 50000, 'amount' => 50000]);

        $this->actingAs($admin)->get("/invoices/{$invoice->id}/pdf")
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }
}
