<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatIdiomasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cat_idiomas')->insert([
            ['id' => 1, 'nombre' => 'Español', 'codigo' => 'es', 'activo' => true],
            ['id' => 2, 'nombre' => 'Inglés', 'codigo' => 'en', 'activo' => true],
            ['id' => 3, 'nombre' => 'Portugués', 'codigo' => 'pt', 'activo' => true],
            ['id' => 4, 'nombre' => 'Italiano', 'codigo' => 'it', 'activo' => true],
            ['id' => 5, 'nombre' => 'Francés', 'codigo' => 'fr', 'activo' => true],
        ]);
    }
}
