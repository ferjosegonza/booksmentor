<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatEstadosEnvioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cat_estados_envio')->insert([
            ['id' => 1, 'nombre' => 'Pendiente'],
            ['id' => 2, 'nombre' => 'Entregado'],
            ['id' => 3, 'nombre' => 'Rebotado'],
            ['id' => 4, 'nombre' => 'Abierto'],
            ['id' => 5, 'nombre' => 'Fallido'],
        ]);
    }
}
