<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prenda;
use App\Models\Pedido;
use App\Models\DetallePedido;
use Illuminate\Support\Facades\Auth;

class CarritoController extends Controller
{
    /**
     * Mostrar el carrito de compras
     */
    public function index()
    {
        $carrito = session()->get('carrito', []);
        $total = 0;
        
        // Calcular total
        foreach ($carrito as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }
        
        return view('carrito', compact('carrito', 'total'));
    }

    /**
     * Agregar producto al carrito (SESSION)
     */
    public function agregar($prenda_id)
    {
        $prenda = Prenda::with(['categoria', 'imgsPrendas', 'usuario'])->find($prenda_id);
        
        if (!$prenda) {
            return redirect()->back()->with('error', 'Prenda no encontrada.');
        }

        // Validar que no sea su propia prenda
        $usuarioId = Auth::id();
        if ($prenda->usuario_id === $usuarioId) {
            return redirect()->back()->with('error', 'No puedes comprar tu propia prenda.');
        }

        // Obtener carrito de la sesión
        $carrito = session()->get('carrito', []);

        // Si la prenda ya está en el carrito, incrementar cantidad
        if (isset($carrito[$prenda_id])) {
            $carrito[$prenda_id]['cantidad']++;
        } else {
            // Agregar nueva prenda al carrito
            $carrito[$prenda_id] = [
                'id' => $prenda->id,
                'titulo' => $prenda->titulo,
                'precio' => $prenda->precio,
                'cantidad' => 1,
                'imagen' => $prenda->imgsPrendas->first()->direccion_imagen ?? null,
                'categoria' => $prenda->categoria->tipo_prenda ?? 'Sin categoría',
                'talla' => $prenda->talla,
                'vendedor_id' => $prenda->usuario_id,
                'vendedor_nombre' => $prenda->usuario->name ?? 'Usuario'
            ];
        }

        // Guardar en sesión
        session()->put('carrito', $carrito);
        
        // Actualizar contador
        $this->actualizarContador();

        return redirect()->back()->with('success', '¡Prenda agregada al carrito!');
    }

    /**
     * Actualizar cantidad de un producto
     */
    public function actualizar(Request $request, $prenda_id)
    {
        $carrito = session()->get('carrito', []);
        
        if (isset($carrito[$prenda_id])) {
            $cantidad = max(1, intval($request->cantidad));
            $carrito[$prenda_id]['cantidad'] = $cantidad;
            session()->put('carrito', $carrito);
            $this->actualizarContador();
            
            return redirect()->back()->with('success', 'Cantidad actualizada.');
        }

        return redirect()->back()->with('error', 'Prenda no encontrada en el carrito.');
    }

    /**
     * Eliminar un producto del carrito
     */
    public function eliminar($prenda_id)
    {
        $carrito = session()->get('carrito', []);

        if (isset($carrito[$prenda_id])) {
            unset($carrito[$prenda_id]);
            session()->put('carrito', $carrito);
            $this->actualizarContador();
            
            return redirect()->back()->with('success', 'Prenda eliminada del carrito.');
        }

        return redirect()->back()->with('error', 'Prenda no encontrada en el carrito.');
    }

    /**
     * Vaciar todo el carrito
     */
    public function vaciar()
    {
        session()->forget('carrito');
        session()->put('carrito_count', 0);
        
        return redirect()->back()->with('success', 'Carrito vaciado.');
    }

    /**
     * Procesar la compra (Checkout)
     * Convierte el carrito de SESSION a PEDIDO en la BD
     */
    public function checkout()
    {
        $carritoSession = session()->get('carrito', []);

        if (empty($carritoSession)) {
            return redirect()->route('carrito.index')->with('error', 'El carrito está vacío.');
        }

        try {
            $usuarioId = Auth::id();
            
            // Calcular total
            $total = 0;
            foreach ($carritoSession as $item) {
                $total += $item['precio'] * $item['cantidad'];
            }

            // Crear el pedido
            $pedido = Pedido::create([
                'fecha' => now(),
                'total_pedido' => $total,
                'usuario_id' => $usuarioId
            ]);

            // Guardar los detalles del pedido
            foreach ($carritoSession as $prenda_id => $item) {
                DetallePedido::create([
                    'cantidad' => $item['cantidad'],
                    'subtotal' => $item['precio'] * $item['cantidad'],
                    'prenda_id' => $prenda_id,
                    'pedido_id' => $pedido->id
                ]);
            }

            // Vaciar carrito de la sesión
            session()->forget('carrito');
            session()->put('carrito_count', 0);

            // Redirigir a mis compras
            return redirect()->route('pedidos.misCompras')
                ->with('success', '¡Compra realizada con éxito! Total: $' . number_format($total, 0, ',', '.') . ' COP');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al procesar la compra: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar contador del carrito en sesión
     */
    private function actualizarContador()
    {
        $carrito = session()->get('carrito', []);
        $count = 0;
        
        foreach ($carrito as $item) {
            $count += $item['cantidad'];
        }
        
        session()->put('carrito_count', $count);
    }
}