<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    /**
     * Test unauthenticated users are redirected to login.
     */
    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /**
     * Test login page can be rendered.
     */
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('GarmentERP');
        $response->assertSee('admin@garmenterp.com');
    }

    /**
     * Test admin can login with valid credentials.
     */
    public function test_admin_can_authenticate_and_access_dashboard(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@garmenterp.com',
            'password' => 'admin123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');

        $dashboardResponse = $this->get('/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('GarmentERP');
    }

    /**
     * Test admin cannot login with invalid credentials.
     */
    public function test_admin_cannot_authenticate_with_invalid_password(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'admin@garmenterp.com',
            'password' => 'wrongpassword',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test user can log out.
     */
    public function test_user_can_logout(): void
    {
        $user = User::where('email', 'admin@garmenterp.com')->first();
        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }
}
