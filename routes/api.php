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
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\LineaController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\ChangePwdController;
use App\Http\Middleware\MiddlewareAdministrador;
use App\Http\Middleware\MiddlewareTrabajadores;
use App\Http\Middleware\MiddlewareClientes;




Route::middleware([MiddlewareTrabajadores::class])->group(function(){

    // Pescado Adminstrador
    Route::post('/pescados/create', [PescadoController::class, 'store']);
    Route::delete('/pescados/rm/{id}', [PescadoController::class, 'destroy']);
    Route::put('/pescados/update/{id}', [PescadoController::class, 'update']);

    // Marisco Administrador
    Route::post('/mariscos/create', [MariscoController::class, "store"]);
    Route::delete('/mariscos/rm/{id}', [MariscoController::class, 'destroy']);
    Route::put('/mariscos/update/{id}', [MariscoController::class, 'update']);

});



// Rutas protegidas solo accesibles para administradores
Route::middleware([MiddlewareAdministrador::class])->group(function () {

    // Rutas de proveedores
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
    Route::put('/usuarios/update/{id}', [UsuarioController::class, 'update']);
    Route::get('/usuarios/getByCategoria/{id}', [UsuarioController::class, 'getByCategoria']);

    // Gestion de Gastos del administrador
    Route::get('/gastos/show', [GastoController::class, 'index']);
    Route::post('/gastos/create', [GastoController::class, 'store']);
    Route::delete('/gastos/rm/{id}', [GastoController::class, 'destroy']);

    // Gestion de Ventas del Administrador
    Route::get('/ventas/show', [VentaController::class, 'index']);
    Route::post('/ventas/create', [VentaController::class, 'create']);
    Route::delete('/ventas/rm/{id}', [VentaController::class, 'destroy']);

    // Devolucion de ficheros PDF
    Route::get('/gastos/document/{nombreArchivo}', [GastoController::class, 'getNomina']);

});



Route::middleware([MiddlewareClientes::class])->group( function(){
    Route::post('user/logout', [InicioSesionController::class, 'logout']);
    Route::get('user/me', [InicioSesionController::class, 'me']);
    Route::put('user/update', [InicioSesionController::class, 'updateProfile']);
    Route::put('user/updatePwd', [InicioSesionController::class, 'updatePwd']);
    Route::get('/facturas/getByUser/{id}', [FacturaController::class, 'getByUser']);

    // Carrito
    Route::get('/carrito/comprobar/{id}', [CarritoController::class, 'exits']);
    Route::post('/carrito/create', [CarritoController::class, 'createCarrito']);
    Route::post('/carrito/newProducto', [LineaController::class, 'create']);
    Route::delete('/carrito/deleteProducto/{id}', [LineaController::class, 'delete']);
    Route::put('/carrito/actualizarLinea/{id}', [LineaController::class, 'update']);
    Route::delete('/carrito/deleteCarrito/{id}', [CarritoController::class, 'deleteCarrito']);

    // Gestion de Facturas
    Route::get('/facturas/show', [FacturaController::class, 'index']);
    Route::post('/facturas/create', [FacturaController::class, 'create']);
    Route::post('/facturas/linea/create', [LineaController::class, 'create']);
    Route::get('/facturas/{id}/pdf', [FacturaController::class, 'generarPDF']);
    Route::delete('/facturas/rm/{id}', [FacturaController::class, 'delete']);
    Route::get('/facturas/getById/{id}', [FacturaController::class, 'getById']);

    Route::get('/pescados/show', [PescadoController::class, 'index']);
    Route::get('/pescados/getById/{id}', [PescadoController::class, 'show']);
    Route::put('/pescados/updateCantidad/{id}', [PescadoController::class, 'updateCantidad']);

    Route::get('/mariscos/show', [MariscoController::class, "index"]);
    Route::put('/mariscos/updateCantidad/{id}', [MariscoController::class, 'updateCantidad']);
    Route::get('/mariscos/getById/{id}', [MariscoController::class, 'show']);
});




// Gestion del Inicio de Sesion
Route::post('user/create', [InicioSesionController::class, 'store']);
Route::post('user/login', [InicioSesionController::class, 'login']);


// Cambio de Contraseña
Route::post('user/resetPwd', [InicioSesionController::class, 'resetPassword']);
Route::post('user/validate-reset-token', [ChangePwdController::class, 'validateResetToken']);
Route::post('user/changePwd', [ChangePwdController::class, 'changePwd']);

Route::get('api/storage/images/{filename}', function ($filename) {
    $path = storage_path('/storage/app/public/images/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    return response()->file($path);
});

