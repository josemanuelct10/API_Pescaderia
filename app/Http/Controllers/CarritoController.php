<?php

namespace App\Http\Controllers;
use App\Models\Carrito;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; // Importa JsonResponse desde Illuminate\Http
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\QueryException;

class CarritoController extends Controller
{

/**
 * Verifica si un usuario existe y si tiene un carrito asociado.
 *
 * @param int $idUser ID del usuario.
 * @return JsonResponse Respuesta en formato JSON que indica si el usuario existe y si tiene un carrito.
*/
public function exits(int $idUser): JsonResponse
{
    // Búsqueda del usuario por su ID
    $usuario = User::find($idUser);

    // Si el usuario no existe, retorna una respuesta JSON con un mensaje de error y un código de estado 404
    if (!$usuario) {
        return response()->json([
            'response' => 0,
            'message' => 'El usuario no existe'
        ], 404);
    }

    // Verifica si el usuario tiene un carrito asociado
    if ($usuario->carrito()->exists()) {
        // Cargar el carrito y las relaciones pescado y marisco de cada línea del carrito
        $carrito = $usuario->carrito->load('lineas.pescado', 'lineas.marisco');

        // Retorna una respuesta JSON con el carrito y el conteo de líneas del carrito, con un código de estado 200
        return response()->json([
            'response' => 1,
            'carrito' => $carrito,
            'lineasCarrito' => $carrito->lineas->count()
        ], 200);
    } else {
        // Si el usuario no tiene un carrito, retorna una respuesta JSON con un mensaje y un código de estado 200
        return response()->json([
            'response' => 0,
            'message' => 'El usuario no tiene carrito'
        ], 200);
    }
}



/**
 * Crea un nuevo carrito con los datos proporcionados en la solicitud.
 *
 * @param Request $request Solicitud HTTP que contiene los datos para crear el carrito.
 * @return JsonResponse Respuesta en formato JSON que indica si la creación fue exitosa y contiene los datos del carrito creado.
 */
public function createCarrito(Request $request)
{
    // Crea un nuevo carrito con los datos de la solicitud
    $carrito = Carrito::create($request->all());

    // Retorna una respuesta JSON indicando el éxito de la operación y los datos del carrito creado, con un código de estado 201 (CREATED)
    return response()->json([
        'success' => true,
        'response' => 1,
        'data' => $carrito
    ], Response::HTTP_CREATED);
}

/**
 * Elimina el carrito asociado a un usuario dado.
 *
 * @param int $id ID del usuario.
 * @return JsonResponse Respuesta en formato JSON que indica si la eliminación fue exitosa o si hubo algún problema.
 */
public function deleteCarrito($id): JsonResponse {
    try {
        // Buscar el carrito asociado al usuario por su ID
        $carrito = Carrito::where('user_id', $id)->first();

        // Si no se encuentra el carrito, retorna una respuesta JSON con un mensaje de error y un código de estado 404
        if (!$carrito) {
            return response()->json([
                'success' => false,
                'response' => -1,
                'message' => "No se encontró ningún carrito para el usuario proporcionado."
            ], Response::HTTP_NOT_FOUND);
        }

        // Verificar si hay líneas asociadas al carrito
        if ($carrito->lineas()->exists()) {
            // Si hay líneas asociadas, retorna una respuesta JSON con un mensaje de error y un código de estado 400
            return response()->json([
                'success' => false,
                'response' => -2,
                'message' => "No se puede eliminar el carrito porque tiene líneas asociadas."
            ], Response::HTTP_BAD_REQUEST);
        }

        // Eliminar el carrito
        $carrito->delete();

        // Retorna una respuesta JSON indicando que la eliminación fue exitosa, con un código de estado 200
        return response()->json([
            'success' => true,
            'response' => 1,
            'message' => "Carrito eliminado correctamente."
        ], Response::HTTP_OK);
    } catch (Exception $e) {
        // En caso de una excepción, retorna una respuesta JSON con un mensaje de error y un código de estado 500
        return response()->json([
            'success' => false,
            'response' => 0,
            'message' => "Error al eliminar el carrito. Por favor, inténtalo de nuevo."
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

}
