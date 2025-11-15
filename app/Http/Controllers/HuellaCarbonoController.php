<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HuellaCarbono;
use App\Models\Prenda;
use Illuminate\Support\Facades\Validator;

class HuellaCarbonoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $huellasCarbonos = HuellaCarbono::with('prenda')->get();
        
        return view('huellascarbonos.index', compact('huellasCarbonos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $prendas = Prenda::all();
        
        return view('huellascarbonos.create', compact('prendas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'huella_nueva' => 'required|numeric|min:0',
            'huella_reusada' => 'required|numeric|min:0',
            'prenda_id' => 'required|exists:prendas,id'
        ]);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {

            $huellaCarbono = HuellaCarbono::create($request->all());

            session()->flash('success', 'Huella de carbono creada exitosamente');

            return redirect()->route('huellascarbonos.index');

        } catch (\Exception $e) {

            session()->flash('error', 'Error al crear la huella de carbono: ' . $e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $huellaCarbono = HuellaCarbono::with('prenda')->find($id);

        if (!$huellaCarbono) {

            session()->flash('error', 'Huella de carbono no encontrada.');
            return redirect()->route('huellascarbonos.index');
        }

        return view('huellascarbonos.show', compact('huellaCarbono'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $huellaCarbono = HuellaCarbono::find($id);

        if (!$huellaCarbono) {
            session()->flash('error', 'Huella de carbono no encontrada.');
            return redirect()->route('huellascarbonos.index');
        }

        $prendas = Prenda::all();

        return view('huellascarbonos.edit', compact('huellaCarbono', 'prendas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $huellaCarbono = HuellaCarbono::find($id);

        if (!$huellaCarbono) {
            session()->flash('error', 'Huella de carbono no encontrada.');
            return redirect()->route('huellascarbonos.index');
        }

        $validator = Validator::make($request->all(), [
            'huella_nueva' => 'sometimes|numeric|min:0',
            'huella_reusada' => 'sometimes|numeric|min:0',
            'prenda_id' => 'sometimes|exists:prendas,id'
        ]);

        if ($validator->fails()) {
            session()->flash('error', 'Corrija los errores en el formulario.');
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {

            $huellaCarbono->update($request->all());

            session()->flash('success', 'Huella de carbono actualizada exitosamente.');

            return redirect()->route('huellascarbonos.show', $huellaCarbono->id);

        } catch (\Exception $e) {

            session()->flash('error', 'Error al actualizar la huella de carbono: ' . $e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $huellaCarbono = HuellaCarbono::find($id);

        if (!$huellaCarbono) {
            session()->flash('error', 'Huella de carbono no encontrada.');
            return redirect()->route('huellascarbonos.index');
        }

        try {

            $huellaCarbono->delete();

            session()->flash('success', 'Huella de carbono eliminada exitosamente.');

            return redirect()->route('huellascarbonos.index');

        } catch (\Exception $e) {

            session()->flash('error', 'Error al eliminar la huella de carbono: ' . $e->getMessage());

            return redirect()->back();
        }
    }
}