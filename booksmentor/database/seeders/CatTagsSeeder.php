<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatTagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cat_tags')->insert([
            ['id' => 1, 'nombre' => 'Productividad', 'slug' => 'productividad', 'icono' => '⚡'],
            ['id' => 2, 'nombre' => 'Hábitos', 'slug' => 'habitos', 'icono' => '🔄'],
            ['id' => 3, 'nombre' => 'Liderazgo', 'slug' => 'liderazgo', 'icono' => '👥'],
            ['id' => 4, 'nombre' => 'Finanzas', 'slug' => 'finanzas', 'icono' => '💰'],
            ['id' => 5, 'nombre' => 'Psicología', 'slug' => 'psicologia', 'icono' => '🧠'],
            ['id' => 6, 'nombre' => 'Filosofía', 'slug' => 'filosofia', 'icono' => '📜'],
            ['id' => 7, 'nombre' => 'Creatividad', 'slug' => 'creatividad', 'icono' => '🎨'],
            ['id' => 8, 'nombre' => 'Educación', 'slug' => 'educacion', 'icono' => '📚'],
        ]);
    }
}
