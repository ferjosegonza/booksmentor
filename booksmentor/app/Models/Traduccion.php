<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Ensenanza;
use App\Models\CatIdioma;
use App\Models\HistorialEnvio;
use Illuminate\Database\Eloquent\Model;

class Traduccion extends Model
{
    use HasFactory;
    protected $table = 'traducciones';

    protected $fillable = [
        'ensenanza_id',
        'idioma_id',
        'texto_traducido',
        'fecha_traduccion',
        'veces_usado',
        'ultimo_uso'
    ];

    protected $casts = [
        'fecha_traduccion' => 'date',
        'ultimo_uso' => 'date'
    ];

    public function ensenanza()
    {
        return $this->belongsTo(Ensenanza::class, 'ensenanza_id');
    }

    public function idioma()
    {
        return $this->belongsTo(CatIdioma::class, 'idioma_id');
    }

    public function historialEnvios()
    {
        return $this->hasMany(HistorialEnvio::class, 'idioma_id');
    }
}