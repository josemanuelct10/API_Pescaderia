<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Response;

class VentaController extends Controller
{
    /**
     * Método que devuelve un Json con todas las ventas encontradas en la base de datos
     */
    public function index(): JsonResponse
    {
        $ventas = Venta::all(); // Cambiado "venta" a "Venta"

        return response()->json($ventas, 200);
    }

    public function create(Request $request): JsonResponse
    {
        try {
            // Comprobar si ya existe una venta con la referencia proporcionada
            $ventaExistente = Venta::where('referencia', $request->referencia)->first();

            if ($ventaExistente) {
                // Si ya existe una venta con la referencia, retornar un JSON con success false y response en 1
                return response()->json([
                    'success' => false,
                    'response' => 0
                ], Response::HTTP_OK);
            }

            // Si no existe, crear la venta
            $venta = Venta::create($request->all());

            return response()->json([
                'success' => true,
                'response' => 1,
                'data' => $venta
            ], Response::HTTP_CREATED);
        } catch(QueryException $exception) {
            return response()->json([
                'success' => false,
                'response' => -1,
                'message' => "Error al crear la venta. Por favor, inténtalo de nuevo."
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $venta = venta::findOrFail($id);
            $venta->delete();

            return response()->json([
                'success' => true,
                'response' => 1
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'La venta especificada no fue encontrada.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Se produjo un error al intentar eliminar la venta.'
            ], 500);
        }
    }

}

