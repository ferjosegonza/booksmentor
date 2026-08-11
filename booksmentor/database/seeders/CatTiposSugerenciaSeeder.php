<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatTiposSugerenciaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cat_tipos_sugerencia')->insert([
            ['id' => 1, 'nombre' => '📚 Sugerencia de libro', 'icono' => '📚', 'orden' => 1],
            ['id' => 2, 'nombre' => '🐛 Reporte de error', 'icono' => '🐛', 'orden' => 2],
            ['id' => 3, 'nombre' => '💡 Mejora', 'icono' => '💡', 'orden' => 3],
            ['id' => 4, 'nombre' => '❓ Pregunta', 'icono' => '❓', 'orden' => 4],
            ['id' => 5, 'nombre' => '📝 Otro', 'icono' => '📝', 'orden' => 5],
        ]);
    }
}
