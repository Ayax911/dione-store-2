@extends('layouts.app')

@section('title', 'Mis Ventas - Dione Store')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/pedidos.css') }}">
@endsection

@section('content')
<div class="container" style="max-width: 1200px; margin-top: 2rem;">
    <nav class="breadcrumb-nav mb-4">
        <a href="{{ route('home') }}">
            <i class="bi bi-house-door"></i> Inicio
        </a>
        <span class="mx-2">/</span>
        <span style="color: #6c757d;">Mis Ventas</span>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h2 style="color: var(--clr-main); font-weight: 700; margin: 0;">
            <i class="bi bi-shop"></i> Mis Ventas
        </h2>
        <a href="{{ route('pedidos.misCompras') }}" class="btn btn-outline-primary" style="border-radius: 2rem;">
            <i class="bi bi-bag-check"></i> Ver Mis Compras
        </a>
    </div>

    @if($ventas->isEmpty())
        <div class="empty-state">
            <i class="bi bi-shop-window"></i>
            <h3>Aún no has vendido ninguna prenda</h3>
            <p>Publica prendas para comenzar a vender</p>
            <a href="{{ route('prendas.create') }}" class="btn btn-primary" style="background-color: var(--clr-green); border: none; border-radius: 2rem; padding: 1rem 2.5rem; margin-top: 1rem;">
                <i class="bi bi-plus-circle"></i> Publicar Prenda
            </a>
        </div>
    @else
        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="estadistica-card">
                    <i class="bi bi-cart-check-fill"></i>
                    <div>
                        <h3>{{ $ventas->count() }}</h3>
                        <p>Pedidos Totales</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="estadistica-card">
                    <i class="bi bi-box-seam"></i>
                    <div>
                        @php
                            $totalProductos = $ventas->flatten()->count();
                        @endphp
                        <h3>{{ $totalProductos }}</h3>
                        <p>Productos Vendidos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="estadistica-card destacado">
                    <i class="bi bi-cash-stack"></i>
                    <div>
                        @php
                            $totalVentas = $ventas->flatten()->sum('subtotal');
                        @endphp
                        <h3>${{ number_format($totalVentas, 0, ',', '.') }}</h3>
                        <p>Total Ventas</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros de Ordenamiento -->
        <div class="mb-4">
            <form action="{{ route('pedidos.misVentas') }}" method="GET" class="d-flex gap-2 align-items-center flex-wrap">
                <select name="orden" class="form-select" style="max-width: 200px; border-radius: 2rem;" onchange="this.form.submit()">
                    <option value="reciente" {{ request('orden') == 'reciente' ? 'selected' : '' }}>Más recientes</option>
                    <option value="antiguo" {{ request('orden') == 'antiguo' ? 'selected' : '' }}>Más antiguos</option>
                    <option value="mayor_monto" {{ request('orden') == 'mayor_monto' ? 'selected' : '' }}>Mayor monto</option>
                    <option value="menor_monto" {{ request('orden') == 'menor_monto' ? 'selected' : '' }}>Menor monto</option>
                </select>

                @if(request('orden'))
                    <a href="{{ route('pedidos.misVentas') }}" class="btn btn-outline-secondary" style="border-radius: 2rem;">
                        <i class="bi bi-x-circle"></i> Limpiar
                    </a>
                @endif
            </form>
        </div>

        <!-- Lista de Ventas -->
        <div class="pedidos-lista">
            @foreach($ventas as $pedidoId => $detalles)
                @php
                    $pedido = $detalles->first()->pedido;
                    $totalPedido = $detalles->sum('subtotal');
                @endphp
                
                <div class="pedido-card venta-card">
                    <div class="pedido-header">
                        <div class="d-flex flex-wrap gap-3 align-items-center">
                            <span class="pedido-id">Venta #{{ $pedido->id }}</span>
                            <span class="pedido-fecha">
                                <i class="bi bi-calendar3"></i>
                                {{ \Carbon\Carbon::parse($pedido->fecha)->format('d/m/Y H:i') }}
                            </span>
                        </div>
                        
                        <div class="pedido-comprador">
                            <i class="bi bi-person-fill"></i> 
                            <div>
                                <strong>{{ $pedido->usuario->name ?? 'Usuario' }}</strong>
                                @if(isset($pedido->usuario->email))
                                    <small class="text-muted d-block">
                                        <i class="bi bi-envelope"></i> {{ $pedido->usuario->email }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="pedido-productos">
                        @foreach($detalles as $detalle)
                        <div class="producto-item">
                            <div class="producto-imagen-container">
                                @if($detalle->prenda && $detalle->prenda->imgsPrendas->isNotEmpty())
                                    <img src="{{ asset('storage/' . $detalle->prenda->imgsPrendas->first()->direccion_imagen) }}" 
                                         alt="{{ $detalle->prenda->titulo }}"
                                         onerror="this.src='https://via.placeholder.com/80?text=Sin+Imagen'">
                                @else
                                    <img src="https://via.placeholder.com/80?text=Sin+Imagen" alt="Producto">
                                @endif
                            </div>
                            <div class="producto-info">
                                <h5>{{ $detalle->prenda->titulo ?? 'Producto no disponible' }}</h5>
                                <p class="mb-1">
                                    <span class="badge bg-secondary">Cantidad: {{ $detalle->cantidad }}</span>
                                </p>
                                @if($detalle->prenda)
                                    <div class="d-flex gap-2 flex-wrap">
                                        <small class="text-muted">
                                            <i class="bi bi-tag"></i> {{ $detalle->prenda->categoria->tipo_prenda ?? 'Sin categoría' }}
                                        </small>
                                        
                                        @if($detalle->prenda->condicion)
                                            <span class="badge {{ $detalle->prenda->condicion->estado === 'Nuevo' ? 'badge-nuevo' : 'badge-usado' }}">
                                                <i class="bi bi-shield-check"></i> {{ $detalle->prenda->condicion->estado }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="producto-subtotal ganancia">
                                <small>Tu ganancia:</small>
                                <strong>${{ number_format($detalle->subtotal, 0, ',', '.') }} COP</strong>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="pedido-footer">
                        <div class="total-venta">
                            Total de tu venta: <strong>${{ number_format($totalPedido, 0, ',', '.') }} COP</strong>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('pedidos.show', $pedido->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Ver Detalle Completo
                            </a>
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