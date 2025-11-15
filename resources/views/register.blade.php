@extends('layouts.app')

@section('title', 'Registro')

@section('content')
<main class="login-container">
    <div class="login-card">
        <h2 class="text-center mb-4">Crear Cuenta</h2>
        
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        
        <form method="post" action="{{ route('validar-registro') }}">
            @csrf

            <div class="mb-3">
                <label for="userInput" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="userInput" name="name" 
                       value="{{ old('name') }}" placeholder="Tu nombre" required autocomplete="off">
            </div>

            <div class="mb-3">
                <label for="emailInput" class="form-label">Email</label>
                <input type="email" class="form-control" id="emailInput" name="email" 
                       value="{{ old('email') }}" placeholder="tuemail@ejemplo.com" required autocomplete="off">
            </div>

            <div class="mb-3">
                <label for="passwordInput" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="passwordInput" name="password" 
                       placeholder="********" required>
            </div>

            <div class="mb-3 text-center">
                <p>¿Ya tienes cuenta? 
                    <a href="{{ route('login') }}">Inicia sesión</a>
                </p>
            </div>

            <button type="submit" class="btn btn-primary">Registrarse</button>
        </form>
    </div>
</main>
@endsection