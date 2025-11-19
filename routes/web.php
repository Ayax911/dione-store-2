<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PrendaController;

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
    
    // Rutas del carrito (placeholder temporal)
    Route::get('/carrito', function() {
        return redirect()->route('home')->with('info', 'Carrito en desarrollo');
    })->name('carrito.index');
    
    Route::post('/carrito/agregar/{id}', function($id) {
        return redirect()->back()->with('info', 'Funcionalidad de carrito en desarrollo');
    })->name('carrito.agregar');
    
    Route::delete('/carrito/eliminar/{id}', function($id) {
        return redirect()->back()->with('info', 'Funcionalidad de carrito en desarrollo');
    })->name('carrito.eliminar');
});