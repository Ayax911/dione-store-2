# Documentación: `resources/views/home.blade.php`

## Resumen

Esta vista muestra el catálogo (página principal) de la tienda Dione Store. Incluye:

- Sección "Hero" con título y eslogan.
- Barra de búsqueda que envía `GET` a la ruta `home` usando el parámetro `buscar`.
- Filtros por categoría (botones que envían `categoria` por `GET`).
- Contenedor/GRID de prendas con imagen, título, precio, categoría y condición.
- Botón para publicar una prenda visible solo para usuarios autenticados.
- Mensajes flash (`success`, `error`) que se autoocultan con JavaScript.

## Variables esperadas (desde el controlador)

- `$prendas` (Collection): lista de objetos `Prenda`. Cada objeto idealmente carga relaciones:
  - `imgsPrendas` (collection) — para mostrar la primera imagen (si existe)
  - `categoria` — para mostrar `tipo_prenda`
  - `condicion` — para mostrar `estado` (por ejemplo 'Nuevo'/'Usado')
- `$categorias` (Collection): lista de `Categoria` usadas para renderizar los botones de filtro.

## Parámetros de petición (query string)

- `buscar`: texto para filtrar prendas por título o descripción.
- `categoria`: id de categoría para filtrar. Si no se especifica muestra "Todos".

## Rutas utilizadas

- `route('home')` — para búsqueda y filtros.
- `route('prendas.create')` — enlace para publicar (visible solo si `@auth`).
- `route('prendas.show', $prenda->id)` — enlace a detalles de cada prenda.

## Estructura importante de la vista

- Secciones Blade definidas:
  - `@section('title', 'Catálogo - Dione Store')`
  - `@section('styles')` — carga `css/home.css`.
  - `@section('content')` — contenido principal con hero, búsqueda, filtros y grid.
  - `@section('scripts')` — script de auto-hide para alertas.

## Manejo de imágenes

- Si `imgsPrendas` no está vacío se usa la primera imagen con `asset('storage/' . direccion_imagen)`.
- Si no hay imagen se utiliza un placeholder remoto (`via.placeholder.com`).

## Consideraciones para el controlador

Ejemplo mínimo de método en el controlador para devolver la vista:

```php
public function index(Request $request)
{
    $buscar = $request->input('buscar');
    $categoriaId = $request->input('categoria');

    $query = Prenda::with(['imgsPrendas', 'categoria', 'condicion']);

    if ($buscar) {
        $query->where(function($q) use ($buscar) {
            $q->where('titulo', 'like', "%{$buscar}%")
              ->orWhere('descripcion', 'like', "%{$buscar}%");
        });
    }

    if ($categoriaId) {
        $query->where('categoria_id', $categoriaId);
    }

    $prendas = $query->get();
    $categorias = Categoria::all();

    return view('home', compact('prendas', 'categorias'));
}
```

Notas:
- Si la colección es grande, usar `paginate()` en lugar de `get()` y adaptar la vista para renderizar paginación.
- Asegurarse de ejecutar `php artisan storage:link` si las imágenes están en `storage/app/public`.

## Estilo y accesibilidad

- La vista usa iconos de Bootstrap Icons (`bi`). Verificar que la plantilla base cargue los assets de iconos.
- Los botones tienen `border-radius` y estilos inline; considerar mover reglas a `css/home.css` para mantenibilidad.

## Añadidos / mejoras sugeridas

- Soportar paginación y conservar parámetros `buscar`/`categoria` en enlaces.
- Añadir atributos `alt` más descriptivos y `aria-label` en botones clave para accesibilidad.
- Extraer lógica de filtrado a un scope en el modelo `Prenda` para centralizar queries.

---

Archivo generado automáticamente para documentar `home.blade.php`.
