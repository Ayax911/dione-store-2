<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ImgsPrendas extends Model
{
    use HasFactory;

    protected $table = 'imgs_prendas'; 

    protected $fillable = [
        'direccion_imagen',
        'prenda_id'
    ];

    public $timestamps = false;

    /**
     * Relación con Prenda
     */
    public function prenda()
    {
        return $this->belongsTo(Prenda::class, 'prenda_id');
    }
}