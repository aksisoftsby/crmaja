<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Item;
use App\Models\Tax;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CoreHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_super_admin_can_create_staff_and_inactive_staff_cannot_login(): void
    {
        $admin = User::query()->where('email', 'admin@aksisoft.test')->firstOrFail();

        $this->actingAs($admin)->post('/staff', [
            'name' => 'Staff Dinonaktifkan',
            'email' => 'inactive.staff@example.test',
            'password' => 'StrongPassword123!',
            'password_confirmation' => 'StrongPassword123!',
            'roles' => ['Staff'],
            'is_active' => false,
        ])->assertRedirect('/staff');

        $this->assertDatabaseHas('users', ['email' => 'inactive.staff@example.test', 'is_active' => false]);
        Auth::logout();
        $this->post('/login', ['email' => 'inactive.staff@example.test', 'password' => 'StrongPassword123!'])
            ->assertSessionHasErrors('email');
    }

    public function test_invoice_snapshots_selected_tax_rate_and_total(): void
    {
        $admin = User::query()->where('email', 'admin@aksisoft.test')->firstOrFail();
        $client = Client::create(['company_name' => 'PT Finance Test', 'currency' => 'IDR', 'assigned_staff_id' => $admin->id, 'created_by' => $admin->id, 'is_active' => true]);
        $item = Item::create(['title' => 'Implementasi', 'rate' => 100000, 'is_active' => true]);
        $tax = Tax::query()->where('name', 'PPN')->firstOrFail();

        $this->actingAs($admin)->post('/invoices', [
            'client_id' => $client->id,
            'date' => now()->toDateString(),
            'discount' => 10000,
            'tax_id' => $tax->id,
            'items' => [['item_id' => $item->id, 'description' => 'Implementasi', 'qty' => 2, 'rate' => 100000]],
        ])->assertRedirect();

        $this->assertDatabaseHas('invoices', ['client_id' => $client->id, 'subtotal' => 200000, 'discount' => 10000, 'tax_rate' => 11, 'tax_amount' => 20900, 'total' => 210900]);
    }
}
