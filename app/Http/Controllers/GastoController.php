<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; // Importa JsonResponse desde Illuminate\Http
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;
use App\Models\Gasto;
use Illuminate\Support\Str;



class GastoController extends Controller
{
    /**
     * Metodo que devuelve un Json con todos los gastos encontrados en la base de datos
     */
    public function index(): JsonResponse
    {
        $gastos = gasto::with('proveedor','user')->get();

        return response()->json($gastos);
    }

    public function store(Request $request): JsonResponse
    {
        try{
            // Decodificar el archivo base64
            if ($request->has('documento')){
                $base64Documento = $request->input('documento');
                $base64Documento2 = substr($base64Documento, strpos($base64Documento, ',') + 1);
                $documentoBinario = base64_decode($base64Documento2);

                $nombreArchivo = $request->input('referencia').'.pdf'; // Nombre del archivo único

                $rutaArchivo = $nombreArchivo;

                Storage::disk('public')->put('documents/' . $nombreArchivo, $documentoBinario);

            }
            else $rutaArchivo = null;

            $gasto = Gasto::create([
                'descripcion' => $request->input('descripcion'),
                'referencia'=> $request->input('referencia'),
                'cantidad' => $request->input('cantidad'),
                'fecha' => $request->input('fecha'),
                'user_id' => $request->input('user_id'),
                'proveedor_id' => $request->input('proveedor_id'),
                'documento' => $rutaArchivo
            ]);

            return response()->json([
                'success' => true,
                'base64' => $base64Documento2,
                'data' => $gasto
            ], Response::HTTP_CREATED);

        }catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => "Error al crear el gasto. Por favor, intentalo de nuevo."
            ], Response:: HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getNomina(string $nombreArchivo)
    {
        $rutaArchivo = 'documents/' . $nombreArchivo;
        return response()->file(storage_path('app/public/' . $rutaArchivo));
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $gasto = Gasto::findOrFail($id);
            $gasto->delete();

            $documento = $gasto->documento;


            if ($documento) {
                // Formar la ruta completa al archivo
                $rutaArchivo = 'public/documents/' . $documento;

                // Eliminar el archivo
                Storage::delete($rutaArchivo);
            }

            return response()->json([
                'success' => true
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'El gasto con el ID especificado no fue encontrado.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Se produjo un error al intentar eliminar el gasto.'
            ], 500);
        }
    }
}
