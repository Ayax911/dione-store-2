<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Carrito extends Model
{
    use HasFactory;

    protected $table = 'carritos';

    protected $fillable = [
        'fecha',
        'total_carrito',
        'pedido_id'
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public $timestamps = false;

    public function detalles()
    {
        return $this->hasMany(DetalleCarrito::class, 'carrito_id');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }
}
