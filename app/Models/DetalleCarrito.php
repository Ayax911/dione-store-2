<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetalleCarrito extends Model
{
    use HasFactory;

    protected $table = 'detalles_carritos';

    protected $fillable = [
        'cantidad',
        'fecha_adicion',
        'subtotal',
        'prenda_id',
        'carrito_id'
    ];

    protected $casts = [
        'fecha_adicion' => 'datetime'
    ];

    public $timestamps = false;

    public function carrito()
    {
        return $this->belongsTo(Carrito::class, 'carrito_id');
    }

    public function prenda()
    {
        return $this->belongsTo(Prenda::class, 'prenda_id');
    }
}
