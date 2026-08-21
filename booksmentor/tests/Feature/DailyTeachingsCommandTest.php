<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;
use App\Models\Usuario;
use App\Models\Libro;
use App\Models\Suscripcion;
use Carbon\Carbon;

class DailyTeachingsCommandTest extends TestCase
{
    use DatabaseTransactions;

    public function test_teachings_send_command_executes_successfully()
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
            'fecha_proximo_envio' => Carbon::now()->subMinute(),
        ]);
        $sub->idiomas()->sync([1, 2]);

        $this->artisan('teachings:send --force')
             ->assertExitCode(0);

        $this->assertDatabaseHas('historial_envios', [
            'usuario_id' => $usuario->id,
        ]);
    }
}