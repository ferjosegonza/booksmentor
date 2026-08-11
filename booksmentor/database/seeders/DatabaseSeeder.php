<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CatFrecuenciasSeeder::class,
            CatPlanesSeeder::class,
            CatIdiomasSeeder::class,
            CatTagsSeeder::class,
            CatEstadosSuscripcionSeeder::class,
            CatEstadosEnvioSeeder::class,
            CatTiposSugerenciaSeeder::class,
        ]);
    }
}
