<?php

namespace App\Models;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatFrecuencia extends Model
{
    use HasFactory;
    protected $table = 'cat_frecuencias';

    protected $fillable = [
        'nombre',
        'dias_entre_envios',
        'orden'
    ];

    // Relaciones
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'frecuencia_id');
    }
}
