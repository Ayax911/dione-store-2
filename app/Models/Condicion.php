<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Condicion extends Model
{
    use HasFactory;

    protected $table = 'condiciones';

    protected $fillable = [
        'descripcion',
        'estado',
        'prenda_id'
    ];

    public $timestamps = false;

    
    public function prenda()
    {
        return $this->belongsTo(Prenda::class, 'prenda_id');
    }
}
