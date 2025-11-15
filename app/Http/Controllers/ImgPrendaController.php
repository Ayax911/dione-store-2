<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ImgPrenda;
use App\Models\Prenda;
use Illuminate\Support\Facades\Validator;

class ImgPrendaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $imgPrendas = ImgPrenda::with('prenda')->get();
        
        return view('imgprendas.index', compact('imgPrendas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $prendas = Prenda::all();
        
        return view('imgprendas.create', compact('prendas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'direccion_imagen' => 'required|string|max:255',
            'prenda_id' => 'required|exists:prendas,id'
        ]);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {

            $imgPrenda = ImgPrenda::create($request->all());

            session()->flash('success', 'Imagen de prenda creada exitosamente');

            return redirect()->route('imgprendas.index');

        } catch (\Exception $e) {

            session()->flash('error', 'Error al crear la imagen: ' . $e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $imgPrenda = ImgPrenda::with('prenda')->find($id);

        if (!$imgPrenda) {

            session()->flash('error', 'Imagen no encontrada.');
            return redirect()->route('imgprendas.index');
        }

        return view('imgprendas.show', compact('imgPrenda'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $imgPrenda = ImgPrenda::find($id);

        if (!$imgPrenda) {
            session()->flash('error', 'Imagen no encontrada.');
            return redirect()->route('imgprendas.index');
        }

        $prendas = Prenda::all();

        return view('imgprendas.edit', compact('imgPrenda', 'prendas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $imgPrenda = ImgPrenda::find($id);

        if (!$imgPrenda) {
            session()->flash('error', 'Imagen no encontrada.');
            return redirect()->route('imgprendas.index');
        }

        $validator = Validator::make($request->all(), [
            'direccion_imagen' => 'sometimes|string|max:255',
            'prenda_id' => 'sometimes|exists:prendas,id'
        ]);

        if ($validator->fails()) {
            session()->flash('error', 'Corrija los errores en el formulario.');
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {

            $imgPrenda->update($request->all());

            session()->flash('success', 'Imagen actualizada exitosamente.');

            return redirect()->route('imgprendas.show', $imgPrenda->id);

        } catch (\Exception $e) {

            session()->flash('error', 'Error al actualizar la imagen: ' . $e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $imgPrenda = ImgPrenda::find($id);

        if (!$imgPrenda) {
            session()->flash('error', 'Imagen no encontrada.');
            return redirect()->route('imgprendas.index');
        }

        try {

            $imgPrenda->delete();

            session()->flash('success', 'Imagen eliminada exitosamente.');

            return redirect()->route('imgprendas.index');

        } catch (\Exception $e) {

            session()->flash('error', 'Error al eliminar la imagen: ' . $e->getMessage());

            return redirect()->back();
        }
    }
}