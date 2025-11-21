# Documentación: PedidoController

## Resumen

`PedidoController` presenta y controla el acceso a los pedidos relacionados con el usuario:

- **Compras** (pedidos donde el usuario es comprador).
- **Ventas** (detalles de pedidos donde el usuario es vendedor).
- **Detalle** de pedido con validación de permisos (comprador o vendedor involucrado).

Ruta del archivo: `app/Http/Controllers/PedidoController.php`

## Métodos

- `misCompras()`
  - Recupera todos los `Pedido` donde `usuario_id` es el id del usuario autenticado.
  - Usa `with()` para cargar relaciones profundas: `detallesPedidos.prenda.imgsPrendas`,
    `detallesPedidos.prenda.usuario`, `detallesPedidos.prenda.categoria`, `detallesPedidos.prenda.condicion`.
  - Ordena por fecha descendente y devuelve la vista `mis-compras` con la variable `compras`.

- `misVentas(Request $request)`
  - Recupera `DetallePedido` cuya `prenda` pertenece al usuario autenticado (se usa `whereHas`).
  - Soporta ordenamiento por la query `orden`: `reciente` (por defecto), `antiguo`, `mayor_monto`, `menor_monto`.
  - Agrupa los resultados por `pedido_id` para presentar las ventas agrupadas por pedido.
  - Devuelve la vista `mis-ventas` con la variable `ventas`.

- `show($id)`
  - Busca un `Pedido` por id y carga relaciones necesarias para mostrar el detalle.
  - Si no existe, redirige con `with('error', 'Pedido no encontrado.')`.
  - Determina si el usuario autenticado es el **comprador** (`usuario_id === Auth::id()`) o
    uno de los **vendedores** (comprobando si algún `DetallePedido` contiene una prenda
    cuyo `usuario_id` es el id del usuario).
  - Si no es comprador ni vendedor, deniega acceso redirigiendo con un mensaje de error.
  - Devuelve la vista `detalle-pedido` con `pedido`, `esComprador` y `esVendedor`.

## Relaciones y eager loading

El controlador hace uso intensivo de `with()` para evitar consultas N+1 y obtener datos de
prenda, imagenes, usuario vendendor, categoría y condición en una sola consulta.

Ejemplo de relaciones cargadas:

```
Pedido::with([
  'detallesPedidos.prenda.imgsPrendas',
  'detallesPedidos.prenda.usuario',
  'detallesPedidos.prenda.categoria',
  'detallesPedidos.prenda.condicion',
  'usuario'
])->find($id);
```

## Seguridad y permisos

- `show()` aplica una comprobación de permisos sencilla: el usuario debe ser comprador del pedido
  o vendedor de al menos una de las prendas incluidas en el pedido.
- Dependiendo de la política de la aplicación, considera usar Policies de Laravel (`Gate`/`Policy`)
  para centralizar esta lógica y reusar en otros puntos.

## Rendimiento y escalabilidad

- Los métodos usan `get()` sin paginación; para listados grandes (muchas compras/ventas)
  conviene usar `paginate()` y pasar la paginación a la vista.
- `misVentas()` aplica `groupBy('pedido_id')` en el Collection resultado; si la consulta retorna
  muchos detalles, podría ser más eficiente agrupar con SQL o paginar antes del agrupamiento.

## Mejores prácticas sugeridas

- Paginación en `misCompras()` y `misVentas()` para evitar devolver colecciones muy grandes.
- Validar las entradas de `Request` en `misVentas()` si se agregan más filtros (ej. fechas,
  rangos de importe, estado del pedido).
- Usar Policies para autorización en `show()` y posiblemente en otros endpoints que cambien el pedido.
- Registrar en logs intentos denegados de acceso a `show()` para auditoría.



Archivo generado automáticamente para documentar `PedidoController`.
