<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetalleCarrito;
use App\Models\Prenda;
use App\Models\Carrito;
use Illuminate\Support\Facades\Validator;

class DetalleCarritoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $detallesCarritos = DetalleCarrito::with('prenda', 'carrito')->get();
        
        return view('detallescarritos.index', compact('detallesCarritos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $prendas = Prenda::all();
        $carritos = Carrito::all();
        
        return view('detallescarritos.create', compact('prendas', 'carritos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cantidad' => 'required|integer|min:1',
            'subtotal' => 'required|numeric|min:0',
            'fecha_adicion' => 'required|date',
            'prenda_id' => 'required|exists:prendas,id',
            'carrito_id' => 'required|exists:carritos,id'
        ]);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {

            $detalleCarrito = DetalleCarrito::create($request->all());

            session()->flash('success', 'Detalle de carrito creado exitosamente');

            return redirect()->route('detallescarritos.index');

        } catch (\Exception $e) {

            session()->flash('error', 'Error al crear el detalle del carrito: ' . $e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $detalleCarrito = DetalleCarrito::with('prenda', 'carrito')->find($id);

        if (!$detalleCarrito) {

            session()->flash('error', 'Detalle de carrito no encontrado.');
            return redirect()->route('detallescarritos.index');
        }

        return view('detallescarritos.show', compact('detalleCarrito'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $detalleCarrito = DetalleCarrito::find($id);

        if (!$detalleCarrito) {
            session()->flash('error', 'Detalle de carrito no encontrado.');
            return redirect()->route('detallescarritos.index');
        }

        $prendas = Prenda::all();
        $carritos = Carrito::all();

        return view('detallescarritos.edit', compact('detalleCarrito', 'prendas', 'carritos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $detalleCarrito = DetalleCarrito::find($id);

        if (!$detalleCarrito) {
            session()->flash('error', 'Detalle de carrito no encontrado.');
            return redirect()->route('detallescarritos.index');
        }

        $validator = Validator::make($request->all(), [
            'cantidad' => 'sometimes|integer|min:1',
            'subtotal' => 'sometimes|numeric|min:0',
            'fecha_adicion' => 'sometimes|date',
            'prenda_id' => 'sometimes|exists:prendas,id',
            'carrito_id' => 'sometimes|exists:carritos,id'
        ]);

        if ($validator->fails()) {
            session()->flash('error', 'Corrija los errores en el formulario.');
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {

            $detalleCarrito->update($request->all());

            session()->flash('success', 'Detalle de carrito actualizado exitosamente.');

            return redirect()->route('detallescarritos.show', $detalleCarrito->id);

        } catch (\Exception $e) {

            session()->flash('error', 'Error al actualizar el detalle del carrito: ' . $e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $detalleCarrito = DetalleCarrito::find($id);

        if (!$detalleCarrito) {
            session()->flash('error', 'Detalle de carrito no encontrado.');
            return redirect()->route('detallescarritos.index');
        }

        try {

            $detalleCarrito->delete();

            session()->flash('success', 'Detalle de carrito eliminado exitosamente.');

            return redirect()->route('detallescarritos.index');

        } catch (\Exception $e) {

            session()->flash('error', 'Error al eliminar el detalle del carrito: ' . $e->getMessage());

            return redirect()->back();
        }
    }
}