<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetallePedido;
use App\Models\Prenda;
use App\Models\Pedido;
use Illuminate\Support\Facades\Validator;

class DetallePedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $detallesPedidos = DetallePedido::with('prenda', 'pedido')->get();
        
        return view('detallespedidos.index', compact('detallesPedidos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $prendas = Prenda::all();
        $pedidos = Pedido::all();
        
        return view('detallespedidos.create', compact('prendas', 'pedidos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cantidad' => 'required|integer|min:1',
            'subtotal' => 'required|numeric|min:0',
            'prenda_id' => 'required|exists:prendas,id',
            'pedido_id' => 'required|exists:pedidos,id'
        ]);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {

            $detallePedido = DetallePedido::create($request->all());

            session()->flash('success', 'Detalle de pedido creado exitosamente');

            return redirect()->route('detallespedidos.index');

        } catch (\Exception $e) {

            session()->flash('error', 'Error al crear el detalle del pedido: ' . $e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $detallePedido = DetallePedido::with('prenda', 'pedido')->find($id);

        if (!$detallePedido) {

            session()->flash('error', 'Detalle de pedido no encontrado.');
            return redirect()->route('detallespedidos.index');
        }

        return view('detallespedidos.show', compact('detallePedido'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $detallePedido = DetallePedido::find($id);

        if (!$detallePedido) {
            session()->flash('error', 'Detalle de pedido no encontrado.');
            return redirect()->route('detallespedidos.index');
        }

        $prendas = Prenda::all();
        $pedidos = Pedido::all();

        return view('detallespedidos.edit', compact('detallePedido', 'prendas', 'pedidos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $detallePedido = DetallePedido::find($id);

        if (!$detallePedido) {
            session()->flash('error', 'Detalle de pedido no encontrado.');
            return redirect()->route('detallespedidos.index');
        }

        $validator = Validator::make($request->all(), [
            'cantidad' => 'sometimes|integer|min:1',
            'subtotal' => 'sometimes|numeric|min:0',
            'prenda_id' => 'sometimes|exists:prendas,id',
            'pedido_id' => 'sometimes|exists:pedidos,id'
        ]);

        if ($validator->fails()) {
            session()->flash('error', 'Corrija los errores en el formulario.');
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {

            $detallePedido->update($request->all());

            session()->flash('success', 'Detalle de pedido actualizado exitosamente.');

            return redirect()->route('detallespedidos.show', $detallePedido->id);

        } catch (\Exception $e) {

            session()->flash('error', 'Error al actualizar el detalle del pedido: ' . $e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $detallePedido = DetallePedido::find($id);

        if (!$detallePedido) {
            session()->flash('error', 'Detalle de pedido no encontrado.');
            return redirect()->route('detallespedidos.index');
        }

        try {

            $detallePedido->delete();

            session()->flash('success', 'Detalle de pedido eliminado exitosamente.');

            return redirect()->route('detallespedidos.index');

        } catch (\Exception $e) {

            session()->flash('error', 'Error al eliminar el detalle del pedido: ' . $e->getMessage());

            return redirect()->back();
        }
    }
}