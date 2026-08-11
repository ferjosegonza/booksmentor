<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Usuario;
use App\Models\Ensenanza;
use App\Models\CatIdioma;
use App\Models\CatEstadoEnvio;
use Illuminate\Database\Eloquent\Model;

class HistorialEnvio extends Model
{
    use HasFactory;
    protected $table = 'historial_envios';

    protected $fillable = [
        'usuario_id',
        'ensenanza_id',
        'idioma_id',
        'estado_id',
        'fecha_envio'
    ];

    protected $casts = [
        'fecha_envio' => 'datetime'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function ensenanza()
    {
        return $this->belongsTo(Ensenanza::class, 'ensenanza_id');
    }

    public function idioma()
    {
        return $this->belongsTo(CatIdioma::class, 'idioma_id');
    }

    public function estado()
    {
        return $this->belongsTo(CatEstadoEnvio::class, 'estado_id');
    }

    public function scopeEntregados($query)
    {
        return $query->where('estado_id', 2);
    }

    public function scopeFallidos($query)
    {
        return $query->where('estado_id', 5);
    }

    public function scopeDeUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }
}