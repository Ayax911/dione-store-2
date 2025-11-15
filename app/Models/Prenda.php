<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Prenda extends Model
{
     use HasFactory;

    protected $table = 'prendas';

    protected $fillable = [
        'descripcion',
        'talla',
        'precio',
        'material',
        'titulo',
        'categoria_id',
        'usuario_id'
    ];

    protected $casts = [

        
    ];

    public $timestamps = false;

    public function usuario(){
        return $this->belongsTo(User::class, 'usuario_id');
    }
    public function categoria(){
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }


    public function imgsPrendas(){
        return $this->hasMany(ImgPrenda::class, 'prenda_id');
    }
    public function condicion(){
        return $this->hasOne(Condicion::class, 'prenda_id');
    }
    public function huellasCarbonos(){
        return $this->hasMany(HuellaCarbono::class, 'prenda_id');
    }
    public function detallesCarritos(){
        return $this->hasMany(DetalleCarrito::class, 'prenda_id');
    }
    public function detallesPedidos(){
        return $this->hasMany(DetallePedido::class, 'prenda_id');
    }

    public function scopePorCondicion($query, $estado)
    {
        return $query->whereHas('condicion', function($q) use ($estado) {
            $q->where('estado', $estado);
        });
    }
}
