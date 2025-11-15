<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pedidos = Pedido::with('User', 'detallesPedidos', 'carrito')->get();
        
        return view('pedidos.index', compact('pedidos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $Users = User::all();
        
        return view('pedidos.create', compact('Users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fecha' => 'nullable|date',
            'total_pedido' => 'required|numeric|min:0',
            'User_id' => 'required|exists:Users,id'
        ]);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {

            $pedido = Pedido::create($request->all());

            session()->flash('success', 'Pedido creado exitosamente');

            return redirect()->route('pedidos.index');

        } catch (\Exception $e) {

            session()->flash('error', 'Error al crear el pedido: ' . $e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $pedido = Pedido::with('User', 'detallesPedidos', 'carrito')->find($id);

        if (!$pedido) {

            session()->flash('error', 'Pedido no encontrado.');
            return redirect()->route('pedidos.index');
        }

        return view('pedidos.show', compact('pedido'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $pedido = Pedido::find($id);

        if (!$pedido) {
            session()->flash('error', 'Pedido no encontrado.');
            return redirect()->route('pedidos.index');
        }

        $Users = User::all();

        return view('pedidos.edit', compact('pedido', 'Users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $pedido = Pedido::find($id);

        if (!$pedido) {
            session()->flash('error', 'Pedido no encontrado.');
            return redirect()->route('pedidos.index');
        }

        $validator = Validator::make($request->all(), [
            'fecha' => 'nullable|date',
            'total_pedido' => 'sometimes|numeric|min:0',
            'User_id' => 'sometimes|exists:Users,id'
        ]);

        if ($validator->fails()) {
            session()->flash('error', 'Corrija los errores en el formulario.');
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {

            $pedido->update($request->all());

            session()->flash('success', 'Pedido actualizado exitosamente.');

            return redirect()->route('pedidos.show', $pedido->id);

        } catch (\Exception $e) {

            session()->flash('error', 'Error al actualizar el pedido: ' . $e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pedido = Pedido::find($id);

        if (!$pedido) {
            session()->flash('error', 'Pedido no encontrado.');
            return redirect()->route('pedidos.index');
        }

        try {

            $pedido->delete();

            session()->flash('success', 'Pedido eliminado exitosamente.');

            return redirect()->route('pedidos.index');

        } catch (\Exception $e) {

            session()->flash('error', 'Error al eliminar el pedido: ' . $e->getMessage());

            return redirect()->back();
        }
    }
}