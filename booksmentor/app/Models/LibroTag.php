<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Libro;
use App\Models\CatTag;
use Illuminate\Database\Eloquent\Model;

class LibroTag extends Model
{
    use HasFactory;
    protected $table = 'libro_tags';

    protected $fillable = [
        'libro_id',
        'tag_id'
    ];

    public function libro()
    {
        return $this->belongsTo(Libro::class, 'libro_id');
    }

    public function tag()
    {
        return $this->belongsTo(CatTag::class, 'tag_id');
    }
}