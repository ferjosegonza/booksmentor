<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatFrecuenciasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cat_frecuencias')->insert([
            ['id' => 1, 'nombre' => 'Diaria', 'dias_entre_envios' => 1, 'orden' => 1],
            ['id' => 2, 'nombre' => 'Solo laborables', 'dias_entre_envios' => 1, 'orden' => 2],
            ['id' => 3, 'nombre' => 'Semanal', 'dias_entre_envios' => 7, 'orden' => 3],
            ['id' => 4, 'nombre' => 'Mensual', 'dias_entre_envios' => 30, 'orden' => 4],
        ]);
    }
}
