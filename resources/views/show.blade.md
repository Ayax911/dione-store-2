# Documentación: `resources/views/show.blade.php`

## Resumen

Vista de detalle de una prenda en Dione Store. Presenta:

- Galería de imágenes (imagen principal y thumbnails si existen).
- Información detallada: título, precio, condición, talla, categoría, material y descripción.
- Información del vendedor.
- Indicador de huella de carbono con comparación nueva vs reusada.
- Acciones condicionadas por autenticación (agregar al carrito, editar/eliminar si es propietario).
- Sección de productos similares (opcional).

## Variables esperadas (desde el controlador)

- `$prenda` (Prenda): modelo con relaciones recomendadas:
  - `imgsPrendas` (Collection) — para la galería
  - `categoria` — para mostrar `tipo_prenda`
  - `condicion` — para mostrar `estado` (e.g., 'Nuevo'/'Usado')
  - `usuario` — información del vendedor
  - `huellasCarbonos` (Collection) — para mostrar impacto ambiental
- `$productosSimilares` (Collection, opcional): lista de prendas a mostrar en la sección "También te puede interesar".

## Mensajes flash y autenticación

- `session('success')` — muestra un mensaje de éxito y se autooculta.
- `session('info')` — actualmente se muestra con `alert()` de JS.
- La vista usa directivas `@auth` y `Auth::id()` para mostrar botones de acción según el usuario.

## Rutas utilizadas

- `route('home')` — breadcrumb a inicio / categoría.
- `route('prendas.show', $prenda->id)` — enlaces a otros productos.
- `route('prendas.edit', $prenda->id)` — editar publicación (propietario).
- `route('prendas.destroy', $prenda->id)` — eliminar publicación (propietario).
- `route('carrito.agregar', $prenda->id)` — acción para agregar al carrito.
- `route('login')` — enlace para iniciar sesión.

## Cálculo de huella de carbono

La vista usa las propiedades `huella_nueva` y `huella_reusada` del primer registro en
`$prenda->huellasCarbonos` y calcula la reducción porcentual como:

```
reduccion = ((huella_nueva - huella_reusada) / huella_nueva) * 100
```

Validar en el controlador que los valores no sean nulos antes de mostrar para evitar
división por cero.

## Ejemplo de método en el controlador

```php
public function show(Prenda $prenda)
{
    // Cargar relaciones necesarias
    $prenda->load(['imgsPrendas', 'categoria', 'condicion', 'usuario', 'huellasCarbonos']);

    // Ejemplo simple para productos similares (por categoría)
    $productosSimilares = Prenda::where('categoria_id', $prenda->categoria_id)
        ->where('id', '!=', $prenda->id)
        ->with('imgsPrendas')
        ->limit(6)
        ->get();

    return view('show', compact('prenda', 'productosSimilares'));
}
```

## Recomendaciones y mejoras

- Evitar usar `alert()` para `session('info')`; usar un componente de alerta consistente.
- Añadir comprobaciones nulas para valores de huella y formatos numéricos.
- Mover estilos inline a `css/show.css` y consolidar clases para accesibilidad.
- Añadir `loading="lazy"` a imágenes secundarias y `srcset`/`sizes` si se requiere optimización.

---

Archivo generado automáticamente para documentar `show.blade.php`.
