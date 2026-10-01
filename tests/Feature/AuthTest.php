<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_a_customer_even_if_role_is_posted(): void
    {
        $this->post(route('register.store'), [
            'name' => 'زبون', 'email' => 'a@example.com',
            'password' => 'password123', 'password_confirmation' => 'password123',
            'role' => 'admin',
        ])->assertRedirect('/');

        $this->assertSame('customer', User::first()->role);
        $this->assertAuthenticated();
    }

    public function test_customer_can_login_and_logout(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        $this->post(route('login.attempt'), ['email' => $user->email, 'password' => 'password123'])->assertRedirect('/');
        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_wrong_password_is_rejected(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        $this->post(route('login.attempt'), ['email' => $user->email, 'password' => 'bad'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_staff_login_redirects_to_admin_panel(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'password' => 'password123']);

        $this->post(route('login.attempt'), ['email' => $admin->email, 'password' => 'password123'])->assertRedirect('/admin');
    }

    public function test_guest_pages_render(): void
    {
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
    }
}
