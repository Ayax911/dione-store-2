# Documentación: CarritoController

## Resumen

`CarritoController` maneja el carrito de compras de la aplicación. El carrito se guarda en la
sesión del usuario y el controlador ofrece métodos para ver, agregar, actualizar, eliminar,
vaciar y finalizar (checkout) el carrito transformándolo en un `Pedido` con `DetallePedido`.

Ruta del archivo: `app/Http/Controllers/CarritoController.php`

## Métodos públicos

- `index()`
  - Muestra la vista del carrito con el contenido actual (`session('carrito')`) y el total.

- `agregar($prenda_id)`
  - Busca la prenda y la agrega al carrito en sesión.
  - Si la prenda ya existe en el carrito, incrementa la cantidad.
  - No permite agregar la propia prenda del vendedor (comprueba `Auth::id()`).
  - Actualiza `carrito_count` y redirige con `session()->flash('success', ...)`.

- `actualizar(Request $request, $prenda_id)`
  - Actualiza la cantidad de un ítem (mínimo 1) en la sesión.
  - Recalcula el `carrito_count`.

- `eliminar($prenda_id)`
  - Elimina un ítem del carrito y actualiza `carrito_count`.

- `vaciar()`
  - Elimina completamente `carrito` de la sesión y pone `carrito_count` en 0.

- `checkout()`
  - Valida que el carrito no esté vacío.
  - Calcula el total y crea un `Pedido` y registros `DetallePedido` para cada ítem.
  - Luego borra la sesión del carrito y redirige con un mensaje de éxito.

## Uso de `session` (detallado)

El carrito se representa como un array asociativo en la clave de sesión `carrito`. Estructura
por clave `prenda_id`:

```
carrito = [
    prenda_id => [
        'id' => int,
        'titulo' => string,
        'precio' => float,
        'cantidad' => int,
        'imagen' => string|null,
        'categoria' => string,
        'talla' => string,
        'vendedor_id' => int,
        'vendedor_nombre' => string,
    ],
    ...
]
```

Operaciones comunes con sesión usadas en la clase:

- `session()->get('carrito', [])` — obtener carrito actual; devuelve `[]` si no existe.
- `session()->put('carrito', $carrito)` — escribir carrito actualizado en sesión.
- `session()->put('carrito_count', $count)` — actualizar contador de unidades totales.
- `session()->forget('carrito')` — eliminar el carrito de la sesión completamente.

Ejemplo de agregar y actualizar contador (simplificado):

```php
$carrito = session()->get('carrito', []);

if (isset($carrito[$prenda_id])) {
    $carrito[$prenda_id]['cantidad']++;
} else {
    $carrito[$prenda_id] = [...];
}

session()->put('carrito', $carrito);

// actualizar contador
$count = array_sum(array_column($carrito, 'cantidad'));
session()->put('carrito_count', $count);
```

### Notas sobre persistencia y escalabilidad

- Actualmente el carrito se guarda en la sesión del servidor (o en el driver configurado
  para sesiones). Para usuarios anónimos esto funciona bien, pero tiene limitaciones:
  - Si se usan múltiples servidores sin un store de sesión compartido, la sesión puede
    perderse entre peticiones.
  - No permite compartir carrito entre dispositivos o recuperar historial si la sesión expira.

- Para mejorar:
  - Persistir el carrito en la base de datos para usuarios autenticados (tabla `carritos` / `carrito_items`).
  - Sincronizar la sesión con la base de datos al iniciar sesión.
  - Agregar validaciones en `checkout()` para comprobar disponibilidad y precios actuales.

## Manejo de errores

- `agregar()` y otros métodos devuelven mensajes flash (`with('success')` / `with('error')`) para
  mostrar en la interfaz.
- `checkout()` captura excepciones y devuelve `with('error', $e->getMessage())`. En producción
  conviene registrar el error en logs y devolver un mensaje genérico al usuario.

## Recomendaciones de seguridad y UX

- Validar que `precio` y `cantidad` sean valores válidos antes de crear el pedido.
- Comprobar que el usuario que realiza `checkout()` sea el mismo que creó el carrito o que tenga
  permiso para comprar los ítems.
- Evitar mostrar información sensible del vendedor en la sesión si no es necesaria.

---

Archivo generado automáticamente para documentar `CarritoController`.
