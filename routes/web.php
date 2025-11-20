<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PrendaController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\PedidoController;

// Redirigir raíz al login
Route::get('/', function () {
    return view('login');
})->name('inicio');

// Rutas de autenticación
Route::view('/login', "login")->name('login');
Route::view('/registro', "register")->name('registro');

Route::post('/validar-registro', [LoginController::class, 'register'])->name('validar-registro');
Route::post('/iniciar-sesion', [LoginController::class, 'login'])->name('iniciar-sesion');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// Rutas públicas de prendas
Route::get('/home', [PrendaController::class, 'index'])->name('home');
Route::get('/prendas/{id}', [PrendaController::class, 'show'])->name('prendas.show');

// Rutas protegidas (requieren autenticación)
Route::middleware('auth')->group(function () {
    // CRUD de prendas
    Route::get('/prendas/crear', [PrendaController::class, 'create'])->name('prendas.create');
    Route::post('/prendas', [PrendaController::class, 'store'])->name('prendas.store');
    Route::get('/prendas/{id}/editar', [PrendaController::class, 'edit'])->name('prendas.edit');
    Route::put('/prendas/{id}', [PrendaController::class, 'update'])->name('prendas.update');
    Route::delete('/prendas/{id}', [PrendaController::class, 'destroy'])->name('prendas.destroy');
    
    // Mis publicaciones
    Route::get('/mis-publicaciones', [PrendaController::class, 'misPublicaciones'])->name('mis-publicaciones');
    
    // Carrito
    Route::post('/carrito/agregar/{prenda}', [CarritoController::class, 'agregar'])->name('carrito.agregar');
    Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
    Route::patch('/carrito/{id}', [CarritoController::class, 'actualizar'])->name('carrito.actualizar');
    Route::delete('/carrito/{id}', [CarritoController::class, 'eliminar'])->name('carrito.eliminar');
    Route::post('/carrito/vaciar', [CarritoController::class, 'vaciar'])->name('carrito.vaciar');
    Route::post('/carrito/checkout', [CarritoController::class, 'checkout'])->name('carrito.checkout');
    
    // Pedidos (Ver compras y ventas)
    Route::get('/mis-compras', [PedidoController::class, 'misCompras'])->name('pedidos.misCompras');
    Route::get('/mis-ventas', [PedidoController::class, 'misVentas'])->name('pedidos.misVentas');
    Route::get('/pedido/{id}', [PedidoController::class, 'show'])->name('pedidos.show');
});