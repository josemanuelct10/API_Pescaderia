<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; // Importa JsonResponse desde Illuminate\Http
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;
use App\Models\Linea;
use Illuminate\Support\Str;

class LineaController extends Controller
{
    /**
     * Crea una nueva línea de factura.
     *
     * @param  \Illuminate\Http\Request  $request Los datos de la solicitud.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON indicando el resultado de la creación.
     */
    public function create(Request $request): JsonResponse
    {
        try {
            // Crear una nueva línea de factura con los datos recibidos en la solicitud
            $linea = Linea::create($request->all());

            // Devolver una respuesta JSON exitosa con los datos de la línea de factura recién creada
            return response()->json([
                'success' => true,
                'response' => 1,
                'data' => $linea
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            // Manejar cualquier excepción y devolver una respuesta JSON de error con un mensaje descriptivo
            return response()->json([
                'success' => false,
                'response' => 0,
                'message' => "Error al crear la línea. Por favor, inténtalo de nuevo."
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Actualiza una línea de factura.
     *
     * @param  \Illuminate\Http\Request  $request Los datos de la solicitud.
     * @param  int  $id El ID de la línea de factura que se va a actualizar.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON indicando el resultado de la actualización.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            // Buscar la línea de factura por su ID
            $linea = Linea::findOrFail($id);

            // Actualizar los datos de la línea de factura con los datos recibidos en la solicitud
            $linea->update($request->all());

            // Devolver una respuesta JSON exitosa con los datos actualizados de la línea de factura
            return response()->json([
                'success' => true,
                'response' => 1,
                'data' => $linea
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            // Manejar cualquier excepción y devolver una respuesta JSON de error con un mensaje descriptivo
            return response()->json([
                'success' => false,
                'response' => 0,
                'message' => "Error al actualizar la línea. Por favor, inténtalo de nuevo."
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    /**
     * Elimina una línea de factura.
     *
     * @param  int  $id El ID de la línea de factura que se va a eliminar.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON indicando el resultado de la eliminación.
     */
    public function delete(int $id)
    {
        try {
            // Buscar la línea de factura por su ID
            $linea = Linea::findOrFail($id);

            // Eliminar la línea de factura
            $linea->delete();

            // Devolver una respuesta JSON exitosa
            return response()->json([
                'success' => true,
                'response' => 1
            ], 200);
        } catch (Exception $e) {
            // Manejar cualquier excepción y devolver una respuesta JSON de error
            return response()->json([
                'success' => false,
                'response' => 0,
                'message' => "Error al eliminar la línea. Por favor, inténtalo de nuevo."
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
