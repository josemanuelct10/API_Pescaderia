<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;
use App\Models\categoriaUsuario;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; // Importa JsonResponse desde Illuminate\Http
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\QueryException;

class CategoriasUsuarioController extends Controller
{
    /**
     * Obtiene todas las categorías de usuario junto con sus usuarios relacionados.
     *
     * @return JsonResponse Respuesta en formato JSON con todas las categorías de usuario y sus usuarios relacionados.
     */
    public function index(): JsonResponse
    {
        // Cargar todas las categorías de usuario junto con sus relaciones 'users'
        $categoriasConUsuarios = categoriaUsuario::with('users')->get();

        // Devolver la respuesta JSON con todas las categorías de usuario y sus usuarios relacionados
        return response()->json($categoriasConUsuarios, 200);
    }

    /**
     * Crea una nueva categoría de usuario con los datos proporcionados en la solicitud.
     *
     * @param Request $request Solicitud HTTP que contiene los datos para crear la nueva categoría de usuario.
     * @return JsonResponse Respuesta en formato JSON que indica si la creación fue exitosa y contiene los datos de la nueva categoría de usuario o un mensaje de error.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Crea una nueva categoría de usuario con los datos proporcionados en la solicitud
            $categoriaUsuario = categoriaUsuario::create($request->all());

            // Retorna una respuesta JSON indicando que la creación fue exitosa y contiene los datos de la nueva categoría de usuario, con un código de estado 201 (CREATED)
            return response()->json([
                'success' => true,
                'data' => $categoriaUsuario
            ], Response::HTTP_CREATED);
        } catch (QueryException $exception) {
            // Si ocurre una excepción de consulta, retorna una respuesta JSON con un mensaje de error y un código de estado 500 (INTERNAL SERVER ERROR)
            return response()->json([
                'success' => false,
                'message' => "Error al crear la categoría. Por favor, intentalo de nuevo."
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Elimina una categoría de usuario por su ID.
     *
     * @param int $id ID de la categoría de usuario a eliminar.
     * @return JsonResponse Respuesta en formato JSON que indica si la eliminación fue exitosa o si hubo algún problema.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            // Buscar la categoría de usuario por su ID, lanzando una excepción si no se encuentra
            $categoria = categoriaUsuario::findOrFail($id);

            // Eliminar la categoría
            $categoria->delete();

            // Retorna una respuesta JSON indicando que la eliminación fue exitosa, con un código de estado 200 (OK)
            return response()->json([
                'success' => true
            ], 200);
        } catch (ModelNotFoundException $e) {
            // Si no se encuentra la categoría, retorna una respuesta JSON con un mensaje de error y un código de estado 404 (NOT FOUND)
            return response()->json([
                'success' => false,
                'error' => 'La categoría con el ID especificado no fue encontrada.'
            ], 404);
        } catch (\Exception $e) {
            // En caso de una excepción genérica, retorna una respuesta JSON con un mensaje de error y un código de estado 500 (INTERNAL SERVER ERROR)
            return response()->json([
                'success' => false,
                'error' => 'Se produjo un error al intentar eliminar la categoría.'
            ], 500);
        }
    }

    /**
     * Verifica si existen usuarios que pertenecen a una categoría de usuario específica.
     *
     * @param int $id ID de la categoría de usuario.
     * @return bool Retorna `true` si existen usuarios con la categoría de usuario especificada, `false` en caso contrario.
    */
    public function checkUsuarios(int $id): bool
    {
        // Verificar si existen usuarios con el ID de la categoría de usuario proporcionada
        $usuarios = User::where('categoria_usuario_id', $id)->exists();

        // Retornar `true` si existen usuarios, `false` en caso contrario
        return $usuarios ? true : false;
    }

}
