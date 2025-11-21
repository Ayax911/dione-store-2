@extends('layouts.app')

@section('title', 'Publicar Prenda - Dione Store')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/create.css') }}">
@endsection

@section('content')
<div class="container" style="max-width: 900px; margin-top: 2rem;">
    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav mb-4">
        <a href="{{ route('home') }}">
            <i class="bi bi-house-door"></i> Inicio
        </a>
        <span class="mx-2">/</span>
        <span style="color: #6c757d;">Publicar Prenda</span>
    </nav>

    <div class="form-container">
        <div class="text-center mb-4">
            <h2 style="color: var(--clr-main); font-weight: 700;">
                <i class="bi bi-plus-circle-fill"></i> Publicar Nueva Prenda
            </h2>
            <p style="color: #6c757d;">Completa la información de tu prenda y sube fotos para venderla</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" style="border-radius: 1rem;">
                <i class="bi bi-exclamation-triangle-fill"></i> 
                <strong>Corrige los siguientes errores:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('prendas.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Titulo -->
            <div class="mb-4">
                <label for="titulo" class="form-label">
                    <i class="bi bi-tag-fill"></i> Título de la prenda *
                </label>
                <input type="text" 
                       class="form-control" 
                       id="titulo" 
                       name="titulo" 
                       value="{{ old('titulo') }}" 
                       placeholder="Ej: Camiseta Nike Original Talla M" 
                       required 
                       maxlength="50">
                <small class="text-muted">Máximo 50 caracteres</small>
            </div>

            <!-- Categoria, Talla y Precio -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <label for="categoria_id" class="form-label">
                        <i class="bi bi-grid-3x3-gap"></i> Categoría *
                    </label>
                    <select class="form-select" id="categoria_id" name="categoria_id" required>
                        <option value="">Selecciona...</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->tipo_prenda }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="talla" class="form-label">
                        <i class="bi bi-rulers"></i> Talla *
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="talla" 
                           name="talla" 
                           value="{{ old('talla') }}" 
                           placeholder="Ej: M, XL, 42" 
                           required 
                           maxlength="10">
                </div>

                <div class="col-md-4">
                    <label for="precio" class="form-label">
                        <i class="bi bi-currency-dollar"></i> Precio (COP) *
                    </label>
                    <input type="number" 
                           class="form-control" 
                           id="precio" 
                           name="precio" 
                           value="{{ old('precio') }}" 
                           placeholder="50000" 
                           required 
                           min="0" 
                           step="1000">
                </div>
            </div>

            <!-- Material -->
            <div class="mb-4">
                <label for="material" class="form-label">
                    <i class="bi bi-palette-fill"></i> Material *
                </label>
                <input type="text" 
                       class="form-control" 
                       id="material" 
                       name="material" 
                       value="{{ old('material') }}" 
                       placeholder="Ej: Algodón 100%, Poliéster, Mezclilla" 
                       required 
                       maxlength="50">
                <small class="text-muted">Describe el material principal de la prenda</small>
            </div>

            <!-- Descripcion -->
            <div class="mb-4">
                <label for="descripcion" class="form-label">
                    <i class="bi bi-card-text"></i> Descripción *
                </label>
                <textarea class="form-control" 
                          id="descripcion" 
                          name="descripcion" 
                          rows="5" 
                          placeholder="Describe tu prenda: estado, detalles, color, motivo de venta, etc." 
                          required 
                          maxlength="500">{{ old('descripcion') }}</textarea>
                <small class="text-muted">Máximo 500 caracteres - <span id="char-count">0</span>/500</small>
            </div>

            <!-- Upload de Imagenes -->
            <div class="mb-4">
                <label class="form-label">
                    <i class="bi bi-images"></i> Imágenes de la prenda
                </label>
                
                <div class="file-upload-area" onclick="document.getElementById('imagenes').click()">
                    <i class="bi bi-cloud-upload-fill"></i>
                    <p class="mb-0"><strong>Click para subir imágenes</strong></p>
                    <small class="text-muted">Puedes subir múltiples imágenes (JPG, PNG, GIF - Máx 2MB c/u)</small>
                    <small class="text-muted d-block mt-1">📸 Recomendado: 3-5 fotos desde diferentes ángulos</small>
                </div>
                
                <input type="file" 
                       class="d-none" 
                       id="imagenes" 
                       name="imagenes[]" 
                       accept="image/*" 
                       multiple
                       onchange="previewImages(event)">
                
                <div id="preview-container" class="preview-container"></div>
            </div>

            <!-- Botones de Accion -->
            <div class="d-flex gap-3 justify-content-end">
                <a href="{{ route('home') }}" class="btn btn-secondary" style="border-radius: 2rem; padding: 0.75rem 2rem;">
                    <i class="bi bi-x-circle"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary" style="background-color: var(--clr-orange); border: none; border-radius: 2rem; padding: 0.75rem 2.5rem;">
                    <i class="bi bi-check-circle-fill"></i> Publicar Prenda
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
let selectedFiles = [];

function previewImages(event) {
    const files = Array.from(event.target.files);
    selectedFiles = [...selectedFiles, ...files];
    
    const container = document.getElementById('preview-container');
    container.innerHTML = '';
    
    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'preview-item';
            div.innerHTML = `
                <img src="${e.target.result}" alt="Preview ${index + 1}">
                <button type="button" class="btn-remove" onclick="removeImage(${index})">
                    <i class="bi bi-x-lg"></i>
                </button>
                <div class="preview-label">Foto ${index + 1}</div>
            `;
            container.appendChild(div);
        };
        
        reader.readAsDataURL(file);
    });
}

function removeImage(index) {
    selectedFiles.splice(index, 1);
    
    // Recrear FileList
    const dataTransfer = new DataTransfer();
    selectedFiles.forEach(file => dataTransfer.items.add(file));
    document.getElementById('imagenes').files = dataTransfer.files;
    
    // Actualizar preview
    const event = { target: { files: selectedFiles } };
    previewImages(event);
}

// Contador de caracteres para descripción
document.getElementById('descripcion').addEventListener('input', function() {
    const count = this.value.length;
    document.getElementById('char-count').textContent = count;
    
    if (count > 450) {
        document.getElementById('char-count').style.color = '#d63031';
    } else {
        document.getElementById('char-count').style.color = '#6c757d';
    }
});
</script>
@endsection