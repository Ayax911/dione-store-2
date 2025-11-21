<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prenda;
use App\Models\User;
use App\Models\Categoria;
use App\Models\ImgsPrendas;
use App\Models\HuellaCarbono;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PrendaController extends Controller
{
    /**
     * Datos base de CO2 por categoría
     */
    private const CO2_POR_CATEGORIA = [
        'Camisetas' => ['fabricacion' => 7.0],
        'Blusas' => ['fabricacion' => 6.5],
        'Pantalones' => ['fabricacion' => 33.4],
        'Jeans' => ['fabricacion' => 33.4],
        'Vestidos' => ['fabricacion' => 47.0],
        'Chaquetas' => ['fabricacion' => 50.0],
        'Abrigos' => ['fabricacion' => 65.0],
        'Zapatos' => ['fabricacion' => 14.0],
        'Zapatillas' => ['fabricacion' => 13.6],
        'Botas' => ['fabricacion' => 18.0],
        'Faldas' => ['fabricacion' => 15.0],
        'Ropa Deportiva' => ['fabricacion' => 9.0],
        'Accesorios' => ['fabricacion' => 3.0],
        'Bolsos' => ['fabricacion' => 12.0],
        'Ropa Interior' => ['fabricacion' => 2.5],
    ];

    /**
     * Factores por material
     */
    private const FACTOR_MATERIAL = [
        'Algodón orgánico' => 0.7,
        'Lino' => 0.8,
        'Algodón' => 1.0,
        'Poliéster' => 1.3,
        'Nylon' => 1.4,
        'Lana' => 1.5,
        'Seda' => 1.2,
        'Mezclilla' => 1.1,
        'Cuero' => 2.0,
        'Sintético' => 1.3,
    ];

    /**
     * HOME PAGE - Catálogo completo
     */
    public function index(Request $request)
    {
        $categorias = Categoria::all();
        
        $query = Prenda::with(['usuario', 'categoria', 'imgsPrendas', 'condicion', 'huellasCarbonos']);
        
        if ($request->has('categoria') && $request->categoria != '') {
            $query->where('categoria_id', $request->categoria);
        }
        
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
        return view('create', compact('categorias'));
    }

    /**
     * GUARDAR prenda nueva + CALCULAR HUELLA
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

            // CALCULAR HUELLA DE CARBONO
            $huella = $this->calcularHuellaCarbono($prenda);

            return redirect()->route('home')->with('success', 
                'Prenda publicada exitosamente. Impacto ambiental: ' . number_format($huella->co2_ahorrado, 1) . ' kg CO2 ahorrados'
            );

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

        // Si no tiene huella, calcularla
        if ($prenda->huellasCarbonos->isEmpty()) {
            $this->calcularHuellaCarbono($prenda);
            $prenda->load('huellasCarbonos');
        }

        $huella = $prenda->huellasCarbonos->first();

        // Productos similares
        $productosSimilares = Prenda::with(['imgsPrendas', 'categoria'])
            ->where('categoria_id', $prenda->categoria_id)
            ->where('id', '!=', $prenda->id)
            ->take(4)
            ->get();

        if ($prenda->huellasCarbonos->isEmpty()) {
            $this->calcularHuellaCarbono($prenda);
            $prenda->load('huellasCarbonos');
        }

        $huella = $prenda->huellasCarbonos->first();

        $categorias = Categoria::all();

        return view('show', compact('prenda', 'productosSimilares', 'categorias', 'huella'));
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

        if ($prenda->usuario_id !== Auth::id()) {
            return redirect()->route('home')->with('error', 'No tienes permiso para editar esta prenda.');
        }

        $categorias = Categoria::all();
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

            // Recalcular huella si cambió material o categoría
            if ($request->has('material') || $request->has('categoria_id')) {
                $this->calcularHuellaCarbono($prenda);
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
        
        $query = Prenda::with(['categoria', 'imgsPrendas', 'condicion', 'huellasCarbonos'])
            ->where('usuario_id', $usuarioId);
        
        if ($request->has('categoria') && $request->categoria != '') {
            $query->where('categoria_id', $request->categoria);
        }
        
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
        
        // Calcular impacto personal
        $impactoPersonal = $this->calcularImpactoUsuario($usuarioId);
        
        return view('mis-publicaciones', compact('prendas', 'categorias', 'impactoPersonal'));
    }

    /**
     * MÉTODO PRIVADO: Calcular huella de carbono
     */
    private function calcularHuellaCarbono(Prenda $prenda)
    {
        // Obtener categoría
        $categoria = $prenda->categoria->tipo_prenda ?? 'default';
        $co2Base = self::CO2_POR_CATEGORIA[$categoria]['fabricacion'] ?? 15.0;

        // Factor de material
        $material = ucfirst(strtolower(trim($prenda->material)));
        $factorMaterial = self::FACTOR_MATERIAL[$material] ?? 1.0;
        
        // Buscar coincidencia parcial si no hay exacta
        if ($factorMaterial === 1.0) {
            foreach (self::FACTOR_MATERIAL as $key => $factor) {
                if (stripos($material, $key) !== false) {
                    $factorMaterial = $factor;
                    break;
                }
            }
        }

        // 1. CO2 de fabricación
        $co2Fabricacion = $co2Base * $factorMaterial;

        // 2. Transporte (20% de fabricación)
        $co2Transporte = $co2Fabricacion * 0.2;

        // 3. Total prenda nueva
        $co2TotalNuevo = $co2Fabricacion + $co2Transporte;

        // 4. Segunda mano (15% del total)
        $co2SegundaMano = $co2TotalNuevo * 0.15;

        // 5. CO2 ahorrado
        $co2Ahorrado = $co2TotalNuevo - $co2SegundaMano;

        // 6. Porcentaje
        $porcentajeAhorro = ($co2Ahorrado / $co2TotalNuevo) * 100;

        // Crear o actualizar
        return HuellaCarbono::updateOrCreate(
            ['prenda_id' => $prenda->id],
            [
                'co2_fabricacion' => round($co2Fabricacion, 2),
                'co2_total_nueva' => round($co2TotalNuevo, 2),
                'co2_segunda_mano' => round($co2SegundaMano, 2),
                'co2_ahorrado' => round($co2Ahorrado, 2),
                'porcentaje_ahorro' => round($porcentajeAhorro, 2),
                'categoria_calculo' => $categoria
            ]
        );
    }

    /**
     * MÉTODO PRIVADO: Calcular impacto del usuario
     */
    private function calcularImpactoUsuario($usuarioId)
    {
        $prendas = Prenda::where('usuario_id', $usuarioId)
            ->with('huellasCarbonos')
            ->get();

        $totalCO2 = $prendas->sum(function($prenda) {
            return optional($prenda->huellasCarbonos->first())->co2_ahorrado ?? 0;
        });

        $totalPrendas = $prendas->count();

        return [
            'total_co2_ahorrado' => round($totalCO2, 2),
            'total_prendas' => $totalPrendas,
            'promedio_por_prenda' => $totalPrendas > 0 ? round($totalCO2 / $totalPrendas, 2) : 0
        ];


        


        
    }


    
}