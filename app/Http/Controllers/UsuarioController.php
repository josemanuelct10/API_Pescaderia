<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UsuarioController extends Controller
{
    // Metodo para devolver todos los usuarios
    public function getAll(): JsonResponse{
        // Carga la relación categoriaUsuario junto con los usuarios
        $usuarios = User::with('categoriaUsuario')->get();

        return response()->json($usuarios);
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $usuario = User::findOrFail($id);
            $usuario->delete();

            return response()->json([
                'success' => true
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'El usuario con el ID especificado no fue encontrado.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Se produjo un error al intentar eliminar el usuario.'
            ], 500);
        }
    }


}
