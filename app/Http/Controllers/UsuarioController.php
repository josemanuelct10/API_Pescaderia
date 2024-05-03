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
        $usuarios = User::with('categoriaUsuario', 'gastos')->get();

        return response()->json($usuarios);
    }

    public function getById(int $id): JsonResponse{
        $usuario = User::with('categoriaUsuario', 'gastos')->findOrFail($id);
        return response()->json($usuario);
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

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $usuario = User::find($id);

            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado',
                ], 404);
            }

            $usuario->categoria_usuario_id = $request->categoria_usuario_id;

            $usuario->save();

            return response()->json([
                'success' => true,
                'data' => $usuario,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el usuario: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getByCategoria(int $id): JsonResponse
    {
        try {
            // Realizar la consulta para obtener los usuarios con el ID de categoría dado
            $usuarios = User::where('categoria_usuario_id', $id)->get();

            // Verificar si se encontraron usuarios
            if ($usuarios->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron usuarios para la categoría con ID: ' . $id,
                ], 404);
            }

            // Devolver la respuesta con los usuarios encontrados
            return response()->json($usuarios, 200);

        } catch (\Exception $e) {
            // Manejar cualquier excepción que pueda ocurrir durante la consulta
            return response()->json([
                'success' => false,
                'id' => $id,
                'message' => 'Error al obtener los usuarios: ' . $e->getMessage(),
            ], 500);
        }
    }

}
