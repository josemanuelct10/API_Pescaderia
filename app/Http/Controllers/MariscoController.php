<?php

namespace App\Http\Controllers;
use App\Models\marisco;
use Illuminate\Http\Request;
use App\Http\Requests\MariscoRequest;
use Illuminate\Http\JsonResponse; // Importa JsonResponse desde Illuminate\Http
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;


class MariscoController extends Controller
{

    /**
     * Devuelve un JSON con todos los mariscos encontrados en la base de datos.
     *
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON con los mariscos.
     */
    public function index(): JsonResponse
    {
        // Obtener todos los mariscos con las relaciones 'proveedor' y 'user'
        $mariscos = Marisco::with('proveedor', 'user')->get();

        // Devolver una respuesta JSON con los mariscos obtenidos
        return response()->json($mariscos);
    }


    /**
     * Almacena un nuevo marisco en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request La solicitud HTTP entrante.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON indicando el éxito o el fracaso de la operación.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Verificar si se proporcionó una imagen en la solicitud
            if ($request->hasFile('imagen')) {
                // Guardar la imagen en el sistema de archivos y obtener el nombre del archivo
                $ruta = $request->file('imagen')->store('public/images');
                $imagenNombre = basename($ruta);
            } else {
                $imagenNombre = null; // No se proporcionó ninguna imagen
            }

            // Convertir precioKG y cantidad a tipo float
            $precioKG = (float) $request->input('precioKG');
            $cantidad = (float) $request->input('cantidad');

            // Convertir fechaCompra a tipo date
            $fechaCompra = date_create($request->input('fechaCompra'));

            // Obtener IDs de usuario y proveedor
            $user_id = intval($request->input('user_id'));
            $proveedor_id = intval($request->input('proveedor_id'));

            // Crear un nuevo marisco en la base de datos
            $marisco = Marisco::create([
                'nombre' => $request->input('nombre'),
                'descripcion' => $request->input('descripcion'),
                'origen' => $request->input('origen'),
                'precioKG' => $precioKG,
                'cantidad' => $cantidad,
                'fechaCompra' => $fechaCompra,
                'categoria' => $request->input('categoria'),
                'cocido' => $request->input('cocido'),
                'imagen' => $imagenNombre, // Guardar solo el nombre del archivo de imagen
                'user_id' => $user_id, // ID del usuario creador
                'proveedor_id' => $proveedor_id // ID del proveedor
            ]);

            // Devolver una respuesta JSON con éxito y los datos del marisco creado
            return response()->json([
                'success' => true,
                'data' => $marisco
            ], Response::HTTP_CREATED);
        } catch (QueryException $exception) {
            // Manejar errores de consulta y devolver una respuesta JSON de error
            return response()->json([
                'success' => false,
                'message' => "Error al crear el marisco. Por favor, inténtalo de nuevo."
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Elimina un marisco de la base de datos.
     *
     * @param  int  $id El ID del marisco que se eliminará.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON indicando el éxito o el fracaso de la operación.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            // Buscar el marisco por su ID
            $marisco = Marisco::findOrFail($id);

            // Obtener el nombre de la imagen del marisco (si existe)
            $imagenNombre = $marisco->imagen;

            // Eliminar el marisco de la base de datos
            $marisco->delete();

            // Si se proporcionó el nombre de la imagen, eliminarla del almacenamiento
            if ($imagenNombre) {
                Storage::delete($imagenNombre);
            }

            // Devolver una respuesta JSON indicando éxito
            return response()->json([
                'success' => true
            ], 200);
        } catch (ModelNotFoundException $e) {
            // Manejar el caso en que el marisco no se encuentra y devolver un error 404
            return response()->json([
                'success' => false,
                'error' => 'El marisco con el ID especificado no fue encontrado.'
            ], 404);
        } catch (\Exception $e) {
            // Manejar cualquier otra excepción y devolver un error 500
            return response()->json([
                'success' => false,
                'error' => 'Se produjo un error al intentar eliminar el marisco.'
            ], 500);
        }
    }



    /**
     * Muestra los detalles de un marisco específico.
     *
     * @param  int  $id El ID del marisco que se mostrará.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON con los detalles del marisco o un mensaje de error si no se encuentra.
     */
    public function show(int $id): JsonResponse
    {
        // Buscar el marisco por su ID junto con los detalles del proveedor
        $marisco = Marisco::with('proveedor')->find($id);

        // Verificar si se encontró el marisco
        if (!$marisco) {
            // Si no se encuentra, devolver un mensaje de error con código de estado 404
            return response()->json(['error' => 'El marisco no se encontró.'], 404);
        }

        // Si se encuentra, devolver los detalles del marisco en una respuesta JSON con código de estado 200
        return response()->json($marisco, 200);
    }


    /**
     * Actualiza los detalles de un marisco existente.
     *
     * @param  \Illuminate\Http\Request  $request La solicitud HTTP que contiene los nuevos detalles del marisco.
     * @param  string  $id El ID del marisco que se actualizará.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON indicando si la actualización tuvo éxito o no.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            // Buscar el marisco por su ID
            $marisco = Marisco::find($id);

            // Verificar si se encontró el marisco
            if (!$marisco) {
                // Si no se encuentra, devolver un mensaje de error con código de estado 404
                return response()->json([
                    'success' => false,
                    'message' => 'Marisco no encontrado',
                ], 404);
            }

            // Actualizar los detalles del marisco con los datos de la solicitud
            $marisco->nombre = $request->nombre;
            $marisco->descripcion = $request->descripcion;
            $marisco->origen = $request->origen;
            $marisco->precioKG = $request->precioKG;
            $marisco->cantidad = $request->cantidad;
            $marisco->fechaCompra = $request->fechaCompra;
            $marisco->categoria = $request->categoria;
            $marisco->cocido = $request->cocido;
            $marisco->save();

            // Devolver una respuesta JSON indicando que la actualización fue exitosa
            return response()->json([
                'success' => true,
                'data' => $marisco,
            ], 200);
        } catch (\Exception $e) {
            // Si ocurre un error durante la actualización, devolver un mensaje de error con código de estado 500
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el marisco: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Actualiza la cantidad de un marisco específico.
     *
     * @param  \Illuminate\Http\Request  $request La solicitud HTTP que contiene la nueva cantidad del marisco.
     * @param  int  $id El ID del marisco cuya cantidad se actualizará.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON indicando si la actualización tuvo éxito o no.
     */
    public function updateCantidad(Request $request, $id)
    {
        try {
            // Buscar el marisco por su ID
            $marisco = Marisco::findOrFail($id);

            // Actualizar la cantidad del marisco con el valor proporcionado en la solicitud
            $marisco->cantidad = $request->input('cantidad');
            $marisco->save();

            // Devolver una respuesta JSON indicando que la actualización fue exitosa
            return response()->json([
                'success' => true,
                'response' => 1,
                'message' => "Cantidad de marisco actualizada correctamente"
            ], 200);
        } catch (Exception $e) {
            // Si ocurre un error durante la actualización, devolver un mensaje de error con código de estado 500
            return response()->json([
                'success' => false,
                'response' => 0,
                'message' => "Error al actualizar la cantidad de marisco"
            ], 500);
        }
    }



}
