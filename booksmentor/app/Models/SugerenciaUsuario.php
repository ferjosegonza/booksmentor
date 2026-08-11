<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Usuario;
use App\Models\CatTipoSugerencia;
use Illuminate\Database\Eloquent\Model;

class SugerenciaUsuario extends Model
{
    use HasFactory;
    protected $table = 'sugerencias_usuarios';

    protected $fillable = [
        'usuario_id',
        'email',
        'tipo_id',
        'libro_sugerido',
        'mensaje',
        'adjunto_url',
        'leido',
        'atendido',
        'fecha_envio',
        'fecha_respuesta',
        'respuesta_admin'
    ];

    protected $casts = [
        'leido' => 'boolean',
        'atendido' => 'boolean',
        'fecha_envio' => 'datetime',
        'fecha_respuesta' => 'datetime'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function tipo()
    {
        return $this->belongsTo(CatTipoSugerencia::class, 'tipo_id');
    }

    public function scopeNoLeidas($query)
    {
        return $query->where('leido', false);
    }

    public function scopeNoAtendidas($query)
    {
        return $query->where('atendido', false);
    }

    public function scopePendientes($query)
    {
        return $query->where('leido', false)->orWhere('atendido', false);
    }

    public function scopeDeTipo($query, $tipoId)
    {
        return $query->where('tipo_id', $tipoId);
    }

    public function marcarComoLeido()
    {
        $this->update(['leido' => true]);
    }

    public function marcarComoAtendido($respuesta = null)
    {
        $data = [
            'atendido' => true,
            'fecha_respuesta' => now()
        ];

        if ($respuesta) {
            $data['respuesta_admin'] = $respuesta;
        }

        $this->update($data);
    }
}