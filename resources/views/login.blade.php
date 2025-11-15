@extends('layouts.app')

@section('title', 'Login')

@section('content')
<main class="login-container">
    <div class="login-card">
        <h2 class="text-center mb-4">Iniciar Sesión</h2>
        
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        
        <form method="post" action="{{ route('iniciar-sesion') }}">
            @csrf

            <div class="mb-3">
                <label for="emailInput" class="form-label">Email</label>
                <input type="email" class="form-control" id="emailInput" name="email" 
                       value="{{ old('email') }}" placeholder="email@ejemplo.com" required>
            </div>

            <div class="mb-3">
                <label for="passwordInput" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="passwordInput" name="password" 
                       placeholder="********" required>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="rememberCheck" name="remember">
                <label class="form-check-label" for="rememberCheck">
                    Mantener sesión iniciada
                </label>
            </div>

            <div class="mb-3 text-center">
                <p>¿No tienes cuenta? 
                    <a href="{{ route('registro') }}">Regístrate</a>
                </p>
            </div>

            <button type="submit" class="btn btn-primary">Acceder</button>
        </form>
    </div>
</main>
@endsection