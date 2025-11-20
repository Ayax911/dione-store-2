<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prenda;
use App\Models\User;
use App\Models\Categoria;
use App\Models\ImgsPrendas;  // ✅ Importar modelo correcto
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PrendaController extends Controller
{
    /**
     * HOME PAGE - Catálogo completo
     */
    public function index(Request $request)
    {
        $categorias = Categoria::all();
        
        $query = Prenda::with(['usuario', 'categoria', 'imgsPrendas', 'condicion', 'huellasCarbonos']);
        
        // Filtrar por categoría
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
     * Formulario para CREAR prenda
     */
    public function create()
    {
        $categorias = Categoria::all();
        
        // ✅ Vista en raíz de views (SIN carpeta prendas/)
        return view('create', compact('categorias'));
    }

    /**
     * GUARDAR prenda nueva
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

            // Guardar imágenes
            if ($request->hasFile('imagenes')) {
                foreach ($request->file('imagenes') as $imagen) {
                    $path = $imagen->store('prendas', 'public');
                    
                    $prenda->imgsPrendas()->create([
                        'direccion_imagen' => $path
                    ]);
                }
            }

            return redirect()->route('home')->with('success', 'Prenda publicada exitosamente');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al crear la prenda: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * MOSTRAR detalle de prenda
     */
    public function show($id)
    {
        $prenda = Prenda::with(['usuario', 'categoria', 'imgsPrendas', 'condicion', 'huellasCarbonos'])->find($id);

        if (!$prenda) {
            return redirect()->route('home')->with('error', 'Prenda no encontrada.');
        }

        // Productos similares
        $productosSimilares = Prenda::with(['imgsPrendas', 'categoria'])
            ->where('categoria_id', $prenda->categoria_id)
            ->where('id', '!=', $prenda->id)
            ->take(4)
            ->get();

        $categorias = Categoria::all();

        // ✅ Vista en raíz de views (SIN carpeta prendas/)
        return view('show', compact('prenda', 'productosSimilares', 'categorias'));
    }

    /**
     * Formulario para EDITAR prenda
     */
    public function edit($id)
    {
        $prenda = Prenda::with('imgsPrendas')->find($id);

        if (!$prenda) {
            return redirect()->route('home')->with('error', 'Prenda no encontrada.');
        }

        // Verificar permisos
        if ($prenda->usuario_id !== Auth::id()) {
            return redirect()->route('home')->with('error', 'No tienes permiso para editar esta prenda.');
        }

        $categorias = Categoria::all();

        // ✅ Vista en raíz de views (SIN carpeta prendas/)
        return view('edit', compact('prenda', 'categorias'));
    }

    /**
     * ACTUALIZAR prenda
     */
    public function update(Request $request, $id)
    {
        $prenda = Prenda::find($id);

        if (!$prenda) {
            return redirect()->route('home')->with('error', 'Prenda no encontrada.');
        }

        if ($prenda->usuario_id !== Auth::id()) {
            return redirect()->route('home')->with('error', 'No tienes permiso para editar esta prenda.');
        }

        $validator = Validator::make($request->all(), [
            'descripcion' => 'sometimes|string|max:500',
            'talla' => 'sometimes|string|max:10',
            'precio' => 'sometimes|numeric|min:0',
            'material' => 'sometimes|string|max:50',
            'titulo' => 'sometimes|string|max:50',
            'categoria_id' => 'sometimes|exists:categorias,id',
            'imagenes.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'imagenes_eliminar' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Actualizar datos
            $prenda->update($request->only([
                'descripcion', 'talla', 'precio', 'material', 'titulo', 'categoria_id'
            ]));

            // Eliminar imágenes marcadas
            if ($request->has('imagenes_eliminar') && $request->imagenes_eliminar != '') {
                $imagenesIds = explode(',', $request->imagenes_eliminar);
                foreach ($imagenesIds as $imagenId) {
                    $imagen = ImgsPrendas::find($imagenId);
                    if ($imagen && $imagen->prenda_id == $prenda->id) {
                        Storage::disk('public')->delete($imagen->direccion_imagen);
                        $imagen->delete();
                    }
                }
            }

            // Agregar nuevas imágenes
            if ($request->hasFile('imagenes')) {
                foreach ($request->file('imagenes') as $imagen) {
                    $path = $imagen->store('prendas', 'public');
                    $prenda->imgsPrendas()->create([
                        'direccion_imagen' => $path
                    ]);
                }
            }

            return redirect()->route('prendas.show', $prenda->id)->with('success', 'Prenda actualizada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * ELIMINAR prenda
     */
    public function destroy($id)
    {
        $prenda = Prenda::with('imgsPrendas')->find($id);

        if (!$prenda) {
            return redirect()->route('home')->with('error', 'Prenda no encontrada.');
        }

        if ($prenda->usuario_id !== Auth::id()) {
            return redirect()->route('home')->with('error', 'No tienes permiso para eliminar esta prenda.');
        }

        try {
            // Eliminar imágenes del storage
            foreach ($prenda->imgsPrendas as $imagen) {
                Storage::disk('public')->delete($imagen->direccion_imagen);
            }

            $prenda->delete();

            return redirect()->route('mis-publicaciones')->with('success', 'Prenda eliminada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    /**
     * MIS PUBLICACIONES
     */
    public function misPublicaciones(Request $request)
    {
        $usuarioId = Auth::id();
        $categorias = Categoria::all();
        
        $query = Prenda::with(['categoria', 'imgsPrendas', 'condicion'])
            ->where('usuario_id', $usuarioId);
        
        // Filtro por categoría
        if ($request->has('categoria') && $request->categoria != '') {
            $query->where('categoria_id', $request->categoria);
        }
        
        // Ordenamiento
        switch ($request->get('orden', 'reciente')) {
            case 'antiguo':
                $query->oldest('created_at');
                break;
            case 'precio_alto':
                $query->orderBy('precio', 'desc');
                break;
            case 'precio_bajo':
                $query->orderBy('precio', 'asc');
                break;
            default:
                $query->latest('created_at');
                break;
        }
        
        $prendas = $query->get();
        
        return view('mis-publicaciones', compact('prendas', 'categorias'));
    }
}