<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;
use Illuminate\Support\Facades\Validator;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categorias = Categoria::with('prendas')->get();
        
        return view('categorias.index', compact('categorias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categorias.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipo_prenda' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {

            $categoria = Categoria::create($request->all());

            session()->flash('success', 'Categoría creada exitosamente');

            return redirect()->route('categorias.index');

        } catch (\Exception $e) {

            session()->flash('error', 'Error al crear la categoría: ' . $e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $categoria = Categoria::with('prendas')->find($id);

        if (!$categoria) {

            session()->flash('error', 'Categoría no encontrada.');
            return redirect()->route('categorias.index');
        }

        return view('categorias.show', compact('categoria'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $categoria = Categoria::find($id);

        if (!$categoria) {
            session()->flash('error', 'Categoría no encontrada.');
            return redirect()->route('categorias.index');
        }

        return view('categorias.edit', compact('categoria'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $categoria = Categoria::find($id);

        if (!$categoria) {
            session()->flash('error', 'Categoría no encontrada.');
            return redirect()->route('categorias.index');
        }

        $validator = Validator::make($request->all(), [
            'tipo_prenda' => 'sometimes|string|max:255'
        ]);

        if ($validator->fails()) {
            session()->flash('error', 'Corrija los errores en el formulario.');
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {

            $categoria->update($request->all());

            session()->flash('success', 'Categoría actualizada exitosamente.');

            return redirect()->route('categorias.show', $categoria->id);

        } catch (\Exception $e) {

            session()->flash('error', 'Error al actualizar la categoría: ' . $e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $categoria = Categoria::find($id);

        if (!$categoria) {
            session()->flash('error', 'Categoría no encontrada.');
            return redirect()->route('categorias.index');
        }

        try {

            $categoria->delete();

            session()->flash('success', 'Categoría eliminada exitosamente.');

            return redirect()->route('categorias.index');

        } catch (\Exception $e) {

            session()->flash('error', 'Error al eliminar la categoría: ' . $e->getMessage());

            return redirect()->back();
        }
    }
}