@extends('layouts.app')

@section('title', 'Mis Compras - Dione Store')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
<link rel="stylesheet" href="{{ asset('css/mis-publicaciones.css') }}">
@endsection

@section('content')
<div class="container">
    <!-- Header con título y botón -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="titulo-principal">
            <i class="bi bi-bag-check"></i> Mis Compras
        </h2>
        <a href="{{ route('home') }}" class="btn btn-success" style="background-color: var(--clr-green); border: none; border-radius: 2rem; padding: 0.75rem 2rem;">
            <i class="bi bi-shop"></i> Explorar Catálogo
        </a>
    </div>

    <!-- Estadísticas -->
    @if($compras->isNotEmpty())
    <div class="stats-container">
        <div class="stat-card">
            <i class="bi bi-cart-check-fill"></i>
            <div class="stat-number">{{ $compras->count() }}</div>
            <div class="stat-label">Compras Totales</div>
        </div>

        <div class="stat-card orange">
            <i class="bi bi-box-seam"></i>
            <div class="stat-number">{{ $compras->sum(function($c) { return $c->detallesPedidos->count(); }) }}</div>
            <div class="stat-label">Productos Comprados</div>
        </div>

        <div class="stat-card green">
            <i class="bi bi-cash-stack"></i>
            <div class="stat-number">${{ number_format($compras->sum(function($c) { return $c->detallesPedidos->sum('subtotal'); }), 0, ',', '.') }}</div>
            <div class="stat-label">Total Invertido</div>
        </div>
    </div>
    @endif

    <!-- Grid de Compras -->
    @if($compras->isEmpty())
        <div class="empty-state-custom">
            <i class="bi bi-bag"></i>
            <h3 style="color: var(--clr-main); margin-bottom: 1rem;">No tienes compras aún</h3>
            <p style="color: #6c757d; margin-bottom: 2rem; font-size: 1.1rem;">
                ¡Explora nuestro catálogo y comienza a comprar prendas sostenibles!
            </p>
            <a href="{{ route('home') }}" class="btn btn-primary" style="background-color: var(--clr-orange); border: none; border-radius: 2rem; padding: 1rem 2.5rem; font-size: 1.1rem;">
                <i class="bi bi-shop"></i> Ver Catálogo
            </a>
        </div>
    @else
        <div class="pedidos-lista">
            @foreach($compras as $compra)
            <div class="producto">
                <div class="producto-detalles" style="padding: 1.5rem;">
                    <!-- Encabezado de la Compra -->
                    <div class="d-flex justify-content-between align-items-start mb-3" style="border-bottom: 1px solid #e0e0e0; padding-bottom: 1rem;">
                        <div>
                            <h4 class="producto-titulo" style="margin-bottom: 0.25rem;">
                                Compra #{{ $compra->id }}
                            </h4>
                            <small class="text-muted">
                                <i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($compra->fecha)->format('d/m/Y H:i') }}
                            </small>
                        </div>
                        <span class="badge bg-info" style="padding: 0.5rem 1rem;">
                            <i class="bi bi-circle-fill"></i> Completado
                        </span>
                    </div>

                    <!-- Productos de esta Compra -->
                    <div class="mb-3">
                        @foreach($compra->detallesPedidos as $detalle)
                        <div class="d-flex gap-2 align-items-start mb-2 p-2" style="background-color: #f8f9fa; border-radius: 0.5rem;">
                            <!-- Imagen -->
                            <div style="flex-shrink: 0;">
                                @if($detalle->prenda && $detalle->prenda->imgsPrendas->isNotEmpty())
                                    <img src="{{ asset('storage/' . $detalle->prenda->imgsPrendas->first()->direccion_imagen) }}" 
                                         alt="{{ $detalle->prenda->titulo }}"
                                         style="width: 60px; height: 80px; object-fit: cover; border-radius: 0.5rem;"
                                         onerror="this.src='https://via.placeholder.com/60x80?text=Sin+Imagen'">
                                @else
                                    <img src="https://via.placeholder.com/60x80?text=Sin+Imagen" 
                                         alt="Producto"
                                         style="width: 60px; height: 80px; object-fit: cover; border-radius: 0.5rem;">
                                @endif
                            </div>

                            <!-- Info Producto -->
                            <div class="flex-grow-1" style="min-width: 0;">
                                <h6 style="color: var(--clr-main); margin: 0 0 0.25rem 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $detalle->prenda->titulo ?? 'Producto no disponible' }}
                                </h6>
                                <small class="text-muted d-block">
                                    <i class="bi bi-tag"></i> {{ $detalle->prenda->categoria->tipo_prenda ?? 'Sin categoría' }}
                                </small>
                                @if($detalle->prenda->condicion)
                                    <span class="badge {{ $detalle->prenda->condicion->estado === 'Nuevo' ? 'badge-nuevo' : 'badge-usado' }}" style="font-size: 0.75rem; padding: 0.25rem 0.75rem;">
                                        <i class="bi bi-shield-check"></i> {{ $detalle->prenda->condicion->estado }}
                                    </span>
                                @endif
                            </div>

                            <!-- Precio y Cantidad -->
                            <div style="text-align: right; flex-shrink: 0;">
                                <small class="text-muted d-block">Cantidad: {{ $detalle->cantidad }}</small>
                                <strong style="color: var(--clr-orange);">
                                    ${{ number_format($detalle->subtotal, 0, ',', '.') }} COP
                                </strong>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Total y Acciones -->
                    <div class="d-flex justify-content-between align-items-center" style="border-top: 1px solid #e0e0e0; padding-top: 1rem;">
                        <div>
                            <small class="text-muted d-block">Total de compra:</small>
                            <h5 style="color: var(--clr-orange); margin: 0; font-weight: 700;">
                                ${{ number_format($compra->detallesPedidos->sum('subtotal'), 0, ',', '.') }} COP
                            </h5>
                        </div>
                        
                        <div class="producto-acciones">
                            <a href="{{ route('pedidos.show', $compra->id) }}" 
                               class="btn btn-primary" 
                               style="background-color: var(--clr-main);">
                                <i class="bi bi-eye"></i> Ver Detalle
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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