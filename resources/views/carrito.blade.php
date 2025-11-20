@extends('layouts.app')

@section('title', 'Carrito de Compras - Dione Store')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/carrito.css') }}">
@endsection

@section('content')
<div class="container" style="max-width: 1200px; margin-top: 2rem;">
    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav mb-4">
        <a href="{{ route('home') }}">
            <i class="bi bi-house-door"></i> Inicio
        </a>
        <span class="mx-2">/</span>
        <span style="color: #6c757d;">Carrito de Compras</span>
    </nav>

    <h2 style="color: var(--clr-main); font-weight: 700; margin-bottom: 2rem;">
        <i class="bi bi-cart-fill"></i> Mi Carrito
    </h2>

    @if(empty($carrito))
        <!-- Carrito Vacío -->
        <div class="carrito-vacio">
            <i class="bi bi-cart-x"></i>
            <h3>Tu carrito está vacío</h3>
            <p>¡Explora nuestro catálogo y encuentra prendas increíbles!</p>
            <a href="{{ route('home') }}" class="btn btn-primary" style="background-color: var(--clr-orange); border: none; border-radius: 2rem; padding: 1rem 2.5rem; margin-top: 1rem;">
                <i class="bi bi-shop"></i> Ir a la Tienda
            </a>
        </div>
    @else
        <!-- Productos en el Carrito -->
        <div class="row">
            <div class="col-lg-8">
                <div class="carrito-productos">
                    @foreach($carrito as $id => $item)
                    <div class="carrito-producto">
                        <div class="carrito-producto-imagen-container">
                            @if(isset($item['imagen']) && $item['imagen'])
                                <img class="carrito-producto-imagen" 
                                     src="{{ asset('storage/' . $item['imagen']) }}" 
                                     alt="{{ $item['titulo'] }}"
                                     onerror="this.src='https://via.placeholder.com/120x160?text=Sin+Imagen'">
                            @else
                                <img class="carrito-producto-imagen" 
                                     src="https://via.placeholder.com/120x160?text=Sin+Imagen" 
                                     alt="{{ $item['titulo'] }}">
                            @endif
                        </div>
                        
                        <div class="carrito-producto-info">
                            <h4 class="carrito-producto-titulo">{{ $item['titulo'] }}</h4>
                            <div class="carrito-producto-detalles">
                                <span><i class="bi bi-tag"></i> {{ $item['categoria'] ?? 'Sin categoría' }}</span>
                                <span><i class="bi bi-rulers"></i> Talla: {{ $item['talla'] }}</span>
                                <span><i class="bi bi-person"></i> {{ $item['vendedor_nombre'] ?? 'Vendedor' }}</span>
                            </div>
                            <div class="carrito-producto-precio">
                                <strong>${{ number_format($item['precio'], 0, ',', '.') }}</strong> COP
                            </div>
                        </div>

                        <div class="carrito-producto-cantidad">
                            <label>Cantidad:</label>
                            <div class="cantidad-controls">
                                <form action="{{ route('carrito.actualizar', $id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="cantidad" value="{{ max(1, $item['cantidad'] - 1) }}">
                                    <button type="submit" class="btn-cantidad" {{ $item['cantidad'] <= 1 ? 'disabled' : '' }}>
                                        <i class="bi bi-dash"></i>
                                    </button>
                                </form>
                                
                                <span class="cantidad-valor">{{ $item['cantidad'] }}</span>
                                
                                <form action="{{ route('carrito.actualizar', $id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="cantidad" value="{{ $item['cantidad'] + 1 }}">
                                    <button type="submit" class="btn-cantidad">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="carrito-producto-subtotal">
                            <small>Subtotal:</small>
                            <strong>${{ number_format($item['cantidad'] * $item['precio'], 0, ',', '.') }}</strong>
                        </div>
                        
                        <form action="{{ route('carrito.eliminar', $id) }}" method="POST" class="carrito-producto-eliminar-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-eliminar" title="Eliminar">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>

                <!-- Botón Vaciar Carrito -->
                <div class="mt-3">
                    <form action="{{ route('carrito.vaciar') }}" method="POST" id="form-vaciar">
                        @csrf
                        <button type="button" 
                                class="btn btn-outline-danger" 
                                onclick="confirmarVaciar()"
                                style="border-radius: 2rem; padding: 0.75rem 1.5rem;">
                            <i class="bi bi-trash3"></i> Vaciar Carrito
                        </button>
                    </form>
                </div>
            </div>

            <!-- Resumen del Pedido -->
            <div class="col-lg-4">
                <div class="resumen-pedido">
                    <h4><i class="bi bi-receipt"></i> Resumen del Pedido</h4>
                    
                    <div class="resumen-linea">
                        <span>Productos ({{ array_sum(array_column($carrito, 'cantidad')) }})</span>
                        <span>${{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="resumen-linea">
                        <span>Envío</span>
                        <span class="text-success"><strong>GRATIS</strong></span>
                    </div>
                    
                    <hr>
                    
                    <div class="resumen-total">
                        <span>Total:</span>
                        <span>${{ number_format($total, 0, ',', '.') }} COP</span>
                    </div>

                    <form action="{{ route('carrito.checkout') }}" method="POST" id="form-checkout">
                        @csrf
                        <button type="button" 
                                class="btn-checkout" 
                                data-total="{{ $total }}"
                                onclick="confirmarCompra(this)">
                            <i class="bi bi-check-circle-fill"></i> Finalizar Compra
                        </button>
                    </form>

                    <a href="{{ route('home') }}" class="btn-continuar">
                        <i class="bi bi-arrow-left"></i> Seguir Comprando
                    </a>

                    <!-- Info Adicional -->
                    <div class="info-adicional">
                        <div class="info-item">
                            <i class="bi bi-shield-check"></i>
                            <span>Compra 100% segura</span>
                        </div>
                        <div class="info-item">
                            <i class="bi bi-arrow-repeat"></i>
                            <span>Devolución fácil</span>
                        </div>
                        <div class="info-item">
                            <i class="bi bi-leaf"></i>
                            <span>Compra sostenible</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function confirmarVaciar() {
    Swal.fire({
        title: '¿Vaciar carrito?',
        text: 'Se eliminarán todos los productos',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0E6E63',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, vaciar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-vaciar').submit();
        }
    });
}

function confirmarCompra(button) {
    // Obtener total del data attribute
    const total = parseFloat(button.dataset.total);
    const totalFormateado = total.toLocaleString('es-CO');
    
    Swal.fire({
        title: '¿Confirmar compra?',
        html: '<strong style="font-size: 1.3rem;">Total a pagar: $' + totalFormateado + ' COP</strong><br><br><small>Se creará tu pedido y se notificará a los vendedores</small>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#7AC943',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bi bi-check-circle"></i> Sí, comprar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Procesando compra...',
                html: 'Por favor espera',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            document.getElementById('form-checkout').submit();
        }
    });
}
</script>

<!-- Mensajes de sesión -->
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: "{{ session('success') }}",
        timer: 3000,
        showConfirmButton: false
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: "{{ session('error') }}",
        confirmButtonColor: '#0E6E63'
    });
</script>
@endif
@endsection