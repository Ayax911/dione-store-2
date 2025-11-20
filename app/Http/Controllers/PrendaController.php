<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prenda;
use App\Models\User;
use App\Models\Categoria;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PrendaController extends Controller
{
    /**
     * Display a listing of the resource (HOME PAGE)
     */
    public function index(Request $request)
    {
        $categorias = Categoria::all();
        
        // Query base
        $query = Prenda::with(['usuario', 'categoria', 'imgsPrendas', 'condicion', 'huellasCarbonos']);
        
        // Filtrar por categoría si se proporciona
        if ($request->has('categoria') && $request->categoria != '') {
            $query->where('categoria_id', $request->categoria);
        }
        
        // Búsqueda por texto
        if ($request->has('buscar') && $request->buscar != '') {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('titulo', 'LIKE', "%{$buscar}%")
                  ->orWhere('descripcion', 'LIKE', "%{$buscar}%");
            });
        }
        
        $prendas = $query->latest('id')->get();
        
        return view('home', compact('prendas', 'categorias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categorias = Categoria::all();
        
        return view('prendas.create', compact('categorias'));
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
            'imagenes.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Obtener el ID del usuario autenticado
            $usuarioId = Auth::id();

            // Crear la prenda
            $prenda = Prenda::create([
                'descripcion' => $request->descripcion,
                'talla' => $request->talla,
                'precio' => $request->precio,
                'material' => $request->material,
                'titulo' => $request->titulo,
                'categoria_id' => $request->categoria_id,
                'usuario_id' => $usuarioId
            ]);

            // Guardar imágenes si existen
            if ($request->hasFile('imagenes')) {
                foreach ($request->file('imagenes') as $imagen) {
                    $path = $imagen->store('prendas', 'public');
                    
                    $prenda->imgsPrendas()->create([
                        'direccion_imagen' => $path
                    ]);
                }
            }

            session()->flash('success', 'Prenda publicada exitosamente');
            return redirect()->route('home');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear la prenda: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $prenda = Prenda::with(['usuario', 'categoria', 'imgsPrendas', 'condicion', 'huellasCarbonos'])->find($id);

        if (!$prenda) {
            session()->flash('error', 'Prenda no encontrada.');
            return redirect()->route('home');
        }

        // Productos similares (misma categoría, máximo 4)
        $productosSimilares = Prenda::with(['imgsPrendas', 'categoria'])
            ->where('categoria_id', $prenda->categoria_id)
            ->where('id', '!=', $prenda->id)
            ->take(4)
            ->get();

        $categorias = Categoria::all();

        return view('prendas.show', compact('prenda', 'productosSimilares', 'categorias'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $prenda = Prenda::with('imgsPrendas')->find($id);

        if (!$prenda) {
            session()->flash('error', 'Prenda no encontrada.');
            return redirect()->route('home');
        }

        // Verificar que el usuario sea el dueño - CORRECCIÓN AQUÍ
        $usuarioId = Auth::id();
        if ($prenda->usuario_id !== $usuarioId) {
            session()->flash('error', 'No tienes permiso para editar esta prenda.');
            return redirect()->route('home');
        }

        $categorias = Categoria::all();

        return view('prendas.edit', compact('prenda', 'categorias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $prenda = Prenda::find($id);

        if (!$prenda) {
            session()->flash('error', 'Prenda no encontrada.');
            return redirect()->route('home');
        }

        // Verificar que el usuario sea el dueño - CORRECCIÓN AQUÍ
        $usuarioId = Auth::id();
        if ($prenda->usuario_id !== $usuarioId) {
            session()->flash('error', 'No tienes permiso para editar esta prenda.');
            return redirect()->route('home');
        }

        $validator = Validator::make($request->all(), [
            'descripcion' => 'sometimes|string|max:500',
            'talla' => 'sometimes|string|max:10',
            'precio' => 'sometimes|numeric|min:0',
            'material' => 'sometimes|string|max:50',
            'titulo' => 'sometimes|string|max:50',
            'categoria_id' => 'sometimes|exists:categorias,id',
            'imagenes.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $prenda->update($request->only([
                'descripcion', 'talla', 'precio', 'material', 'titulo', 'categoria_id'
            ]));

            // Agregar nuevas imágenes si existen
            if ($request->hasFile('imagenes')) {
                foreach ($request->file('imagenes') as $imagen) {
                    $path = $imagen->store('prendas', 'public');
                    
                    $prenda->imgsPrendas()->create([
                        'direccion_imagen' => $path
                    ]);
                }
            }

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
        $prenda = Prenda::with('imgsPrendas')->find($id);

        if (!$prenda) {
            session()->flash('error', 'Prenda no encontrada.');
            return redirect()->route('home');
        }

        // Verificar que el usuario sea el dueño - CORRECCIÓN AQUÍ
        $usuarioId = Auth::id();
        if ($prenda->usuario_id !== $usuarioId) {
            session()->flash('error', 'No tienes permiso para eliminar esta prenda.');
            return redirect()->route('home');
        }

        try {
            // Eliminar imágenes del storage
            foreach ($prenda->imgsPrendas as $imagen) {
                Storage::disk('public')->delete($imagen->direccion_imagen);
            }

            $prenda->delete();

            session()->flash('success', 'Prenda eliminada exitosamente.');
            return redirect()->route('home');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar la prenda: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function misPublicaciones(Request $request)
    {
        $usuarioId = Auth::id();
        $categorias = Categoria::all();
        
        $query = Prenda::with(['categoria', 'imgsPrendas', 'condicion'])
            ->where('usuario_id', $usuarioId);
        
        
        
        $prendas = $query->get();
        
        return view('prendas.mis-publicaciones', compact('prendas', 'categorias'));
    }
}