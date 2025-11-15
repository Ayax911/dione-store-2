<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';

    protected $fillable = [

        'tipo_prenda'
    ];

    public $timestamps = false;

    public function prendas(){
        return $this->hasMany(Prenda::class, 'categoria_id');
    }
}
