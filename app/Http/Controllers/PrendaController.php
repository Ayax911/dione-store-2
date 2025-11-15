<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prenda;
use App\Models\User;
use App\Models\Categoria;
use Illuminate\Support\Facades\Validator;

class PrendaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $prendas = Prenda::with('User', 'categoria', 'imgsPrendas', 'condicion', 'huellasCarbonos')->get();
        
        return view('prendas.index', compact('prendas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $Users = User::all();
        $categorias = Categoria::all();
        
        return view('prendas.create', compact('Users', 'categorias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string|max:500',
            'talla' => 'required|string|max:10',
            'precio' => 'required|numeric|min:0',
            'material' => 'required|string|max:50',
            'titulo' => 'required|string|max:50',
            'categoria_id' => 'required|exists:categorias,id',
            'User_id' => 'required|exists:Users,id'
        ]);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {

            $prenda = Prenda::create($request->all());

            session()->flash('success', 'Prenda creada exitosamente');

            return redirect()->route('prendas.index');

        } catch (\Exception $e) {

            session()->flash('error', 'Error al crear la prenda: ' . $e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $prenda = Prenda::with('User', 'categoria', 'imgsPrendas', 'condicion', 'huellasCarbonos')->find($id);

        if (!$prenda) {

            session()->flash('error', 'Prenda no encontrada.');
            return redirect()->route('prendas.index');
        }

        return view('prendas.show', compact('prenda'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $prenda = Prenda::find($id);

        if (!$prenda) {
            session()->flash('error', 'Prenda no encontrada.');
            return redirect()->route('prendas.index');
        }

        $Users = User::all();
        $categorias = Categoria::all();

        return view('prendas.edit', compact('prenda', 'Users', 'categorias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $prenda = Prenda::find($id);

        if (!$prenda) {
            session()->flash('error', 'Prenda no encontrada.');
            return redirect()->route('prendas.index');
        }

        $validator = Validator::make($request->all(), [
            'descripcion' => 'sometimes|string|max:500',
            'talla' => 'sometimes|string|max:10',
            'precio' => 'sometimes|numeric|min:0',
            'material' => 'sometimes|string|max:50',
            'titulo' => 'sometimes|string|max:50',
            'categoria_id' => 'sometimes|exists:categorias,id',
            'User_id' => 'sometimes|exists:Users,id'
        ]);

        if ($validator->fails()) {
            session()->flash('error', 'Corrija los errores en el formulario.');
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {

            $prenda->update($request->all());

            session()->flash('success', 'Prenda actualizada exitosamente.');

            return redirect()->route('prendas.show', $prenda->id);

        } catch (\Exception $e) {

            session()->flash('error', 'Error al actualizar la prenda: ' . $e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $prenda = Prenda::find($id);

        if (!$prenda) {
            session()->flash('error', 'Prenda no encontrada.');
            return redirect()->route('prendas.index');
        }

        try {

            $prenda->delete();

            session()->flash('success', 'Prenda eliminada exitosamente.');

            return redirect()->route('prendas.index');

        } catch (\Exception $e) {

            session()->flash('error', 'Error al eliminar la prenda: ' . $e->getMessage());

            return redirect()->back();
        }
    }
}