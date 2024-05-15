<?php

namespace App\Http\Controllers;
use App\Models\pescado;
use Illuminate\Http\Request;
use App\Http\Requests\PescadoRequest;
use Illuminate\Http\JsonResponse; // Importa JsonResponse desde Illuminate\Http
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;



class PescadoController extends Controller
{
    /**
     * Metodo que devuelve un Json con todos los pescados encontrados en la base de datos
     */
    public function index(): JsonResponse
    {
        $pescados = Pescado::with('proveedor', 'user')->get();

        return response()->json($pescados);
    }


    /**
     * Método que crea nuevos pescados
     * Metodo que Recibe un Request Personalizado de Pescado
     * Devuelve una Respuesta Json
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Verificar si se ha enviado una imagen
            if ($request->hasFile('imagen')) {
                // Guardar la imagen en el sistema de archivos y obtener el nombre del archivo
                $ruta = $request->file('imagen')->store('public/images');
                $imagenNombre = basename($ruta);
            } else {
                $imagenNombre = null;
            }

            // Convertir precioKG y cantidad a float
            $precioKG = (float)$request->input('precioKG');
            $cantidad = (float)$request->input('cantidad');
            $user_id = intval($request->input('user_id'));
            $proveedor_id = intval($request->input('proveedor_id'));

            // Convertir fechaCompra a tipo date
            $fechaCompra = date_create($request->input('fechaCompra'));

            // Crear un nuevo pescado con los datos recibidos
            $pescado = Pescado::create([
                'nombre' => $request->input('nombre'),
                'descripcion' => $request->input('descripcion'),
                'origen' => $request->input('origen'),
                'precioKG' => $precioKG,
                'cantidad' => $cantidad,
                'fechaCompra' => $fechaCompra,
                'categoria' => $request->input('categoria'),
                'imagen' => $imagenNombre,
                'user_id' => $user_id,
                'proveedor_id' => $proveedor_id
            ]);

            return response()->json([
                'success' => true,
                'data' => $pescado
            ], Response::HTTP_CREATED);
        } catch(\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => "Error al crear el pescado. Por favor, inténtalo de nuevo.",
                'exception_message' => $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Metodo que recibe un Id
     * Devuelve un Json con el pescado encontrado
     */

    public function show(int $id): JsonResponse
    {
        $pescado = Pescado::with('proveedor')->find($id);
        return response()->json($pescado, 200);
    }


    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $pescado = Pescado::find($id);

            if (!$pescado) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pescado no encontrado',
                ], 404);
            }

            $pescado->nombre = $request->nombre;
            $pescado->descripcion = $request->descripcion;
            $pescado->origen = $request->origen;
            $pescado->precioKG = $request->precioKG;
            $pescado->cantidad = $request->cantidad;
            $pescado->fechaCompra = $request->fechaCompra;
            $pescado->categoria = $request->categoria;
            $pescado->save();

            return response()->json([
                'success' => true,
                'data' => $pescado,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el pescado: ' . $e->getMessage(),
            ], 500);
        }
    }



    public function destroy(int $id): JsonResponse
    {
        try {
            $pescado = Pescado::findOrFail($id);
            $pescado->delete();

            $imagenNombre = $pescado->imagen;


            if ($imagenNombre){
                Storage::delete($imagenNombre);
            }


            return response()->json([
                'success' => true
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'El pescado con el ID especificado no fue encontrado.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Se produjo un error al intentar eliminar el pescado.'
            ], 500);
        }
    }

    public function updateCantidad(Request $request, $id)
    {
        try {
            $pescado = Pescado::findOrFail($id);
            $pescado->cantidad = $request->input('cantidad');
            $pescado->save();

            return response()->json([
                'success' => true,
                'response' => 1,
                'message' => "Cantidad de pescado actualizada correctamente"
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'response' => 0,
                'message' => "Error al actualizar la cantidad de pescado"
            ], 500);
        }
    }
}
