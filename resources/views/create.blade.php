@extends('layouts.app')

@section('title', 'Publicar Prenda - Dione Store')

@section('styles')
<style>
    .form-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .form-label {
        color: var(--clr-main);
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .form-control, .form-select {
        border: 2px solid var(--clr-gray);
        border-radius: 0.5rem;
        padding: 0.75rem;
        transition: border-color 0.3s;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--clr-orange);
        box-shadow: 0 0 0 0.2rem rgba(255, 122, 89, 0.25);
    }

    .btn-publicar {
        background-color: var(--clr-orange);
        color: white;
        border: none;
        padding: 1rem 3rem;
        border-radius: 2rem;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        width: 100%;
        margin-top: 1rem;
    }

    .btn-publicar:hover {
        background-color: var(--clr-main);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .preview-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .preview-item {
        position: relative;
        aspect-ratio: 1;
        border-radius: 0.5rem;
        overflow: hidden;
        border: 2px solid var(--clr-gray);
    }

    .preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .preview-item .btn-remove {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        background-color: rgba(255, 122, 89, 0.9);
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .file-upload-area {
        border: 2px dashed var(--clr-main);
        border-radius: 1rem;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        background-color: #f8f9fa;
    }

    .file-upload-area:hover {
        border-color: var(--clr-orange);
        background-color: #fff;
    }

    .file-upload-area i {
        font-size: 3rem;
        color: var(--clr-main);
        margin-bottom: 1rem;
    }
</style>
@endsection

@section('content')
<div class="mb-3">
    <a href="{{ route('home') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="form-container">
    <h2 class="titulo-principal text-center mb-4">
        <i class="bi bi-plus-circle"></i> Publicar Nueva Prenda
    </h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('prendas.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="titulo" class="form-label">
                <i class="bi bi-tag"></i> Título de la prenda *
            </label>
            <input type="text" 
                   class="form-control" 
                   id="titulo" 
                   name="titulo" 
                   value="{{ old('titulo') }}" 
                   placeholder="Ej: Camiseta Nike Original" 
                   required 
                   maxlength="50">
            <small class="text-muted">Máximo 50 caracteres</small>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="categoria_id" class="form-label">
                    <i class="bi bi-list"></i> Categoría *
                </label>
                <select class="form-select" id="categoria_id" name="categoria_id" required>
                    <option value="">Selecciona una categoría</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                            {{ $categoria->tipo_prenda }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 mb-3">
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

            <div class="col-md-3 mb-3">
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
                       step="0.01">
            </div>
        </div>

        <div class="mb-3">
            <label for="material" class="form-label">
                <i class="bi bi-palette"></i> Material *
            </label>
            <input type="text" 
                   class="form-control" 
                   id="material" 
                   name="material" 
                   value="{{ old('material') }}" 
                   placeholder="Ej: Algodón 100%, Poliéster" 
                   required 
                   maxlength="50">
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">
                <i class="bi bi-text-paragraph"></i> Descripción *
            </label>
            <textarea class="form-control" 
                      id="descripcion" 
                      name="descripcion" 
                      rows="4" 
                      placeholder="Describe tu prenda: estado, detalles, razón de venta, etc." 
                      required 
                      maxlength="500">{{ old('descripcion') }}</textarea>
            <small class="text-muted">Máximo 500 caracteres</small>
        </div>

        <div class="mb-4">
            <label class="form-label">
                <i class="bi bi-images"></i> Imágenes de la prenda
            </label>
            
            <div class="file-upload-area" onclick="document.getElementById('imagenes').click()">
                <i class="bi bi-cloud-upload"></i>
                <p class="mb-0"><strong>Click para subir imágenes</strong></p>
                <small class="text-muted">Puedes subir múltiples imágenes (JPG, PNG, GIF - Máx 2MB c/u)</small>
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

        <button type="submit" class="btn-publicar">
            <i class="bi bi-check-circle"></i> Publicar Prenda
        </button>
    </form>
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
                    <i class="bi bi-x"></i>
                </button>
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
</script>
@endsection