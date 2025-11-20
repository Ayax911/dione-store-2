<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pedido extends Model
{
     use HasFactory;

    protected $table = 'pedidos';

    protected $fillable = [
        'fecha',
        'total_pedido',
        'usuario_id'
    ];

    protected $casts = [

        'fecha' => 'datetime',

    ];

    

    public function usuario(){
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function detallesPedidos(){
        return $this->hasMany(DetallePedido::class, 'pedido_id');
    }

    public function carrito(){
        return $this->hasOne(Carrito::class, 'pedido_id');
    }

}
