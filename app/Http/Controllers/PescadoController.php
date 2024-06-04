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
     * Obtiene todos los pescados de la base de datos junto con sus proveedores y usuarios asociados.
     *
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON que contiene todos los pescados con sus proveedores y usuarios asociados.
     */
    public function index(): JsonResponse
    {
        // Obtener todos los pescados con sus relaciones 'proveedor' y 'user'
        $pescados = Pescado::with('proveedor', 'user')->get();

        // Devolver una respuesta JSON con todos los pescados
        return response()->json($pescados);
    }


    /**
     * Almacena un nuevo pescado en la base de datos.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP que contiene los datos del pescado a almacenar.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON indicando si se pudo crear el pescado o no.
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

            // Devolver una respuesta JSON con éxito y los datos del pescado creado
            return response()->json([
                'success' => true,
                'data' => $pescado
            ], Response::HTTP_CREATED);
        } catch(\Exception $exception) {
            // Capturar cualquier excepción y devolver una respuesta JSON con error
            return response()->json([
                'success' => false,
                'message' => "Error al crear el pescado. Por favor, inténtalo de nuevo.",
                'exception_message' => $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Muestra los detalles de un pescado específico.
     *
     * @param int $id El ID del pescado que se desea mostrar.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON con los detalles del pescado.
     */
    public function show(int $id): JsonResponse
    {
        // Buscar el pescado por su ID, incluyendo los detalles del proveedor asociado
        $pescado = Pescado::with('proveedor')->find($id);

        // Verificar si se encontró el pescado
        if ($pescado) {
            // Devolver una respuesta JSON con el pescado encontrado y el estado 200 (OK)
            return response()->json($pescado, 200);
        } else {
            // Si el pescado no se encuentra, devolver una respuesta JSON con un mensaje de error y el estado 404 (No encontrado)
            return response()->json(['message' => 'Pescado no encontrado'], 404);
        }
    }


    /**
     * Actualiza los detalles de un pescado específico.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP con los datos actualizados del pescado.
     * @param string $id El ID del pescado que se desea actualizar.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON indicando si la actualización fue exitosa o no.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            // Buscar el pescado por su ID
            $pescado = Pescado::find($id);

            // Verificar si el pescado existe
            if (!$pescado) {
                // Si el pescado no se encuentra, devolver una respuesta JSON con un mensaje de error y el estado 404 (No encontrado)
                return response()->json([
                    'success' => false,
                    'message' => 'Pescado no encontrado',
                ], 404);
            }

            // Actualizar los detalles del pescado con los datos recibidos en la solicitud
            $pescado->nombre = $request->nombre;
            $pescado->descripcion = $request->descripcion;
            $pescado->origen = $request->origen;
            $pescado->precioKG = $request->precioKG;
            $pescado->cantidad = $request->cantidad;
            $pescado->fechaCompra = $request->fechaCompra;
            $pescado->categoria = $request->categoria;
            $pescado->save();

            // Devolver una respuesta JSON indicando que la actualización fue exitosa y los nuevos detalles del pescado
            return response()->json([
                'success' => true,
                'data' => $pescado,
            ], 200);
        } catch (\Exception $e) {
            // Si ocurre un error durante la actualización, devolver una respuesta JSON con un mensaje de error y el estado 500 (Error interno del servidor)
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el pescado: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Elimina un pescado específico.
     *
     * @param int $id El ID del pescado que se desea eliminar.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON indicando si la eliminación fue exitosa o no.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            // Buscar el pescado por su ID
            $pescado = Pescado::findOrFail($id);

            // Eliminar el pescado de la base de datos
            $pescado->delete();

            // Obtener el nombre de la imagen del pescado
            $imagenNombre = $pescado->imagen;

            // Verificar si hay una imagen asociada al pescado y eliminarla del almacenamiento
            if ($imagenNombre){
                Storage::delete($imagenNombre);
            }

            // Devolver una respuesta JSON indicando que la eliminación fue exitosa
            return response()->json([
                'success' => true
            ], 200);
        } catch (ModelNotFoundException $e) {
            // Si el pescado no se encuentra, devolver una respuesta JSON con un mensaje de error y el estado 404 (No encontrado)
            return response()->json([
                'success' => false,
                'error' => 'El pescado con el ID especificado no fue encontrado.'
            ], 404);
        } catch (\Exception $e) {
            // Si ocurre un error durante la eliminación, devolver una respuesta JSON con un mensaje de error y el estado 500 (Error interno del servidor)
            return response()->json([
                'success' => false,
                'error' => 'Se produjo un error al intentar eliminar el pescado.'
            ], 500);
        }
    }


    /**
     * Actualiza la cantidad de un pescado específico.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP que contiene los datos de actualización.
     * @param int $id El ID del pescado cuya cantidad se desea actualizar.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON indicando si la actualización fue exitosa o no.
     */
    public function updateCantidad(Request $request, $id)
    {
        try {
            // Buscar el pescado por su ID
            $pescado = Pescado::findOrFail($id);

            // Actualizar la cantidad del pescado con el valor proporcionado en la solicitud
            $pescado->cantidad = $request->input('cantidad');
            $pescado->save();

            // Devolver una respuesta JSON indicando que la cantidad del pescado se actualizó correctamente
            return response()->json([
                'success' => true,
                'response' => 1,
                'message' => "Cantidad de pescado actualizada correctamente"
            ], 200);
        } catch (Exception $e) {
            // Si ocurre un error durante la actualización, devolver una respuesta JSON con un mensaje de error y el estado 500 (Error interno del servidor)
            return response()->json([
                'success' => false,
                'response' => 0,
                'message' => "Error al actualizar la cantidad de pescado"
            ], 500);
        }
    }

}
