<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_payment_updates_invoice_paid_amount_and_status(): void
    {
        $admin = User::query()->where('email', 'admin@aksisoft.test')->firstOrFail();
        $client = Client::create(['company_name' => 'PT Payment Test', 'currency' => 'IDR', 'assigned_staff_id' => $admin->id, 'created_by' => $admin->id, 'is_active' => true]);
        $invoice = Invoice::create(['client_id' => $client->id, 'number' => 'INV-TEST-001', 'date' => now(), 'status' => 'unpaid', 'subtotal' => 100000, 'discount' => 0, 'total' => 100000, 'paid_amount' => 0, 'created_by' => $admin->id]);

        $this->actingAs($admin)->post("/invoices/{$invoice->id}/payments", [
            'amount' => 40000,
            'paid_at' => now()->toDateString(),
            'payment_mode' => 'transfer',
            'transaction_id' => 'TRX-001',
        ])->assertRedirect();

        $this->assertDatabaseHas('payments', ['invoice_id' => $invoice->id, 'amount' => 40000, 'payment_mode' => 'transfer']);
        $this->assertDatabaseHas('invoices', ['id' => $invoice->id, 'paid_amount' => 40000, 'status' => 'partial']);
    }
}
