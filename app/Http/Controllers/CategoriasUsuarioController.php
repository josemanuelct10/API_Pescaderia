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
     * Metodo que devuelve un Json con todas las categorias encontrados en la base de datos
     */
    public function index(): JsonResponse
    {
        return response()->json(categoriaUsuario::all(), 200);
    }

        /**
    * Método que crea nuevos mariscos
    * Metodo que Recibe un Request Personalizado de Marisco
    * Devuelve una Respuesta Json
    */
    public function store(Request $request): JsonResponse
    {
        try{
            $categoriaUsuario = categoriaUsuario::create($request->all());
            return response()->json([
                'success' => true,
                'data' => $categoriaUsuario
            ], Response::HTTP_CREATED);
        } catch(QueryException $exception){
            return response()->json([
                'success' => false,
                'message' => "Error al crear la categoría. Por favor, intentalo de nuevo."
            ], Response:: HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $categoria = categoriaUsuario::findOrFail($id);
            $categoria->delete();

            return response()->json([
                'success' => true
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'La categoría con el ID especificado no fue encontrado.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Se produjo un error al intentar eliminar la categoría.'
            ], 500);
        }
    }

    public function checkUsuarios(int $id){
        $usuarios = User::where('categoria_usuario_id', $id)->exists();
        return $usuarios ? true : false;
    }
}
