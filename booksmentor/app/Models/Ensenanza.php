<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Libro;
use App\Models\Traduccion;
use App\Models\HistorialEnvio;
use Illuminate\Database\Eloquent\Model;

class Ensenanza extends Model
{
    use HasFactory;
    protected $table = 'ensenanzas';

    protected $fillable = [
        'libro_id',
        'orden',
        'texto_original',
        'tema'
    ];

    public function libro()
    {
        return $this->belongsTo(Libro::class, 'libro_id');
    }

    public function traducciones()
    {
        return $this->hasMany(Traduccion::class, 'ensenanza_id');
    }

    public function historialEnvios()
    {
        return $this->hasMany(HistorialEnvio::class, 'ensenanza_id');
    }

    public function getTextoEnIdioma($idiomaId)
    {
        if ($idiomaId == $this->libro->idioma_original_id) {
            return $this->texto_original;
        }

        $traduccion = $this->traducciones()
            ->where('idioma_id', $idiomaId)
            ->first();

        return $traduccion ? $traduccion->texto_traducido : null;
    }

    public function tieneTraduccion($idiomaId)
    {
        return $this->traducciones()
            ->where('idioma_id', $idiomaId)
            ->exists();
    }

    public function incrementarUsoTraduccion($idiomaId)
    {
        $traduccion = $this->traducciones()
            ->where('idioma_id', $idiomaId)
            ->first();

        if ($traduccion) {
            $traduccion->increment('veces_usado');
            $traduccion->update(['ultimo_uso' => now()]);
        }
    }
}