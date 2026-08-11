<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Libro;
use App\Models\Traduccion;
use App\Models\SuscripcionIdioma;
use App\Models\HistorialEnvio;
use Illuminate\Database\Eloquent\Model;

class CatIdioma extends Model
{
    use HasFactory;
    protected $table = 'cat_idiomas';

    protected $fillable = [
        'nombre',
        'codigo',
        'activo'
    ];

    public function librosOriginales()
    {
        return $this->hasMany(Libro::class, 'idioma_original_id');
    }

    public function traducciones()
    {
        return $this->hasMany(Traduccion::class, 'idioma_id');
    }

    public function suscripcionIdiomas()
    {
        return $this->hasMany(SuscripcionIdioma::class, 'idioma_id');
    }

    public function historialEnvios()
    {
        return $this->hasMany(HistorialEnvio::class, 'idioma_id');
    }
}