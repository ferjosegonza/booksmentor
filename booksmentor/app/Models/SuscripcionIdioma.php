<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Suscripcion;
use App\Models\CatIdioma;
use Illuminate\Database\Eloquent\Model;

class SuscripcionIdioma extends Model
{
    use HasFactory;
    protected $table = 'suscripcion_idiomas';

    protected $fillable = [
        'suscripcion_id',
        'idioma_id'
    ];

    public function suscripcion()
    {
        return $this->belongsTo(Suscripcion::class, 'suscripcion_id');
    }

    public function idioma()
    {
        return $this->belongsTo(CatIdioma::class, 'idioma_id');
    }
}