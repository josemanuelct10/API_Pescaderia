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
     * Obtiene todas las ventas.
     *
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON con todas las ventas.
     */
    public function index(): JsonResponse
    {
        $ventas = Venta::all(); // Obtiene todas las ventas de la base de datos

        // Devuelve una respuesta JSON con todas las ventas y un código de respuesta HTTP 200
        return response()->json($ventas, 200);
    }


    /**
     * Crea una nueva venta.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP con los datos de la venta.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON indicando si la venta se creó correctamente o si ocurrió algún error.
     */
    public function create(Request $request): JsonResponse
    {
        try {
            // Comprobar si ya existe una venta con la referencia proporcionada
            $ventaExistente = Venta::where('referencia', $request->referencia)->first();

            if ($ventaExistente) {
                // Si ya existe una venta con la referencia, retornar un JSON con éxito falso y response en 1
                return response()->json([
                    'success' => false,
                    'response' => 0
                ], Response::HTTP_OK);
            }

            // Si no existe, crear la venta
            $venta = Venta::create($request->all());

            // Devolver una respuesta JSON indicando éxito y los datos de la venta creada
            return response()->json([
                'success' => true,
                'response' => 1,
                'data' => $venta
            ], Response::HTTP_CREATED);
        } catch(QueryException $exception) {
            // Manejar cualquier excepción de consulta y devolver una respuesta JSON de error
            return response()->json([
                'success' => false,
                'response' => -1,
                'message' => "Error al crear la venta. Por favor, inténtalo de nuevo."
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Elimina una venta por su ID.
     *
     * @param int $id El ID de la venta a eliminar.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON indicando si la venta se eliminó correctamente o si ocurrió algún error.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            // Buscar la venta por su ID
            $venta = Venta::findOrFail($id);

            // Eliminar la venta
            $venta->delete();

            // Devolver una respuesta JSON indicando éxito
            return response()->json([
                'success' => true,
                'response' => 1
            ], 200);
        } catch (ModelNotFoundException $e) {
            // Manejar el caso en que la venta no se encuentre
            return response()->json([
                'success' => false,
                'error' => 'La venta especificada no fue encontrada.'
            ], 404);
        } catch (\Exception $e) {
            // Manejar cualquier otro error que pueda ocurrir durante la eliminación
            return response()->json([
                'success' => false,
                'error' => 'Se produjo un error al intentar eliminar la venta.'
            ], 500);
        }
    }

}

