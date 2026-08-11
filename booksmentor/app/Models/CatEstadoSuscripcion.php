<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Suscripcion;

class CatEstadoSuscripcion extends Model
{
    use HasFactory;
    protected $table = 'cat_estados_suscripcion';

    protected $fillable = [
        'nombre',
        'permite_envios'
    ];

    public function suscripciones()
    {
        return $this->hasMany(Suscripcion::class, 'estado_id');
    }
}