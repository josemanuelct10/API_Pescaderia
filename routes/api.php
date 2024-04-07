<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PescadoController;
use App\Http\Controllers\InicioSesionController;
use App\Http\Controllers\MariscoController;
use App\Http\Controllers\ProveedorController;



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







// Gestion del Inicio de Sesion
Route::post('user/create', [InicioSesionController::class, 'store']);
Route::post('user/login', [InicioSesionController::class, 'login']);



