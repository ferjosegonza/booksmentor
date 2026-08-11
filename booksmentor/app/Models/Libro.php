<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\CatIdioma;
use App\Models\CatTag;
use App\Models\Ensenanza;
use App\Models\Suscripcion;
use Illuminate\Database\Eloquent\Model;

class Libro extends Model
{
    use HasFactory;
    protected $table = 'libros';

    protected $fillable = [
        'titulo',
        'autor',
        'idioma_original_id',
        'anio_publicacion',
        'cantidad_ensenanzas',
        'fecha_procesamiento',
        'activo'
    ];

    protected $casts = [
        'fecha_procesamiento' => 'date',
        'activo' => 'boolean'
    ];

    public function idiomaOriginal()
    {
        return $this->belongsTo(CatIdioma::class, 'idioma_original_id');
    }

    public function tags()
    {
        return $this->belongsToMany(CatTag::class, 'libro_tags', 'libro_id', 'tag_id');
    }

    public function ensenanzas()
    {
        return $this->hasMany(Ensenanza::class, 'libro_id');
    }

    public function suscripciones()
    {
        return $this->hasMany(Suscripcion::class, 'libro_id');
    }

    public function getEnsenanzasEnIdioma($idiomaId)
    {
        if ($idiomaId == $this->idioma_original_id) {
            return $this->ensenanzas()->orderBy('orden')->get();
        }

        return $this->ensenanzas()
            ->whereHas('traducciones', function ($query) use ($idiomaId) {
                $query->where('idioma_id', $idiomaId);
            })
            ->with(['traducciones' => function ($query) use ($idiomaId) {
                $query->where('idioma_id', $idiomaId);
            }])
            ->orderBy('orden')
            ->get();
    }

    public function getProgresoPromedioAttribute()
    {
        return $this->suscripciones()
            ->where('estado_id', 1)
            ->avg('porcentaje_avance');
    }
}