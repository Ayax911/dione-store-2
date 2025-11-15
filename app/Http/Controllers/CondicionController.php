<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Condicion;
use App\Models\Prenda;
use Illuminate\Support\Facades\Validator;

class CondicionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $condiciones = Condicion::with('prenda')->get();
        
        return view('condiciones.index', compact('condiciones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $prendas = Prenda::all();
        
        return view('condiciones.create', compact('prendas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string|max:255',
            'estado' => 'required|string|max:255',
            'prenda_id' => 'required|exists:prendas,id'
        ]);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {

            $condicion = Condicion::create($request->all());

            session()->flash('success', 'Condición creada exitosamente');

            return redirect()->route('condiciones.index');

        } catch (\Exception $e) {

            session()->flash('error', 'Error al crear la condición: ' . $e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $condicion = Condicion::with('prenda')->find($id);

        if (!$condicion) {

            session()->flash('error', 'Condición no encontrada.');
            return redirect()->route('condiciones.index');
        }

        return view('condiciones.show', compact('condicion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $condicion = Condicion::find($id);

        if (!$condicion) {
            session()->flash('error', 'Condición no encontrada.');
            return redirect()->route('condiciones.index');
        }

        $prendas = Prenda::all();

        return view('condiciones.edit', compact('condicion', 'prendas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $condicion = Condicion::find($id);

        if (!$condicion) {
            session()->flash('error', 'Condición no encontrada.');
            return redirect()->route('condiciones.index');
        }

        $validator = Validator::make($request->all(), [
            'descripcion' => 'sometimes|string|max:255',
            'estado' => 'sometimes|string|max:255',
            'prenda_id' => 'sometimes|exists:prendas,id'
        ]);

        if ($validator->fails()) {
            session()->flash('error', 'Corrija los errores en el formulario.');
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {

            $condicion->update($request->all());

            session()->flash('success', 'Condición actualizada exitosamente.');

            return redirect()->route('condiciones.show', $condicion->id);

        } catch (\Exception $e) {

            session()->flash('error', 'Error al actualizar la condición: ' . $e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $condicion = Condicion::find($id);

        if (!$condicion) {
            session()->flash('error', 'Condición no encontrada.');
            return redirect()->route('condiciones.index');
        }

        try {

            $condicion->delete();

            session()->flash('success', 'Condición eliminada exitosamente.');

            return redirect()->route('condiciones.index');

        } catch (\Exception $e) {

            session()->flash('error', 'Error al eliminar la condición: ' . $e->getMessage());

            return redirect()->back();
        }
    }
}