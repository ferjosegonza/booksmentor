<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\CatFrecuencia;
use App\Models\CatPlane;
use App\Models\Suscripcion;
use App\Models\HistorialEnvio;
use App\Models\SugerenciaUsuario;
use App\Models\Libro;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;
    protected $table = 'usuarios';

    protected $fillable = [
        'email',
        'nombre',
        'frecuencia_id',
        'plan_id',
        'hora_envio',
        'zona_horaria',
        'fecha_registro',
        'activo'
    ];

    protected $casts = [
        'fecha_registro' => 'date',
        'activo' => 'boolean'
    ];

    public function frecuencia()
    {
        return $this->belongsTo(CatFrecuencia::class, 'frecuencia_id');
    }

    public function plan()
    {
        return $this->belongsTo(CatPlane::class, 'plan_id');
    }

    public function suscripciones()
    {
        return $this->hasMany(Suscripcion::class, 'usuario_id');
    }

    public function historialEnvios()
    {
        return $this->hasMany(HistorialEnvio::class, 'usuario_id');
    }

    public function sugerencias()
    {
        return $this->hasMany(SugerenciaUsuario::class, 'usuario_id');
    }

    public function librosActivos()
    {
        return $this->belongsToMany(Libro::class, 'suscripciones', 'usuario_id', 'libro_id')
            ->where('suscripciones.estado_id', 1)
            ->withPivot('ultima_ensenanza_enviada', 'fecha_proximo_envio')
            ->withTimestamps();
    }

    public function librosCompletados()
    {
        return $this->belongsToMany(Libro::class, 'suscripciones', 'usuario_id', 'libro_id')
            ->where('suscripciones.estado_id', 2)
            ->withPivot('ultima_ensenanza_enviada', 'fecha_proximo_envio')
            ->withTimestamps();
    }
}