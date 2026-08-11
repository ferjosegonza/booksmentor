<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HistorialEnvio;

class CatEstadoEnvio extends Model
{
    use HasFactory;
    protected $table = 'cat_estados_envio';

    protected $fillable = [
        'nombre'
    ];

    public function historialEnvios()
    {
        return $this->hasMany(HistorialEnvio::class, 'estado_id');
    }
}