<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Usuario;
use App\Models\Libro;
use App\Models\CatEstadoSuscripcion;
use App\Models\CatIdioma;
use App\Models\Ensenanza;
use Illuminate\Database\Eloquent\Model;

class Suscripcion extends Model
{
    use HasFactory;
    protected $table = 'suscripciones';

    protected $fillable = [
        'usuario_id',
        'libro_id',
        'estado_id',
        'ultima_ensenanza_enviada',
        'fecha_proximo_envio'
    ];

    protected $casts = [
        'fecha_proximo_envio' => 'datetime'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function libro()
    {
        return $this->belongsTo(Libro::class, 'libro_id');
    }

    public function estado()
    {
        return $this->belongsTo(CatEstadoSuscripcion::class, 'estado_id');
    }

    public function idiomas()
    {
        return $this->belongsToMany(CatIdioma::class, 'suscripcion_idiomas', 'suscripcion_id', 'idioma_id');
    }

    public function getProximaEnsenanza()
    {
        return Ensenanza::where('libro_id', $this->libro_id)
            ->where('orden', '>', $this->ultima_ensenanza_enviada)
            ->orderBy('orden')
            ->first();
    }

    public function getPorcentajeAvanceAttribute()
    {
        if ($this->libro->cantidad_ensenanzas == 0) {
            return 0;
        }

        return round(($this->ultima_ensenanza_enviada / $this->libro->cantidad_ensenanzas) * 100, 2);
    }

    public function getEstaCompletadoAttribute()
    {
        return $this->estado_id == 2 ||
            $this->ultima_ensenanza_enviada >= $this->libro->cantidad_ensenanzas;
    }

    public function marcarComoCompletado()
    {
        $this->update([
            'estado_id' => 2,
            'fecha_proximo_envio' => null
        ]);
    }

    public function calcularProximoEnvio()
    {
        $dias = $this->usuario->frecuencia->dias_entre_envios ?? 1;
        return now()->addDays($dias);
    }
}