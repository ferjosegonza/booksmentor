<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatEstadosSuscripcionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cat_estados_suscripcion')->insert([
            ['id' => 1, 'nombre' => 'Activo', 'permite_envios' => true],
            ['id' => 2, 'nombre' => 'Completado', 'permite_envios' => false],
            ['id' => 3, 'nombre' => 'Pausado', 'permite_envios' => false],
        ]);
    }
}
