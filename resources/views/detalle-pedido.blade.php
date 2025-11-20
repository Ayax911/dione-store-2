@extends('layouts.app')

@section('title', 'Pedido #' . $pedido->id . ' - Dione Store')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/detalle-pedido.css') }}">
@endsection

@section('content')
<div class="detalle-pedido-container">
    
    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav">
        <a href="{{ route('home') }}">
            <i class="bi bi-house-door"></i> Inicio
        </a>
        <span class="mx-2">/</span>
        @if($esComprador ?? false)
        <a href="{{ route('pedidos.misCompras') }}">
            Mis Compras
        </a>
        @else
        <a href="{{ route('pedidos.misVentas') }}">
            Mis Ventas
        </a>
        @endif
        <span class="mx-2">/</span>
        <span style="color: #6c757d;">Pedido #{{ $pedido->id }}</span>
    </nav>

    <!-- Alerta de Rol -->
    @if(isset($esComprador) && isset($esVendedor))
        @if($esComprador && $esVendedor)
        <div class="alert-role">
            <i class="bi bi-info-circle-fill"></i>
            <strong>Nota:</strong> Eres tanto el comprador como el vendedor en este pedido
        </div>
        @elseif($esComprador)
        <div class="alert-role">
            <i class="bi bi-bag-check-fill"></i>
            <strong>Tu Compra</strong> - Visualizando como comprador
        </div>
        @elseif($esVendedor)
        <div class="alert-role">
            <i class="bi bi-shop"></i>
            <strong>Tu Venta</strong> - Visualizando como vendedor
        </div>
        @endif
    @endif

    <!-- Header del Pedido -->
    <div class="pedido-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 style="margin: 0; font-weight: 800; font-size: 2rem;">
                    <i class="bi bi-receipt"></i> Pedido #{{ $pedido->id }}
                </h1>
                <p style="opacity: 0.9; margin: 0.5rem 0 0 0;">
                    Realizado el {{ $pedido->fecha ? \Carbon\Carbon::parse($pedido->fecha)->format('d/m/Y H:i') : 'N/A' }}
                </p>
            </div>
            <div>
                <span class="estado-badge estado-completado">
                    <i class="bi bi-check-circle"></i> Completado
                </span>
            </div>
        </div>
    </div>

    <!-- Información del Cliente/Vendedor -->
    <div class="pedido-info-card">
        <h3 style="color: var(--clr-main); font-weight: 700; margin-bottom: 1.5rem;">
            <i class="bi bi-person-circle"></i> Información de Contacto
        </h3>
        
        <div class="row">
            <div class="col-md-6">
                <div style="margin-bottom: 1rem;">
                    <strong style="color: #64748b;">
                        @if($esComprador ?? false)
                            Tu información:
                        @else
                            Cliente:
                        @endif
                    </strong><br>
                    <span style="font-size: 1.1rem;">{{ $pedido->usuario->name }}</span>
                </div>
                <div>
                    <strong style="color: #64748b;">Email:</strong><br>
                    <span style="font-size: 1.1rem;">{{ $pedido->usuario->email }}</span>
                </div>
            </div>
            <div class="col-md-6">
                <div style="background: #f1f5f9; padding: 1rem; border-radius: 10px;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <i class="bi bi-calendar3" style="color: var(--clr-orange);"></i>
                        <strong>Fecha del Pedido:</strong>
                    </div>
                    <div style="font-size: 1.1rem; padding-left: 1.8rem;">
                        {{ $pedido->fecha ? \Carbon\Carbon::parse($pedido->fecha)->format('d \d\e F, Y') : 'N/A' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos del Pedido -->
    <div class="pedido-info-card">
        <h3 style="color: var(--clr-main); font-weight: 700; margin-bottom: 1.5rem;">
            <i class="bi bi-bag-check"></i> Productos ({{ $pedido->detallesPedidos->count() }})
        </h3>

        @forelse($pedido->detallesPedidos as $detalle)
            @if($detalle->prenda)
            <div class="producto-pedido">
                <a href="{{ route('prendas.show', $detalle->prenda_id) }}">
                    @if($detalle->prenda->imgsPrendas->isNotEmpty())
                        <img src="{{ asset('storage/' . $detalle->prenda->imgsPrendas->first()->direccion_imagen) }}" 
                             alt="{{ $detalle->prenda->titulo }}"
                             onerror="this.src='https://via.placeholder.com/120?text=Sin+Imagen'">
                    @else
                        <img src="https://via.placeholder.com/120?text=Sin+Imagen" 
                             alt="{{ $detalle->prenda->titulo }}">
                    @endif
                </a>
                
                <div class="producto-info">
                    <a href="{{ route('prendas.show', $detalle->prenda_id) }}" style="text-decoration: none; color: inherit;">
                        <h4 style="margin: 0 0 0.5rem 0; color: var(--clr-main); font-weight: 600;">
                            {{ $detalle->prenda->titulo }}
                        </h4>
                    </a>
                    
                    <div style="display: flex; gap: 2rem; flex-wrap: wrap; margin-bottom: 0.5rem;">
                        <div>
                            <small style="color: #64748b;">Talla:</small>
                            <strong>{{ $detalle->prenda->talla }}</strong>
                        </div>
                        <div>
                            <small style="color: #64748b;">Material:</small>
                            <strong>{{ $detalle->prenda->material }}</strong>
                        </div>
                        <div>
                            <small style="color: #64748b;">Cantidad:</small>
                            <strong>{{ $detalle->cantidad }}</strong>
                        </div>
                        @if($detalle->prenda->categoria)
                        <div>
                            <small style="color: #64748b;">Categoría:</small>
                            <strong>{{ $detalle->prenda->categoria->tipo_prenda }}</strong>
                        </div>
                        @endif
                    </div>

                    @if($detalle->prenda->usuario && !($esComprador ?? false))
                    <div style="margin-top: 0.5rem;">
                        <small style="color: #64748b;">Vendedor:</small>
                        <strong>{{ $detalle->prenda->usuario->name }}</strong>
                    </div>
                    @endif

                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
                        <div>
                            <small style="color: #64748b;">Precio unitario:</small>
                            <div style="font-size: 1.1rem; font-weight: 600;">
                                ${{ number_format($detalle->prenda->precio, 0, ',', '.') }} COP
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <small style="color: #64748b;">Subtotal:</small>
                            <div style="font-size: 1.3rem; font-weight: 700; color: var(--clr-orange);">
                                ${{ number_format($detalle->subtotal, 0, ',', '.') }} COP
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-warning" style="border-radius: 10px;">
                <i class="bi bi-exclamation-triangle"></i>
                <strong>Producto no disponible</strong> - Este producto ha sido eliminado
            </div>
            @endif
        @empty
        <div class="alert alert-info" style="border-radius: 10px;">
            <i class="bi bi-info-circle"></i>
            No hay productos en este pedido
        </div>
        @endforelse
    </div>

    <!-- Totales -->
    <div class="totales-card">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <div style="font-size: 1.2rem; opacity: 0.9; margin-bottom: 0.5rem;">Total del Pedido</div>
                <div style="font-size: 3rem; font-weight: 800; line-height: 1;">
                    ${{ number_format($pedido->total_pedido, 0, ',', '.') }}
                </div>
                <div style="font-size: 1.2rem; opacity: 0.9; margin-top: 0.5rem;">COP</div>
            </div>
            <div style="text-align: right;">
                <i class="bi bi-check-circle" style="font-size: 4rem; opacity: 0.5;"></i>
            </div>
        </div>

        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 2px solid rgba(255,255,255,0.3);">
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <span style="opacity: 0.9;">Subtotal ({{ $pedido->detallesPedidos->count() }} {{ $pedido->detallesPedidos->count() == 1 ? 'producto' : 'productos' }}):</span>
                <strong style="font-size: 1.1rem;">${{ number_format($pedido->detallesPedidos->sum('subtotal'), 0, ',', '.') }} COP</strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <span style="opacity: 0.9;">Envío:</span>
                <strong style="font-size: 1.1rem;">Gratis</strong>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 1.2rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.3);">
                <strong>Total:</strong>
                <strong>${{ number_format($pedido->total_pedido, 0, ',', '.') }} COP</strong>
            </div>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="d-flex gap-3 justify-content-center flex-wrap" style="margin-top: 2rem;">
        @if($esComprador ?? false)
        <a href="{{ route('pedidos.misCompras') }}" class="btn btn-secondary" style="border-radius: 50px; padding: 0.75rem 2rem;">
            <i class="bi bi-arrow-left"></i> Volver a Mis Compras
        </a>
        @else
        <a href="{{ route('pedidos.misVentas') }}" class="btn btn-secondary" style="border-radius: 50px; padding: 0.75rem 2rem;">
            <i class="bi bi-arrow-left"></i> Volver a Mis Ventas
        </a>
        @endif
        
        <a href="{{ route('home') }}" class="btn btn-primary" style="background-color: var(--clr-orange); border: none; border-radius: 50px; padding: 0.75rem 2rem;">
            <i class="bi bi-house"></i> Ir al Inicio
        </a>
        
        <button onclick="window.print()" class="btn btn-outline-primary" style="border-radius: 50px; padding: 0.75rem 2rem;">
            <i class="bi bi-printer"></i> Imprimir
        </button>
    </div>

</div>
@endsection

@section('scripts')
<script>
    // Estilos de impresión
    const style = document.createElement('style');
    style.textContent = `
        @media print {
            .breadcrumb-nav, .btn, nav, footer {
                display: none !important;
            }
            .detalle-pedido-container {
                max-width: 100% !important;
            }
        }
    `;
    document.head.appendChild(style);
</script>
@endsection