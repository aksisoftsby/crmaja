<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class NavigationIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_all_primary_staff_navigation_destinations_render_for_super_admin(): void
    {
        $admin = User::query()->where('email', 'admin@aksisoft.test')->firstOrFail();
        $pages = [
            '/dashboard', '/clients', '/clients/create', '/leads', '/leads/create',
            '/items', '/items/create', '/proposals', '/proposals/create', '/estimates',
            '/estimates/create', '/invoices', '/invoices/create', '/projects', '/projects/create',
            '/tasks', '/tasks/create', '/tickets', '/tickets/create', '/kb-articles',
            '/kb-articles/create', '/reports', '/profile',
        ];

        foreach ($pages as $page) {
            $response = $this->actingAs($admin)->get($page);
            $this->assertSame(200, $response->getStatusCode(), "Halaman staf gagal dirender: {$page}");
        }
    }

    public function test_all_primary_client_portal_navigation_destinations_render_for_contact(): void
    {
        $admin = User::query()->where('email', 'admin@aksisoft.test')->firstOrFail();
        $client = Client::create(['company_name' => 'PT Portal Navigation', 'currency' => 'IDR', 'assigned_staff_id' => $admin->id, 'created_by' => $admin->id, 'is_active' => true]);
        $contact = Contact::create(['client_id' => $client->id, 'first_name' => 'Portal', 'last_name' => 'Navigation', 'email' => 'portal.navigation@example.test', 'is_primary' => true, 'is_active' => true, 'password' => Hash::make('PortalPass123!')]);
        $pages = ['/portal', '/portal/invoices', '/portal/proposals', '/portal/estimates', '/portal/projects', '/portal/tickets', '/portal/knowledge-base'];

        foreach ($pages as $page) {
            $response = $this->actingAs($contact, 'portal')->get($page);
            $this->assertSame(200, $response->getStatusCode(), "Halaman portal gagal dirender: {$page}");
        }
    }
}
