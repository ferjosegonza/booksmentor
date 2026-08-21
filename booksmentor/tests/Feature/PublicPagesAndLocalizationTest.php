<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicPagesAndLocalizationTest extends TestCase
{
    public function test_landing_page_renders_with_multilingual_support()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('BooksMentor');

        // Switch to English
        $langResp = $this->get('/lang/en');
        $langResp->assertRedirect();
        $this->assertEquals('en', session('locale'));

        // Switch to Chinese
        $zhResp = $this->get('/lang/zh');
        $zhResp->assertRedirect();
        $this->assertEquals('zh', session('locale'));
    }

    public function test_public_explore_and_donations_pages_render()
    {
        $this->get('/explorar')->assertStatus(200);
        $this->get('/planes')->assertStatus(200);
        $this->get('/donaciones')->assertStatus(200);
    }

    public function test_public_user_can_submit_suggestion()
    {
        $response = $this->post('/sugerir', [
            'email' => 'visitor@test.com',
            'tipo_id' => 1,
            'libro_sugerido' => 'Sapiens',
            'mensaje' => 'Sería genial tener lecciones diarias de Sapiens.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('sugerencias_usuarios', [
            'email' => 'visitor@test.com',
            'libro_sugerido' => 'Sapiens',
        ]);
    }
}