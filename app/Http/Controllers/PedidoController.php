<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\DetallePedido;
use Illuminate\Support\Facades\Auth;

class PedidoController extends Controller
{
    /**
     * Mostrar historial de compras del usuario
     */
    public function misCompras()
    {
        $usuarioId = Auth::id();
        
        // Pedidos donde el usuario ES EL COMPRADOR
        $compras = Pedido::with([
                'detallesPedidos.prenda.imgsPrendas', 
                'detallesPedidos.prenda.usuario',
                'detallesPedidos.prenda.categoria',
                'detallesPedidos.prenda.condicion'
            ])
            ->where('usuario_id', $usuarioId)
            ->orderBy('fecha', 'desc')
            ->get();
        
        return view('mis-compras', compact('compras'));
    }

    /**
     * Mostrar historial de ventas del usuario (con filtros)
     */
    public function misVentas(Request $request)
    {
        $usuarioId = Auth::id();
        
        // Query base - Pedidos donde el usuario ES EL VENDEDOR
        $query = DetallePedido::with([
                'pedido.usuario', 
                'prenda.imgsPrendas', 
                'prenda.categoria',
                'prenda.condicion'
            ])
            ->whereHas('prenda', function($q) use ($usuarioId) {
                $q->where('usuario_id', $usuarioId);
            });
        
        // Ordenamiento
        switch ($request->get('orden', 'reciente')) {
            case 'antiguo':
                $query->oldest('created_at');
                break;
            case 'mayor_monto':
                $query->orderBy('subtotal', 'desc');
                break;
            case 'menor_monto':
                $query->orderBy('subtotal', 'asc');
                break;
            default: // 'reciente'
                $query->latest('created_at');
                break;
        }
        
        $ventas = $query->get()->groupBy('pedido_id');
        
        return view('mis-ventas', compact('ventas'));
    }

    /**
     * Ver detalle de un pedido específico
     */
    public function show($id)
    {
        $pedido = Pedido::with([
                'detallesPedidos.prenda.imgsPrendas', 
                'detallesPedidos.prenda.usuario',
                'detallesPedidos.prenda.categoria',
                'detallesPedidos.prenda.condicion',
                'usuario'
            ])
            ->find($id);

        if (!$pedido) {
            return redirect()->route('home')->with('error', 'Pedido no encontrado.');
        }

        $usuarioId = Auth::id();
        
        // Verificar que el usuario sea el comprador o uno de los vendedores
        $esComprador = $pedido->usuario_id === $usuarioId;
        $esVendedor = $pedido->detallesPedidos->contains(function($detalle) use ($usuarioId) {
            return $detalle->prenda && $detalle->prenda->usuario_id === $usuarioId;
        });

        if (!$esComprador && !$esVendedor) {
            return redirect()->route('home')->with('error', 'No tienes permiso para ver este pedido.');
        }

        return view('detalle-pedido', compact('pedido', 'esComprador', 'esVendedor'));
    }
}