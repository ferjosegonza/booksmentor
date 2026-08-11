<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatTag extends Model
{
    use HasFactory;
    protected $table = 'cat_tags';

    protected $fillable = [
        'nombre',
        'slug',
        'icono'
    ];

    public function libros()
    {
        return $this->belongsToMany(Libro::class, 'libro_tags', 'tag_id', 'libro_id');
    }
}