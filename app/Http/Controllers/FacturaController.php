<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; // Importa JsonResponse desde Illuminate\Http
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;
use App\Models\Factura;
use App\Models\Linea;
use Illuminate\Support\Str;
use Dompdf\Dompdf;
use Dompdf\Options;

class FacturaController extends Controller
{
    public function index(): JsonResponse
    {
        $facturas = factura::with('user')->get();

        return response()->json($facturas);
    }

    public function create(Request $request): JsonResponse{
        try{
            $factura = factura::create($request->all());

            return response()->json([
                'success' => true,
                'response' => 1,
                'data' => $factura
            ], Response::HTTP_CREATED);

        }catch(Excepcion $e){
            return response()->json([
                'success' => false,
                'response' => 0,
                'message' => "Error al crear la factura. Por favor, inténtalo de nuevo."
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function recuperarFactura(int $id)
    {
        $factura = Factura::with('lineas')->findOrFail($id)->with('user')->first();
        return view('facturaPDF', compact('factura'));
    }



    /**
     * Metodo que recibe un id y busca la factura y las lineas que pertenecen a esa factura
     */
    public function generarPDF($id)
    {
        $factura = Factura::findOrFail($id);

        // Generar el contenido HTML de la factura
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
    }
}
