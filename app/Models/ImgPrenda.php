<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ImgPrenda extends Model
{
     use HasFactory;

    protected $table = 'img_prendas';

    protected $fillable = [

        'direccion_imagen',
        'prenda_id'

    ];

    public $timestamps = false;

    public function prenda(){
    return $this->belongsTo(Prenda::class, 'prenda_id');
    }
}
