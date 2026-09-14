<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_login_page_displays_password_visibility_toggle(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('id="password"', false);
        $response->assertSee('id="toggle-password"', false);
        $response->assertSee('data-lucide="eye"', false);
        $response->assertSee('data-lucide="eye-off"', false);
    }

    public function test_failed_login_stays_on_the_login_page(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@khajapos.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_deactivated_account_stays_on_the_login_page(): void
    {
        $admin = User::where('email', 'admin@khajapos.com')->first();
        $admin->is_active = false;
        $admin->save();

        $response = $this->post('/login', [
            'email' => 'admin@khajapos.com',
            'password' => 'KhajaPOS@123',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_valid_admin_login_redirects_to_dashboard(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@khajapos.com',
            'password' => 'KhajaPOS@123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }
}
