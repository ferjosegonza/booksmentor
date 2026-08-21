<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;
use App\Models\Usuario;
use App\Models\Libro;
use App\Models\SugerenciaUsuario;

class AdminPanelCRUDTest extends TestCase
{
    use DatabaseTransactions;

    public function test_non_admin_cannot_access_admin_dashboard()
    {
        $normalUser = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($normalUser)->get('/admin');
        $response->assertRedirect('/dashboard');
    }

    public function test_admin_can_view_dashboard_and_crud_books()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Panel de Control General');

        // Can view books index
        $booksResp = $this->actingAs($admin)->get('/admin/libros');
        $booksResp->assertStatus(200);

        // Can view configurations
        $configResp = $this->actingAs($admin)->get('/admin/configuracion');
        $configResp->assertStatus(200);
    }

    public function test_admin_can_reply_to_suggestions()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $sug = SugerenciaUsuario::create([
            'email' => 'user_feedback@test.com',
            'tipo_id' => 1,
            'mensaje' => 'Por favor agreguen el libro Antifrágil de Nassim Taleb.',
            'leido' => false,
            'atendido' => false,
            'fecha_envio' => now(),
        ]);

        $response = $this->actingAs($admin)->post("/admin/sugerencias/{$sug->id}/responder", [
            'respuesta_admin' => '¡Excelente sugerencia! Ya está en proceso de extracción con IA.',
        ]);

        $response->assertRedirect('/admin/sugerencias');
        $this->assertTrue($sug->fresh()->atendido);
        $this->assertEquals('¡Excelente sugerencia! Ya está en proceso de extracción con IA.', $sug->fresh()->respuesta_admin);
    }
}