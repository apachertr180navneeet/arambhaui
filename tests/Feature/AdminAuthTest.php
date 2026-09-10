<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        User::firstOrCreate(
            ['email' => 'admin@garmenterp.com'],
            [
                'name' => 'Admin User',
                'password' => 'admin123',
                'role' => 'Administrator',
                'status' => 'Active'
            ]
        );

        User::firstOrCreate(
            ['email' => 'supervisor@garmenterp.com'],
            [
                'name' => 'Supervisor User',
                'password' => 'admin123',
                'role' => 'Production Manager',
                'status' => 'Active'
            ]
        );
    }

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
     * Test user can log out via POST.
     */
    public function test_user_can_logout_via_post(): void
    {
        $user = User::where('email', 'admin@garmenterp.com')->first();
        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }

    /**
     * Test user can log out via GET.
     */
    public function test_user_can_logout_via_get(): void
    {
        $user = User::where('email', 'admin@garmenterp.com')->first();
        $response = $this->actingAs($user)->get('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }

    /**
     * Test supervisor user can authenticate.
     */
    public function test_supervisor_can_authenticate(): void
    {
        $response = $this->post('/login', [
            'email' => 'supervisor@garmenterp.com',
            'password' => 'admin123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    /**
     * Test inactive user cannot authenticate.
     */
    public function test_inactive_user_cannot_authenticate(): void
    {
        $inactiveUser = User::create([
            'name' => 'Inactive Worker',
            'email' => 'inactive@garmenterp.com',
            'password' => 'admin123',
            'role' => 'worker',
            'status' => 'inactive'
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'inactive@garmenterp.com',
            'password' => 'admin123',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');

        $inactiveUser->delete();
    }
}
