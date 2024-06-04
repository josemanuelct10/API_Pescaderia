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
     * Devuelve un JSON con todos los gastos encontrados en la base de datos.
     *
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON que contiene todos los gastos.
     */
    public function index(): JsonResponse
    {
        try {
            // Obtener todos los gastos con las relaciones 'proveedor' y 'user' precargadas
            $gastos = Gasto::with('proveedor', 'user')->get();

            // Devolver una respuesta JSON con todos los gastos encontrados
            return response()->json($gastos, 200);
        } catch (\Exception $e) {
            // Manejar cualquier excepción que pueda ocurrir y devolver un mensaje de error
            return response()->json(['error' => 'Error al obtener los gastos.'], 500);
        }
    }


    /**
     * Almacena un nuevo gasto en la base de datos.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP que contiene los datos del gasto.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON que indica si el gasto se creó correctamente.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $rutaArchivo = null; // Inicializamos $rutaArchivo como null por defecto

            // Verificar si se proporciona un documento y no es nulo
            if ($request->filled('documento') && $request->input('documento') !== null) {
                $base64Documento = $request->input('documento');
                $base64Documento2 = substr($base64Documento, strpos($base64Documento, ',') + 1);
                $documentoBinario = base64_decode($base64Documento2);

                // Generar un nombre único para el archivo
                $nombreArchivo = $request->input('referencia') . '.pdf'; // Nombre del archivo único

                // Establecer la ruta del archivo
                $rutaArchivo = 'documents/' . $nombreArchivo;

                // Guardar el documento en el sistema de archivos
                Storage::disk('public')->put($rutaArchivo, $documentoBinario);
            }

            // Crear el nuevo gasto en la base de datos
            $gasto = Gasto::create([
                'descripcion' => $request->input('descripcion'),
                'referencia' => $request->input('referencia'),
                'cantidad' => $request->input('cantidad'),
                'fecha' => $request->input('fecha'),
                'user_id' => $request->input('user_id'),
                'proveedor_id' => $request->input('proveedor_id'),
                'documento' => $rutaArchivo
            ]);

            // Devolver una respuesta JSON indicando que el gasto se creó correctamente
            return response()->json([
                'success' => true,
                'data' => $gasto
            ], Response::HTTP_CREATED);

        } catch (Exception $e) {
            // Manejar cualquier excepción que ocurra durante el proceso y devolver un mensaje de error
            return response()->json([
                'success' => false,
                'message' => "Error al crear el gasto. Por favor, inténtalo de nuevo.",
                'error' => $e->getMessage() // Opcional: para depuración
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Obtiene y devuelve un archivo de nómina.
     *
     * @param string $nombreArchivo El nombre del archivo de nómina a recuperar.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse Una respuesta que devuelve el archivo de nómina.
     */
    public function getNomina(string $nombreArchivo)
    {
        // Construir la ruta del archivo de nómina
        $rutaArchivo = 'documents/' . $nombreArchivo;

        // Devolver una respuesta que envía el archivo de nómina al cliente
        return response()->file(storage_path('app/public/' . $rutaArchivo));
    }



    /**
     * Elimina un registro de gasto y su documento adjunto (si existe).
     *
     * @param int $id El ID del gasto que se va a eliminar.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON indicando el éxito o el fallo de la operación.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            // Buscar el gasto por su ID
            $gasto = Gasto::findOrFail($id);

            // Eliminar el registro de gasto
            $gasto->delete();

            // Obtener el nombre del documento adjunto
            $documento = $gasto->documento;

            if ($documento) {
                // Construir la ruta completa al archivo del documento
                $rutaArchivo = 'documents/' . $documento;

                // Eliminar el archivo del documento
                Storage::delete($rutaArchivo);
            }

            // Devolver una respuesta JSON indicando el éxito de la operación
            return response()->json([
                'success' => true
            ], 200);
        } catch (ModelNotFoundException $e) {
            // Manejar la excepción si el gasto no se encuentra
            return response()->json([
                'success' => false,
                'error' => 'El gasto con el ID especificado no fue encontrado.'
            ], 404);
        } catch (\Exception $e) {
            // Manejar cualquier otra excepción y devolver un error genérico
            return response()->json([
                'success' => false,
                'error' => 'Se produjo un error al intentar eliminar el gasto.'
            ], 500);
        }
    }
}
