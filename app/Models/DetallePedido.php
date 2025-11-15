<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetallePedido extends Model
{
    use HasFactory;

    protected $table = 'detalles_pedidos';

    protected $fillable = [
        'cantidad',
        'subtotal',
        'prenda_id',
        'pedido_id'
    ];

    public $timestamps = false;

    public function prenda()
    {
        return $this->belongsTo(Prenda::class, 'prenda_id');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }
}
