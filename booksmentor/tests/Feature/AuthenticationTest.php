<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;
use App\Models\Usuario;
use App\Models\Libro;

class AuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_login_page_renders_successfully()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Iniciar Sesión');
    }

    public function test_user_can_login_and_redirect_to_dashboard()
    {
        $user = User::factory()->create([
            'email' => 'testuser_' . uniqid() . '@test.com',
            'password' => bcrypt('password123'),
            'role' => 'user',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_can_login_and_redirect_to_admin_panel()
    {
        $admin = User::factory()->create([
            'email' => 'testadmin_' . uniqid() . '@test.com',
            'password' => bcrypt('adminpass123'),
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'adminpass123',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_user_can_register_with_onboarding()
    {
        $email = 'newbie_' . uniqid() . '@test.com';
        $book = Libro::first();

        $response = $this->post('/register', [
            'name' => 'Carlos López',
            'email' => $email,
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'plan_id' => 1,
            'frecuencia_id' => 1,
            'hora_envio' => '07:00',
            'libro_id' => $book ? $book->id : null,
            'idiomas' => [1, 2],
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', ['email' => $email]);
        $this->assertDatabaseHas('usuarios', ['email' => $email]);
    }

    public function test_guest_is_redirected_from_dashboard()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }
}