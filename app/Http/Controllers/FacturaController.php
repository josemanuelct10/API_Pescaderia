<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;
use App\Models\Factura;
use App\Models\Linea;
use App\Models\Pescado;
use App\Models\Marisco;
use Illuminate\Support\Str;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\DB;

use Dompdf\Options;

class FacturaController extends Controller
{
    /**
     * Obtiene todas las facturas con información de usuario asociada.
     *
     * @return JsonResponse Respuesta en formato JSON que contiene las facturas y su información de usuario asociada, o un mensaje de error si ocurre algún problema.
     */
    public function index(): JsonResponse
    {
        try {
            // Obtener todas las facturas con información de usuario asociada
            $facturas = Factura::with(['user'])->get();

            // Retornar una respuesta JSON con las facturas y un código de estado 200 (OK)
            return response()->json([
                'status' => 1,
                'data' => $facturas
            ], 200);
        } catch (\Exception $e) {
            // En caso de error, retornar una respuesta JSON con un mensaje de error y un código de estado 500 (INTERNAL SERVER ERROR)
            return response()->json([
                'status' => 0,
                'message' => 'Error al obtener las facturas'
            ], 500);
        }
    }



    /**
     * Crea una nueva factura.
     *
     * @param Request $request Solicitud HTTP que contiene los datos de la factura a crear.
     * @return JsonResponse Respuesta en formato JSON que indica si la factura se creó correctamente o si ocurrió un error.
     */
    public function create(Request $request): JsonResponse
    {
        try {
            // Obtener los datos de la solicitud
            $data = $request->all();

            // Crear un nuevo objeto Factura
            $factura = new Factura();

            // Asignar los valores uno por uno
            $factura->fecha = $data['fecha'];
            $factura->horaRecogida = $data['horaRecogida'];
            $factura->metodoPago = $data['metodoPago'];
            $factura->precioFactura = $data['precioFactura'];
            $factura->referencia = $data['referencia'];
            $factura->user_id = $data['user_id'];

            // Guardar la factura en la base de datos
            $factura->save();

            // Retornar una respuesta JSON indicando que la factura se creó correctamente
            return response()->json([
                'success' => true,
                'response' => 1,
                'data' => $factura
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            // Manejar la excepción si ocurre algún error y retornar un mensaje de error
            return response()->json([
                'success' => false,
                'response' => 0,
                'message' => "Error al crear la factura. Por favor, inténtalo de nuevo.",
                "data"=> $request->input('referencia')
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Recupera una factura junto con sus líneas asociadas y los datos del usuario correspondiente.
     *
     * @param int $id El ID de la factura que se desea recuperar.
     * @return View Devuelve una vista 'facturaPDF' con los datos de la factura.
     */
    public function recuperarFactura(int $id)
    {
        try {
            // Recuperar la factura con sus líneas asociadas y los datos del usuario
            $factura = Factura::with(['lineas', 'user'])->findOrFail($id);

            // Renderizar la vista 'facturaPDF' con los datos de la factura
            return view('facturaPDF', compact('factura'));
        } catch (\Exception $e) {
            // Manejar cualquier excepción y devolver una vista de error
            return view('error')->with('message', 'Error al recuperar la factura.');
        }
    }

    /**
     * Obtiene todas las facturas asociadas a un usuario específico, con información detallada sobre las líneas de factura y los productos asociados.
     *
     * @param int $userId El ID del usuario para el que se desean recuperar las facturas.
     * @return JsonResponse Una respuesta JSON que contiene las facturas encontradas o un mensaje de error en caso de que ocurra algún problema.
     */
    public function getByUser(int $userId) {
        try {
            // Buscar facturas por user_id con las relaciones 'user', 'lineas', y las relaciones anidadas 'pescado' y 'marisco' dentro de 'lineas'
            $facturas = Factura::with(['user', 'lineas.pescado', 'lineas.marisco'])->where('user_id', $userId)->get();

            // Si no se encuentran facturas para el usuario dado, devolver un mensaje indicando que no se encontraron facturas
            if ($facturas->isEmpty()) {
                return response()->json(['facturas' => null, 'response' => 0], 200);
            }

            // Devolver las facturas encontradas con un estado 200
            return response()->json(['facturas' => $facturas, 'response' => 1], 200);
        } catch (\Exception $e) {
            // Manejar cualquier excepción que pueda ocurrir y devolver un mensaje de error genérico
            return response()->json(['facturas' => null, 'response' => -1], 500);
        }
    }





    /**
     * Genera un archivo PDF que representa una factura específica.
     *
     * @param int $id El ID de la factura para la cual se desea generar el PDF.
     * @return \Illuminate\Http\Response Una respuesta HTTP que contiene el archivo PDF generado.
     */
    public function generarPDF($id)
    {
        try {
            // Buscar la factura correspondiente al ID proporcionado
            $factura = Factura::findOrFail($id);

            // Generar el contenido HTML de la factura utilizando la vista 'facturaPDF'
            $html = view('facturaPDF', compact('factura'))->render();

            // Configuración de Dompdf
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $pdf = new Dompdf($options);

            // Cargar el contenido HTML en Dompdf
            $pdf->loadHtml($html);

            // Renderizar el PDF
            $pdf->render();

            // Obtener el contenido del PDF como una cadena
            $output = $pdf->output();

            // Devolver el contenido del PDF como una respuesta HTTP
            return response($output, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="factura.pdf"');
        } catch (\Exception $e) {
            // Manejar cualquier excepción que pueda ocurrir y devolver una respuesta de error
            return response()->json(['error' => 'Error al generar el PDF de la factura.'], 500);
        }
    }

    /**
     * Elimina una factura y todas las líneas asociadas.
     *
     * @param int $id El ID de la factura que se desea eliminar.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON indicando el resultado de la operación.
     */
    public function delete(int $id)
    {
        try {
            // Buscar la factura correspondiente al ID proporcionado
            $factura = Factura::findOrFail($id);

            // Eliminar todas las líneas asociadas a la factura
            $factura->lineas()->delete();

            // Eliminar la factura
            $factura->delete();

            // Devolver una respuesta JSON indicando que la factura se eliminó correctamente
            return response()->json([
                'success' => true,
                'response' => 1
            ], 200);
        } catch (\Exception $e) {
            // Manejar cualquier excepción que pueda ocurrir y devolver un mensaje de error
            return response()->json([
                'success' => false,
                'response' => 0,
                'message' => "Error al eliminar la factura. Por favor, inténtalo de nuevo."
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Obtener una factura por su ID junto con el usuario relacionado y las líneas con sus relaciones de pescado y marisco.
     *
     * @param int $id El ID de la factura a recuperar.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON que contiene la factura con sus relaciones o un mensaje de error.
     */
    function getById(int $id) {
        try {
            // Buscar la factura con las relaciones 'user', 'lineas', y las relaciones anidadas 'pescado' y 'marisco' dentro de 'lineas'
            $factura = Factura::with(['user', 'lineas.pescado', 'lineas.marisco'])->find($id);

            // Si no se encuentra la factura, devolver un error 404
            if (!$factura) {
                return response()->json(['factura' => null, 'response' => 0], 404);
            }

            // Devolver la factura encontrada con un estado 200
            return response()->json(['factura' => $factura, 'response' => 1], 200);
        } catch (\Exception $e) {
            // Manejar cualquier excepción que pueda ocurrir
            return response()->json(['factura' => null, 'response' => -1], 500);
        }
    }

}
