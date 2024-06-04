<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UsuarioController extends Controller
{
    /**
     * Obtiene todos los usuarios de la base de datos con sus categorías de usuario y gastos asociados.
     *
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON que contiene todos los usuarios con sus categorías de usuario y gastos asociados.
     */
    public function getAll(): JsonResponse {
        // Cargar la relación 'categoriaUsuario' junto con los usuarios
        // También carga la relación 'gastos' para cada usuario
        $usuarios = User::with('categoriaUsuario', 'gastos')->get();

        // Devolver una respuesta JSON que contiene todos los usuarios
        return response()->json($usuarios);
    }

    /**
     * Obtiene un usuario específico de la base de datos con su categoría de usuario, gastos y facturas asociadas.
     *
     * @param int $id El ID del usuario que se desea recuperar.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON que contiene el usuario con su categoría de usuario, gastos y facturas asociadas.
     */
    public function getById(int $id): JsonResponse{
        // Utiliza el método with para cargar las relaciones 'categoriaUsuario', 'gastos' y 'facturas' junto con el usuario
        $usuario = User::with('categoriaUsuario', 'gastos', 'facturas')->findOrFail($id);

        // Devuelve una respuesta JSON que contiene el usuario
        return response()->json($usuario);
    }


    /**
     * Elimina un usuario de la base de datos según su ID.
     *
     * @param int $id El ID del usuario que se desea eliminar.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON que indica si la eliminación fue exitosa o no.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            // Busca el usuario con el ID especificado
            $usuario = User::findOrFail($id);

            // Elimina el usuario
            $usuario->delete();

            // Devuelve una respuesta JSON indicando que la eliminación fue exitosa
            return response()->json([
                'success' => true
            ], 200);
        } catch (ModelNotFoundException $e) {
            // Maneja la excepción si el usuario no fue encontrado y devuelve una respuesta JSON con un mensaje de error
            return response()->json([
                'success' => false,
                'error' => 'El usuario con el ID especificado no fue encontrado.'
            ], 404);
        } catch (\Exception $e) {
            // Maneja cualquier otra excepción y devuelve una respuesta JSON con un mensaje de error
            return response()->json([
                'success' => false,
                'error' => 'Se produjo un error al intentar eliminar el usuario.'
            ], 500);
        }
    }


    /**
     * Actualiza un usuario en la base de datos según su ID.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP con los datos del usuario a actualizar.
     * @param int $id El ID del usuario que se desea actualizar.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON que indica si la actualización fue exitosa o no.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            // Busca el usuario con el ID especificado
            $usuario = User::find($id);

            // Verifica si el usuario no se encontró
            if (!$usuario) {
                return response()->json([
                    'response' => 0,
                    'success' => false,
                    'message' => 'Usuario no encontrado',
                ], 200);
            }

            // Actualiza el ID de la categoría del usuario con el valor recibido en la solicitud
            $usuario->categoria_usuario_id = $request->categoria_usuario_id;

            // Guarda los cambios en la base de datos
            $usuario->save();

            // Devuelve una respuesta JSON indicando que la actualización fue exitosa, junto con los datos actualizados del usuario
            return response()->json([
                'response' => 1,
                'success' => true,
                'data' => $usuario,
            ], 200);
        } catch (\Exception $e) {
            // Maneja cualquier excepción que ocurra durante el proceso y devuelve una respuesta JSON con un mensaje de error
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el usuario: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtiene todos los usuarios que pertenecen a una categoría de usuario específica.
     *
     * @param int $id El ID de la categoría de usuario.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON que contiene los usuarios encontrados o un mensaje de error si no se encuentran usuarios o si ocurre un error durante la consulta.
     */
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
