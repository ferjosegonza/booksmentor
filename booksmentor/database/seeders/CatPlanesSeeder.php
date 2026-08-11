<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatPlanesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cat_planes')->insert([
            ['id' => 1, 'nombre' => 'Gratuito', 'max_libros' => 1, 'max_idiomas' => 1, 'permite_audio' => false, 'precio_mensual' => 0.00, 'orden' => 1],
            ['id' => 2, 'nombre' => 'Básico', 'max_libros' => 5, 'max_idiomas' => 2, 'permite_audio' => false, 'precio_mensual' => 3.00, 'orden' => 2],
            ['id' => 3, 'nombre' => 'Pro', 'max_libros' => 30, 'max_idiomas' => 3, 'permite_audio' => true, 'precio_mensual' => 7.00, 'orden' => 3],
            ['id' => 4, 'nombre' => 'Premium', 'max_libros' => 999, 'max_idiomas' => 5, 'permite_audio' => true, 'precio_mensual' => 12.00, 'orden' => 4],
        ]);
    }
}
