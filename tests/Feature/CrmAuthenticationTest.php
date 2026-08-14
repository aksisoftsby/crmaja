<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CrmAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_is_available(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_super_admin_can_log_in_and_access_dashboard(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@aksisoft.test',
            'password' => Hash::make('ChangeMe123!'),
        ]);

        $admin->assignRole(Role::create([
            'name' => 'Super Admin',
            'guard_name' => 'web',
        ]));

        $this->withoutMiddleware(ValidateCsrfToken::class);

        $response = $this->post('/login', [
            'email' => 'admin@aksisoft.test',
            'password' => 'ChangeMe123!',
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($admin);
        $this->get('/dashboard')->assertOk();
    }

    public function test_public_staff_registration_is_not_available(): void
    {
        $this->get('/register')->assertNotFound();
    }

    public function test_dashboard_redirects_guests_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }
}
