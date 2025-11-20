@extends('layouts.app')

@section('title', 'Mis Publicaciones - Dione Store')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
<link rel="stylesheet" href="{{ asset('css/mis-publicaciones.css') }}">

@endsection

@section('content')
<div class="container">
    <!-- Header con título y botón -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="titulo-principal">
            <i class="bi bi-grid-fill"></i> Mis Publicaciones
        </h2>
        <a href="{{ route('prendas.create') }}" class="btn btn-success" style="background-color: var(--clr-green); border: none; border-radius: 2rem; padding: 0.75rem 2rem;">
            <i class="bi bi-plus-circle"></i> Nueva Publicación
        </a>
    </div>

    <!-- Estadísticas -->
    <div class="stats-container">
        <div class="stat-card">
            <i class="bi bi-grid-3x3-gap-fill"></i>
            <div class="stat-number">{{ $prendas->count() }}</div>
            <div class="stat-label">Publicaciones</div>
        </div>

        <div class="stat-card orange">
            <i class="bi bi-cash-stack"></i>
            <div class="stat-number">${{ number_format($prendas->sum('precio'), 0, ',', '.') }}</div>
            <div class="stat-label">Valor Total</div>
        </div>

        <div class="stat-card green">
            <i class="bi bi-eye-fill"></i>
            <div class="stat-number">-</div>
            <div class="stat-label">Visitas</div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="mb-4">
        <form action="{{ route('mis-publicaciones') }}" method="GET" class="d-flex gap-2 align-items-center flex-wrap">
            <select name="categoria" class="form-select" style="max-width: 200px; border-radius: 2rem;" onchange="this.form.submit()">
                <option value="">Todas las categorías</option>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}" {{ request('categoria') == $categoria->id ? 'selected' : '' }}>
                        {{ $categoria->tipo_prenda }}
                    </option>
                @endforeach
            </select>

            <select name="orden" class="form-select" style="max-width: 200px; border-radius: 2rem;" onchange="this.form.submit()">
                <option value="reciente" {{ request('orden') == 'reciente' ? 'selected' : '' }}>Más recientes</option>
                <option value="antiguo" {{ request('orden') == 'antiguo' ? 'selected' : '' }}>Más antiguos</option>
                <option value="precio_alto" {{ request('orden') == 'precio_alto' ? 'selected' : '' }}>Mayor precio</option>
                <option value="precio_bajo" {{ request('orden') == 'precio_bajo' ? 'selected' : '' }}>Menor precio</option>
            </select>

            @if(request('categoria') || request('orden'))
                <a href="{{ route('mis-publicaciones') }}" class="btn btn-outline-secondary" style="border-radius: 2rem;">
                    <i class="bi bi-x-circle"></i> Limpiar filtros
                </a>
            @endif
        </form>
    </div>

    <!-- Grid de Productos -->
    @if($prendas->isEmpty())
        <div class="empty-state-custom">
            <i class="bi bi-inbox"></i>
            <h3 style="color: var(--clr-main); margin-bottom: 1rem;">No tienes publicaciones aún</h3>
            <p style="color: #6c757d; margin-bottom: 2rem; font-size: 1.1rem;">
                ¡Comienza a vender tu ropa y contribuye a la moda sostenible!
            </p>
            <a href="{{ route('prendas.create') }}" class="btn btn-primary" style="background-color: var(--clr-orange); border: none; border-radius: 2rem; padding: 1rem 2.5rem; font-size: 1.1rem;">
                <i class="bi bi-plus-circle-fill"></i> Publicar mi primera prenda
            </a>
        </div>
    @else
        <div class="contenedor-productos">
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
                             src="https://via.placeholder.com/300x400?text=Sin+Imagen" 
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

                    <!-- Badges de estado - IGUAL QUE HOME.BLADE.PHP -->
                    <div class="mb-2">
                        @if($prenda->condicion)
                            <span class="badge mb-2 {{ $prenda->condicion->estado === 'Nuevo' ? 'badge-nuevo' : 'badge-usado' }}">
                                <i class="bi bi-shield-check"></i> {{ $prenda->condicion->estado }}
                            </span>
                        @endif
                        
                        <span class="badge bg-info">
                            <i class="bi bi-calendar"></i> {{ $prenda->created_at->diffForHumans() }}
                        </span>
                    </div>
                    
                    <!-- Acciones - USANDO DATA ATTRIBUTES -->
                    <div class="producto-acciones">
                        <a href="{{ route('prendas.show', $prenda->id) }}" 
                           class="btn btn-primary" 
                           style="background-color: var(--clr-main);">
                            <i class="bi bi-eye"></i> Ver
                        </a>
                        
                        <a href="{{ route('prendas.edit', $prenda->id) }}" 
                           class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        
                        <button type="button" 
                                class="btn btn-danger" 
                                data-prenda-id="{{ $prenda->id }}"
                                data-prenda-titulo="{{ $prenda->titulo }}"
                                onclick="confirmarEliminacion(this)">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Forms ocultos para eliminación -->
@foreach($prendas as $prenda)
<form id="delete-form-{{ $prenda->id }}" 
      action="{{ route('prendas.destroy', $prenda->id) }}" 
      method="POST" 
      style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endforeach
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Función de confirmación usando data attributes
function confirmarEliminacion(button) {
    const id = button.dataset.prendaId;
    const titulo = button.dataset.prendaTitulo;
    
    Swal.fire({
        title: '¿Eliminar publicación?',
        html: 'Se eliminará "<strong>' + titulo + '</strong>" y todas sus imágenes.<br>Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d63031',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}
</script>

<!-- Mostrar mensaje de éxito - DENTRO DE ETIQUETA SCRIPT -->
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
@endsection