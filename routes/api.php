<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PescadoController;
use App\Http\Controllers\InicioSesionController;


// Pescado Adminstrador
Route::get('/pescados/show', [PescadoController::class, 'index']);
Route::post('/pescados/create', [PescadoController::class, 'store']);
Route::delete('/pescados/rm/{id}', [PescadoController::class, 'destroy']);
Route::get('/pescados/getById/{id}', [PescadoController::class, 'show']);


// Gestion del Inicio de Sesion
Route::post('user/create', [InicioSesionController::class, 'store']);
Route::post('user/login', [InicioSesionController::class, 'login']);



