@extends('layouts.app')

@section('title', $prenda->titulo . ' - Dione Store')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/show.css') }}">
@endsection

@section('content')
<!-- Breadcrumb -->
<nav class="breadcrumb-nav">
    <a href="{{ route('home') }}">
        <i class="bi bi-house-door"></i> Inicio
    </a>
    <span class="mx-2">/</span>
    @if($prenda->categoria)
        <a href="{{ route('home', ['categoria' => $prenda->categoria_id]) }}">
            {{ $prenda->categoria->tipo_prenda }}
        </a>
        <span class="mx-2">/</span>
    @endif
    <span style="color: #6c757d;">{{ $prenda->titulo }}</span>
</nav>

<div class="detalle-container">
    <!-- Galería de Imágenes -->
    <div class="galeria-imagenes">
        @if($prenda->imgsPrendas->isNotEmpty())
            <img id="imagen-principal" 
                 class="imagen-principal" 
                 src="{{ asset('storage/' . $prenda->imgsPrendas->first()->direccion_imagen) }}" 
                 alt="{{ $prenda->titulo }}"
                 onerror="this.src='https://via.placeholder.com/600x800?text=Sin+Imagen'">
            
            @if($prenda->imgsPrendas->count() > 1)
            <div class="imagenes-thumbnails">
                @foreach($prenda->imgsPrendas as $index => $imagen)
                @php
                    $imagenUrl = asset('storage/' . $imagen->direccion_imagen);
                @endphp
                <img class="thumbnail {{ $index === 0 ? 'active' : '' }}" 
                     src="{{ $imagenUrl }}" 
                     alt="{{ $prenda->titulo }}"
                     onclick="cambiarImagenPrincipal(this, '{{ $imagenUrl }}')"
                     onerror="this.src='https://via.placeholder.com/100?text=Error'">
                @endforeach
            </div>
            @endif
        @else
            <img class="imagen-principal" 
                 src="https://via.placeholder.com/600x800?text=Sin+Imagen&text={{ urlencode($prenda->titulo) }}" 
                 alt="{{ $prenda->titulo }}">
        @endif
    </div>

    <!-- Información de la Prenda -->
    <div class="info-prenda">
        <h1 class="titulo-detalle">{{ $prenda->titulo }}</h1>
        
        <div class="precio-grande">
            ${{ number_format($prenda->precio, 0, ',', '.') }} 
            <small style="font-size: 1.2rem; color: #6c757d; font-weight: 400;">COP</small>
        </div>

        @if($prenda->condicion)
        @php
            $claseEstado = $prenda->condicion->estado === 'Nuevo' ? 'badge-nuevo' : 'badge-usado';
        @endphp
        <div>
            <span class="badge-estado {{ $claseEstado }}">
                <i class="bi bi-{{ $prenda->condicion->estado === 'Nuevo' ? 'star-fill' : 'recycle' }}"></i> 
                {{ $prenda->condicion->estado }}
            </span>
        </div>
        @endif

        <div class="detalle-item">
            <i class="bi bi-rulers"></i>
            <div class="detalle-item-content">
                <strong>Talla</strong>
                <span>{{ $prenda->talla }}</span>
            </div>
        </div>

        <div class="detalle-item">
            <i class="bi bi-grid-3x3-gap"></i>
            <div class="detalle-item-content">
                <strong>Categoría</strong>
                <span>{{ $prenda->categoria->tipo_prenda ?? 'Sin categoría' }}</span>
            </div>
        </div>

        <div class="detalle-item">
            <i class="bi bi-palette-fill"></i>
            <div class="detalle-item-content">
                <strong>Material</strong>
                <span>{{ $prenda->material }}</span>
            </div>
        </div>

        <div class="detalle-item">
            <i class="bi bi-card-text"></i>
            <div class="detalle-item-content">
                <strong>Descripción</strong>
                <span>{{ $prenda->descripcion }}</span>
            </div>
        </div>

        <!-- Vendedor -->
        <div class="vendedor-info">
            <div class="vendedor-avatar">
                {{ strtoupper(substr($prenda->usuario->name ?? 'U', 0, 1)) }}
            </div>
            <div>
                <small style="opacity: 0.8;">Vendido por</small><br>
                <strong style="font-size: 1.2rem;">{{ $prenda->usuario->name ?? 'Usuario' }}</strong>
            </div>
        </div>

        <!-- Botón Agregar al Carrito o Acciones de Propietario -->
        @auth
            @if($prenda->usuario_id !== Auth::id())
            <form action="{{ route('carrito.agregar', $prenda->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn-agregar-carrito">
                    <i class="bi bi-cart-plus-fill"></i> 
                    <span>Agregar al Carrito</span>
                </button>
            </form>
            @else
            <div class="alert alert-info text-center" style="border-radius: 1rem;">
                <i class="bi bi-info-circle-fill"></i> 
                <strong>Esta es tu publicación</strong>
            </div>
            <div class="acciones-propietario">
                <a href="{{ route('prendas.edit', $prenda->id) }}" class="btn btn-warning">
                    <i class="bi bi-pencil-square"></i> Editar
                </a>
                <form action="{{ route('prendas.destroy', $prenda->id) }}" method="POST" style="flex: 1;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100" 
                            onclick="return confirm('¿Estás seguro de eliminar esta prenda? Esta acción no se puede deshacer.')">
                        <i class="bi bi-trash3"></i> Eliminar
                    </button>
                </form>
            </div>
            @endif
        @else
        <div class="alert alert-warning text-center" style="border-radius: 1rem;">
            <i class="bi bi-exclamation-triangle-fill"></i> 
            <strong><a href="{{ route('login') }}" style="color: inherit; text-decoration: underline;">Inicia sesión</a></strong> para agregar al carrito
        </div>
        @endauth
    </div>
