<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;
use App\Models\Usuario;
use App\Models\Libro;
use App\Models\Suscripcion;

class SubscriptionsAndReaderTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_can_view_subscriptions_and_pause()
    {
        $user = User::factory()->create(['role' => 'user']);
        $usuario = Usuario::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'nombre' => $user->name,
            'frecuencia_id' => 1,
            'plan_id' => 1,
            'activo' => true,
        ]);

        $book = Libro::first();
        $sub = Suscripcion::create([
            'usuario_id' => $usuario->id,
            'libro_id' => $book->id,
            'estado_id' => 1,
            'ultima_ensenanza_enviada' => 0,
        ]);
        $sub->idiomas()->sync([1, 2]);

        $response = $this->actingAs($user)->get('/dashboard/suscripciones');
        $response->assertStatus(200);
        $response->assertSee($book->titulo);

        // Pause subscription
        $pauseResp = $this->actingAs($user)->post("/dashboard/suscripciones/{$sub->id}/pausar");
        $pauseResp->assertRedirect();
        $this->assertEquals(3, $sub->fresh()->estado_id);
    }

    public function test_user_can_access_interactive_reader()
    {
        $user = User::factory()->create(['role' => 'user']);
        $book = Libro::first();

        $response = $this->actingAs($user)->get("/dashboard/leer/{$book->id}/1");
        $response->assertStatus(200);
        $response->assertSee($book->titulo);
    }

    public function test_user_can_trigger_on_demand_email_delivery()
    {
        $user = User::factory()->create(['role' => 'user']);
        $usuario = Usuario::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'nombre' => $user->name,
            'frecuencia_id' => 1,
            'plan_id' => 1,
            'activo' => true,
        ]);

        $book = Libro::first();
        $sub = Suscripcion::create([
            'usuario_id' => $usuario->id,
            'libro_id' => $book->id,
            'estado_id' => 1,
            'ultima_ensenanza_enviada' => 0,
        ]);
        $sub->idiomas()->sync([1]);

        $response = $this->actingAs($user)->post("/dashboard/suscripciones/{$sub->id}/enviar-prueba");
        $response->assertRedirect();
        $this->assertGreaterThanOrEqual(1, $sub->fresh()->ultima_ensenanza_enviada);
    }
}