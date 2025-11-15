<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carrito;
use App\Models\Pedido;
use Illuminate\Support\Facades\Validator;

class CarritoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $carritos = Carrito::with('detalles', 'pedido')->get();
        
        return view('carritos.index', compact('carritos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pedidos = Pedido::all();
        
        return view('carritos.create', compact('pedidos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fecha' => 'nullable|date',
            'total_carrito' => 'required|numeric|min:0',
            'pedido_id' => 'nullable|exists:pedidos,id'
        ]);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {

            $carrito = Carrito::create($request->all());

            session()->flash('success', 'Carrito creado exitosamente');

            return redirect()->route('carritos.index');

        } catch (\Exception $e) {

            session()->flash('error', 'Error al crear el carrito: ' . $e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $carrito = Carrito::with('detalles', 'pedido')->find($id);

        if (!$carrito) {

            session()->flash('error', 'Carrito no encontrado.');
            return redirect()->route('carritos.index');
        }

        return view('carritos.show', compact('carrito'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $carrito = Carrito::find($id);

        if (!$carrito) {
            session()->flash('error', 'Carrito no encontrado.');
            return redirect()->route('carritos.index');
        }

        $pedidos = Pedido::all();

        return view('carritos.edit', compact('carrito', 'pedidos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $carrito = Carrito::find($id);

        if (!$carrito) {
            session()->flash('error', 'Carrito no encontrado.');
            return redirect()->route('carritos.index');
        }

        $validator = Validator::make($request->all(), [
            'fecha' => 'nullable|date',
            'total_carrito' => 'sometimes|numeric|min:0',
            'pedido_id' => 'nullable|exists:pedidos,id'
        ]);

        if ($validator->fails()) {
            session()->flash('error', 'Corrija los errores en el formulario.');
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {

            $carrito->update($request->all());

            session()->flash('success', 'Carrito actualizado exitosamente.');

            return redirect()->route('carritos.show', $carrito->id);

        } catch (\Exception $e) {

            session()->flash('error', 'Error al actualizar el carrito: ' . $e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $carrito = Carrito::find($id);

        if (!$carrito) {
            session()->flash('error', 'Carrito no encontrado.');
            return redirect()->route('carritos.index');
        }

        try {

            $carrito->delete();

            session()->flash('success', 'Carrito eliminado exitosamente.');

            return redirect()->route('carritos.index');

        } catch (\Exception $e) {

            session()->flash('error', 'Error al eliminar el carrito: ' . $e->getMessage());

            return redirect()->back();
        }
    }
}