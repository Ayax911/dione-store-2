<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dione Store')</title>
    
    <!-- Favicon (Ícono de la pestaña) -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Toastify CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    
    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    
    @yield('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid px-4">
            <!-- Logo + Nombre -->
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <img src="{{ asset('images/logo.png') }}" 
                     alt="Dione Store Logo" 
                     style="height: 40px; width: auto; object-fit: contain;">
                <span>Dione Store</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="bi bi-house-door"></i> Inicio
                        </a>
                    </li>
                    
                    @auth
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('prendas.create') ? 'active' : '' }}" href="{{ route('prendas.create') }}">
                                <i class="bi bi-plus-circle"></i> Publicar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('carrito.index') ? 'active' : '' }}" href="{{ route('carrito.index') }}">
                                <i class="bi bi-cart"></i> Carrito 
                                <span class="badge bg-danger">{{ session('carrito_count', 0) }}</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="bi bi-person"></i> Mi perfil
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="bi bi-grid"></i> Mis publicaciones
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}">
                                        <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right"></i> Iniciar sesión
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('registro') ? 'active' : '' }}" href="{{ route('registro') }}">
                                <i class="bi bi-person-plus"></i> Registrarse
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Mensajes Flash -->
    @if(session('success'))
    <div class="container mt-3">
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 1rem;">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="container mt-3">
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 1rem;">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

    @if(session('info'))
    <div class="container mt-3">
        <div class="alert alert-info alert-dismissible fade show" role="alert" style="border-radius: 1rem;">
            <i class="bi bi-info-circle-fill"></i> {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

    <!-- Content -->
    <div class="container mt-4">
        @yield('content')
    </div>

    <!-- Footer -->
    <footer style="background: var(--clr-main); color: white; margin-top: 5rem; padding: 3rem 0;">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <!-- Logo en el footer también -->
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <img src="{{ asset('images/logo.png') }}" 
                             alt="Dione Store Logo" 
                             style="height: 50px; width: auto; filter: brightness(0) invert(1);">
                        <h5 style="color: var(--clr-orange); margin: 0;">Dione Store</h5>
                    </div>
                    <p style="opacity: 0.9;">Moda sostenible, segundo uso, primer impacto. Juntos reducimos nuestra huella de carbono.</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h6 style="color: var(--clr-orange);">Enlaces</h6>
                    <ul style="list-style: none; padding: 0;">
                        <li><a href="{{ route('home') }}" style="color: white; opacity: 0.8; text-decoration: none;">Inicio</a></li>
                        <li><a href="#" style="color: white; opacity: 0.8; text-decoration: none;">Nosotros</a></li>
                        <li><a href="#" style="color: white; opacity: 0.8; text-decoration: none;">Contacto</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-3">
                    <h6 style="color: var(--clr-orange);">Síguenos</h6>
                    <div style="font-size: 1.5rem;">
                        <a href="#" style="color: white; margin-right: 1rem;"><i class="bi bi-facebook"></i></a>
                        <a href="#" style="color: white; margin-right: 1rem;"><i class="bi bi-instagram"></i></a>
                        <a href="#" style="color: white;"><i class="bi bi-twitter"></i></a>
                    </div>
                </div>
            </div>
            <hr style="border-color: rgba(255,255,255,0.2);">
            <div class="text-center" style="opacity: 0.8;">
                <small>&copy; 2025 Dione Store. Todos los derechos reservados.</small>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Toastify JS -->
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Auto-hide alerts -->
    <script>
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
    
    @yield('scripts')
</body>
</html>