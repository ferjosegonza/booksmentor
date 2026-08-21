<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;
use App\Models\Usuario;
use App\Models\Libro;
use App\Models\Ensenanza;
use App\Services\LLM\LLMService;

class LLMTranslationAndBookUploadTest extends TestCase
{
    use DatabaseTransactions;

    public function test_llm_service_extracts_teachings_and_translates()
    {
        $service = app(LLMService::class);
        
        $content = "Capítulo 1: El hábito del enfoque. La atención plena transforma la productividad.\n\nCapítulo 2: La consistencia diaria vence a la intensidad esporádica.";
        $teachings = $service->extractTeachingsFromBook($content, 2, 'es');

        $this->assertNotEmpty($teachings);
        $this->assertGreaterThanOrEqual(1, count($teachings));

        $translation = $service->translateText("La persistencia vence al talento", "es", "en");
        $this->assertNotEmpty($translation);
    }

    public function test_user_can_upload_custom_book_with_llm()
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

        $response = $this->actingAs($user)->post('/dashboard/mis-libros/crear', [
            'titulo' => 'Psicología del Éxito ' . uniqid(),
            'autor' => 'Autor de Prueba',
            'descripcion' => 'Un libro sobre mentalidad y éxito',
            'idioma_original_id' => 1,
            'target_idiomas' => [1, 2],
            'contenido' => "Lección 1: La mentalidad de crecimiento te permite aprender de los fracasos.\n\nLección 2: Rodéate de personas que eleven tus estándares.",
            'cantidad_ensenanzas' => 2,
        ]);

        $response->assertRedirect('/dashboard/suscripciones');
        $this->assertDatabaseHas('libros', ['autor' => 'Autor de Prueba']);
    }

    public function test_bulk_book_upload_processes_multiple_books()
    {
        $user = User::factory()->create(['role' => 'user']);
        $usuario = Usuario::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'nombre' => $user->name,
            'frecuencia_id' => 1,
            'plan_id' => 3, // Pro plan
            'activo' => true,
        ]);

        $bulkJson = json_encode([
            [
                'titulo' => 'Libro Bulk 1 ' . uniqid(),
                'autor' => 'Autor Bulk 1',
                'contenido' => 'El enfoque radical en una sola tarea produce resultados extraordinarios.'
            ],
            [
                'titulo' => 'Libro Bulk 2 ' . uniqid(),
                'autor' => 'Autor Bulk 2',
                'contenido' => 'La disciplina supera a la motivación en los días difíciles.'
            ]
        ]);

        $response = $this->actingAs($user)->post('/dashboard/mis-libros/bulk', [
            'bulk_data' => $bulkJson,
            'target_idiomas' => [1, 2],
        ]);

        $response->assertRedirect('/dashboard/suscripciones');
        $this->assertDatabaseHas('libros', ['autor' => 'Autor Bulk 1']);
        $this->assertDatabaseHas('libros', ['autor' => 'Autor Bulk 2']);
    }
}