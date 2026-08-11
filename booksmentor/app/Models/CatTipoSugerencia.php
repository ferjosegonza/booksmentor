<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SugerenciaUsuario;

class CatTipoSugerencia extends Model
{
    use HasFactory;
    protected $table = 'cat_tipos_sugerencia';

    protected $fillable = [
        'nombre',
        'icono',
        'orden'
    ];

    public function sugerenciasUsuarios()
    {
        return $this->hasMany(SugerenciaUsuario::class, 'tipo_id');
    }
}