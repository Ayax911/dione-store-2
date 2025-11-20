<!--
        Archivo: resources/views/home.blade.php
        Propósito: Mostrar el catálogo (home) de Dione Store.

        Resumen:
        - Muestra una sección "hero", barra de búsqueda, filtros por categoría,
            listado de prendas y estados vacíos/alertas.
        - Renderiza la cantidad de productos y soporta búsquedas y filtrado por categoría.

        Variables esperadas (desde el controlador):
        - $prendas : Collection|array de modelos `Prenda` con relaciones opcionales:
                ->imgsPrendas, ->categoria, ->condicion
        - $categorias : Collection|array de modelos `Categoria`

        Parámetros de petición (query string):
        - buscar : texto para filtrar por título/descripcion
        - categoria : id de la categoría para filtrar

        Rutas usadas desde la vista:
        - route('home')
        - route('prendas.create') (solo usuarios autenticados ven el botón)
        - route('prendas.show', $prenda->id)

        Notas para desarrolladores:
        - Las imágenes se muestran con `asset('storage/...')` y hay un placeholder si falta.
        - La vista extiende `layouts.app` y define las secciones: `title`, `styles`, `content`, `scripts`.
        - Mensajes flash `session('success')` y `session('error')` se muestran y se autoocultan mediante JS.
        - Estilos adicionales cargados desde `public/css/home.css`.
-->

@extends('layouts.app')

@section('title', 'Catálogo - Dione Store')

@section('styles')
    
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endsection

@section('content')
<!-- Hero Section -->
<div class="hero-section">
    <h1> Dione Store</h1>
    <p style="font-size: 1.2rem; opacity: 0.9;">Moda sostenible, segundo uso, primer impacto</p>
</div>

<!-- Barra de búsqueda -->
<div class="search-container">
    <form action="{{ route('home') }}" method="GET" class="d-flex gap-2">
        <input type="text" 
               name="buscar" 
               class="search-input" 
               placeholder="🔍 Buscar prendas por título o descripción..." 
               value="{{ request('buscar') }}">
        <button type="submit" class="btn" style="background-color: var(--clr-orange); color: white; border: none; border-radius: 2rem; padding: 0.75rem 2rem; white-space: nowrap;">
            <i class="bi bi-search"></i> Buscar
        </button>
        @if(request('buscar'))
            <a href="{{ route('home') }}" class="btn btn-outline-secondary" style="border-radius: 2rem; padding: 0.75rem 1.5rem;">
                <i class="bi bi-x-circle"></i>
            </a>
        @endif
    </form>
</div>

<!-- Filtros por categoría -->
@if($categorias->isNotEmpty())
<div class="filter-section">
    <form action="{{ route('home') }}" method="GET" class="d-flex gap-2 flex-wrap w-100">
        @if(request('buscar'))
            <input type="hidden" name="buscar" value="{{ request('buscar') }}">
        @endif
        
        <button type="submit" name="categoria" value="" 
                class="filter-btn {{ !request('categoria') ? 'active' : '' }}">
            <i class="bi bi-grid-3x3-gap"></i> Todos
        </button>
        
        @foreach($categorias as $categoria)
            <button type="submit" name="categoria" value="{{ $categoria->id }}" 
                    class="filter-btn {{ request('categoria') == $categoria->id ? 'active' : '' }}">
                <i class="bi bi-tag"></i> {{ $categoria->tipo_prenda }}
            </button>
        @endforeach
    </form>
</div>
@endif

<!-- Título y contador -->
<h2 class="titulo-principal">
    @if(request('categoria'))
        <i class="bi bi-funnel-fill"></i> {{ $categorias->find(request('categoria'))->tipo_prenda ?? 'Productos' }}
    @elseif(request('buscar'))
        <i class="bi bi-search"></i> Resultados para "{{ request('buscar') }}"
    @else
        <i class="bi bi-shop"></i> Catálogo completo
    @endif
    <span class="badge" style="background-color: var(--clr-green); font-size: 1rem;">
        {{ $prendas->count() }} {{ $prendas->count() == 1 ? 'producto' : 'productos' }}
    </span>
</h2>

<!-- Botón para publicar (solo usuarios autenticados) -->
@auth
<div class="mb-4">
    <a href="{{ route('prendas.create') }}" class="btn btn-success" style="background-color: var(--clr-green); border: none; border-radius: 2rem; padding: 0.75rem 2rem;">
        <i class="bi bi-plus-circle"></i> Publicar prenda
    </a>
</div>
@endauth

<!-- Grid de productos -->
@if($prendas->isEmpty())
    <div class="empty-state">
        <i class="bi bi-inbox"></i>
        <h3 style="color: var(--clr-main); margin-bottom: 1rem;">No hay productos disponibles</h3>
        <p style="color: #6c757d; margin-bottom: 2rem;">
            @if(request('buscar'))
                No encontramos productos que coincidan con tu búsqueda.
                <br><a href="{{ route('home') }}">Ver todos los productos</a>
            @else
                ¡Sé el primero en publicar una prenda!
            @endif
        </p>
        @auth
            <a href="{{ route('prendas.create') }}" class="btn btn-primary" style="background-color: var(--clr-orange); border: none; border-radius: 2rem; padding: 1rem 2.5rem;">
                <i class="bi bi-plus-circle"></i> Publicar tu primera prenda
            </a>
        @endauth
    </div>
@else
    <div id="contenedor-productos" class="contenedor-productos">
        @foreach($prendas as $prenda)
        <div class="producto">
            <a href="{{ route('prendas.show', $prenda->id) }}">
                @if($prenda->imgsPrendas->isNotEmpty())
                    <img class="producto-imagen" 
                         src="{{ asset('storage/' . $prenda->imgsPrendas->first()->direccion_imagen) }}" 
                         alt="{{ $prenda->titulo }}"
                         loading="lazy"
                         onerror="this.src='https://via.placeholder.com/300x400?text=Sin+Imagen'">
                @else
                    <img class="producto-imagen" 
                         src="https://via.placeholder.com/300x400?text=Sin+Imagen&text={{ urlencode($prenda->titulo) }}" 
                         alt="{{ $prenda->titulo }}">
                @endif
            </a>
            
            <div class="producto-detalles">
                <h3 class="producto-titulo" title="{{ $prenda->titulo }}">
                    {{ $prenda->titulo }}
                </h3>
                
                <p class="producto-precio">
                    ${{ number_format($prenda->precio, 0, ',', '.') }} <small style="font-size: 0.8rem;">COP</small>
                </p>
                
                <div class="d-flex align-items-center gap-2 mb-2" style="font-size: 0.9rem; color: #6c757d;">
                    <i class="bi bi-tag"></i> 
                    <span>{{ $prenda->categoria->tipo_prenda ?? 'Sin categoría' }}</span>
                </div>
                
                @if($prenda->condicion)
                    <span class="badge mb-2 {{ $prenda->condicion->estado === 'Nuevo' ? 'badge-nuevo' : 'badge-usado' }}">
                        <i class="bi bi-shield-check"></i> {{ $prenda->condicion->estado }}
                    </span>
                @endif
                
                <a href="{{ route('prendas.show', $prenda->id) }}" 
                   class="producto-agregar">
                    <i class="bi bi-eye"></i> Ver detalles
                </a>
            </div>
        </div>
        @endforeach
    </div>
@endif

<!-- Mensaje de éxito si viene de crear/editar/eliminar -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 1rem;">
    <i class="bi bi-check-circle"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 1rem;">
    <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@endsection

@section('scripts')
<script>
    // Auto-hide alerts después de 5 segundos
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>
@endsection