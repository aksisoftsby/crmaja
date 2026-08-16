<?php

namespace Tests\Feature;

use Tests\TestCase;

class DemoLoginPickerTest extends TestCase
{
    public function test_staff_login_renders_demo_account_picker(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Akun Demo')
            ->assertSee('data-demo-account="super-admin"', false)
            ->assertSee('sales@demo.aksisoft.test')
            ->assertSee('support@demo.aksisoft.test');
    }

    public function test_client_portal_login_renders_demo_account_picker(): void
    {
        $this->get('/portal/login')
            ->assertOk()
            ->assertSee('Akun Demo')
            ->assertSee('data-demo-account="client-portal"', false)
            ->assertSee('client@aksisoft.test');
    }
}
