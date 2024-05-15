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
    public function exits(int $idUser): JsonResponse
    {
        // Búsqueda del usuario
        $usuario = User::find($idUser);

        if (!$usuario){
            return response()->json([
                'response'=> 0,
                'message'=> 'El usuario no existe'
            ], 404);
        }

        if ($usuario->carrito()->exists()){
            // Cargar el carrito y las relaciones pescado y marisco de cada línea
            $carrito = $usuario->carrito->load('lineas.pescado', 'lineas.marisco');

            return response()->json([
                'response' => 1,
                'carrito' => $carrito,
                'lineasCarrito' => $carrito->lineas->count()
            ], 200);
        }
        else {
            return response()->json([
                'response' => 0,
                'message'=> 'El usuario no tiene carrito'
            ], 200);
        }
    }


    public function createCarrito(Request $request){
        $carrito = Carrito::create($request->all());

        return response()->json([
            'success' => true,
            'response' => 1,
            'data' => $carrito
        ], Response::HTTP_CREATED);
    }

    public function deleteCarrito($id): JsonResponse {
        try {
            $carrito = Carrito::findOrFail($id);

            // Verificar si hay líneas asociadas al carrito
            if ($carrito->lineas()->exists()) {
                return response()->json([
                    'success' => false,
                    'response' => -1,
                    'message' => "No se puede eliminar el carrito porque tiene líneas asociadas."
                ], Response::HTTP_BAD_REQUEST);
            }

            $carrito->delete();

            return response()->json([
                'success' => true,
                'response' => 1,
                'message' => "Carrito eliminado correctamente."
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'response' => 0,
                'message' => "Error al eliminar el carrito. Por favor, inténtalo de nuevo."
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


}
