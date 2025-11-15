@extends('layouts.app')

@section('title', 'Página Privada')

@section('content')
<header class="d-flex justify-content-between align-items-center p-3">
    <div>
        <h3>Bienvenido, {{ Auth::user()->name }}</h3>
    </div>
    <div>
        <a href="{{ route('logout') }}">
            <button type="button" class="btn btn-primary">Cerrar Sesión</button>
        </a>
    </div>
</header>

<main class="container mt-4">
    <h1>Esta es la página de prueba</h1>
    <p>Contenido privado solo para usuarios autenticados.</p>
</main>
@endsection