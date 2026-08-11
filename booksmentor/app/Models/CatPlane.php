<?php

namespace App\Models;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatPlane extends Model
{
    protected $table = 'cat_planes';

    protected $fillable = [
        'nombre',
        'max_libros',
        'max_idiomas',
        'permite_audio',
        'precio_mensual',
        'orden'
    ];

    // Relaciones
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'plan_id');
    }
}
