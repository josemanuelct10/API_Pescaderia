<?php

use App\Http\Controllers\CategoriasUsuarioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PescadoController;
use App\Http\Controllers\InicioSesionController;
use App\Http\Controllers\MariscoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\VentaController;




// Pescado Adminstrador
Route::get('/pescados/show', [PescadoController::class, 'index']);
Route::post('/pescados/create', [PescadoController::class, 'store']);
Route::delete('/pescados/rm/{id}', [PescadoController::class, 'destroy']);
Route::get('/pescados/getById/{id}', [PescadoController::class, 'show']);
Route::put('/pescados/update/{id}', [PescadoController::class, 'update']);

// Marisco Administrador
Route::get('/mariscos/show', [MariscoController::class, "index"]);
Route::post('/mariscos/create', [MariscoController::class, "store"]);
Route::delete('/mariscos/rm/{id}', [MariscoController::class, 'destroy']);
Route::get('/mariscos/getById/{id}', [MariscoController::class, 'show']);
Route::put('/mariscos/update/{id}', [MariscoController::class, 'update']);

// Proveedores Administrador
Route::get('/proveedores/show', [ProveedorController::class, 'index']);
Route::post('/proveedores/create', [ProveedorController::class, 'store']);
Route::delete('/proveedores/rm/{id}', [ProveedorController::class, 'destroy']);
Route::get('/proveedores/getById/{id}', [ProveedorController::class, 'show']);
Route::put('/proveedores/update/{id}', [ProveedorController::class, 'update']);

// Categorias de Usuarios Administrador
Route::get('/categorias-usuarios/show', [CategoriasUsuarioController::class, 'index']);
Route::post('/categorias-usuarios/create', [CategoriasUsuarioController::class, 'store']);
Route::delete('/categorias-usuarios/rm/{id}', [CategoriasUsuarioController::class, 'destroy']);
Route::get('/categorias-usuarios/check/{id}', [CategoriasUsuarioController::class, 'checkUsuarios']);

// Gestion de Usuarios del Administrador
Route::get('/usuarios/show', [UsuarioController::class, 'getAll']);
Route::delete('/usuarios/rm/{id}', [UsuarioController::class, 'destroy']);
Route::get('/usuarios/getById/{id}', [UsuarioController::class, 'getById']);
Route::put('/usuarios/update/{id}', [UsuarioController::class, 'update']);
Route::get('/usuarios/getByCategoria/{id}', [UsuarioController::class, 'getByCategoria']);


// Gestion del Inicio de Sesion
Route::post('user/create', [InicioSesionController::class, 'store']);
Route::post('user/login', [InicioSesionController::class, 'login']);
Route::post('user/logout', [InicioSesionController::class, 'logout']);
Route::post('user/me', [InicioSesionController::class, 'me']);

// Gestion de Gastos del administrador
Route::get('/gastos/show', [GastoController::class, 'index']);
Route::post('/gastos/create', [GastoController::class, 'store']);
Route::delete('/gastos/rm/{id}', [GastoController::class, 'destroy']);

// Devolucion de ficheros PDF
Route::get('/gastos/{nombreArchivo}', [GastoController::class, 'getNomina']);


// Gestion de Ventas del Administrador

Route::get('/ventas/show', [VentaController::class, 'index']);
Route::post('/ventas/create', [VentaController::class, 'create']);
Route::delete('/ventas/rm/{id}', [VentaController::class, 'destroy']);





