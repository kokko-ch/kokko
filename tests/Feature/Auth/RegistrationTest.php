<?php

namespace Tests\Feature\Auth;

use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_not_be_rendered()
    {
        $response = $this->get('/register');

        $response->assertStatus(404);
    }

    public function test_new_users_can_not_register()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // $this->assertAuthenticated();
        $this->assertGuest();
        // $response->assertRedirect(RouteServiceProvider::HOME);
        $response->assertStatus(404);
    }
}