</div>

{{-- ✅ COMPONENTE DE HUELLA DE CARBONO --}}
@if(isset($huella))
    @include('components.huella-carbono', ['huella' => $huella, 'prenda' => $prenda])
@endif

<!-- Productos Similares -->
@if(isset($productosSimilares) && $productosSimilares->isNotEmpty())
<div class="seccion-similares">
    <h3>
        <i class="bi bi-stars"></i>
        También te puede interesar
    </h3>
    <div class="contenedor-productos">
        @foreach($productosSimilares as $similar)
        <div class="producto">
            <a href="{{ route('prendas.show', $similar->id) }}">
                @if($similar->imgsPrendas->isNotEmpty())
                    <img class="producto-imagen" 
                         src="{{ asset('storage/' . $similar->imgsPrendas->first()->direccion_imagen) }}" 
                         alt="{{ $similar->titulo }}"
                         loading="lazy"
                         onerror="this.src='https://via.placeholder.com/300x400?text=Sin+Imagen'">
                @else
                    <img class="producto-imagen" 
                         src="https://via.placeholder.com/300x400?text=Sin+Imagen" 
                         alt="{{ $similar->titulo }}">
                @endif
            </a>
            
            <div class="producto-detalles">
                <h3 class="producto-titulo">{{ $similar->titulo }}</h3>
                <p class="producto-precio">${{ number_format($similar->precio, 0, ',', '.') }} <small style="font-size: 0.8rem;">COP</small></p>
                <a href="{{ route('prendas.show', $similar->id) }}" class="producto-agregar">
                    <i class="bi bi-eye"></i> Ver detalles
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
    function cambiarImagenPrincipal(thumbnail, src) {
        // Cambiar imagen principal
        document.getElementById('imagen-principal').src = src;
        
        // Remover clase active de todos los thumbnails
        document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
        
        // Agregar clase active al thumbnail clickeado
        thumbnail.classList.add('active');
    }
</script>

@if(session('success'))
<script>
    // Auto-hide success message
    setTimeout(() => {
        const alert = document.querySelector('.alert-success');
        if (alert) {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 150);
        }
    }, 5000);
</script>
@endif

@if(session('info'))
<script>
    alert("{{ session('info') }}");
</script>
@endif
@endsection