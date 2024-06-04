<?php
namespace App\Http\Controllers;

use App\Models\proveedor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Response;

class ProveedorController extends Controller
{
    /**
     * Obtiene una lista de proveedores con sus gastos asociados.
     *
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON con la lista de proveedores y sus gastos.
     */
    public function index(): JsonResponse
    {
        // Obtener todos los proveedores con sus gastos asociados
        $proveedoresConGastos = Proveedor::with('gastos')->get();

        // Devolver una respuesta JSON con la lista de proveedores y sus gastos, con un estado 200 (OK)
        return response()->json($proveedoresConGastos, 200);
    }


    /**
     * Almacena un nuevo proveedor en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request  La solicitud HTTP que contiene los datos del proveedor a almacenar.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON que indica el éxito o fracaso de la operación.
     */
    public function store(Request $request): JsonResponse
    {
        // Obtener el CIF y el teléfono del proveedor de la solicitud
        $cif = $request->input('cif');
        $telefono = $request->input('telefono');

        // Verificar si ya existe un proveedor con el mismo CIF
        $existingProveedorWithCif = Proveedor::where('cif', $cif)->first();
        if ($existingProveedorWithCif) {
            return response()->json([
                'response' => -1,
                'success' => false,
                'message' => 'Ya existe un proveedor con este CIF.'
            ], Response::HTTP_CREATED);
        }

        // Verificar si ya existe un proveedor con el mismo teléfono
        $existingProveedorWithTelefono = Proveedor::where('telefono', $telefono)->first();
        if ($existingProveedorWithTelefono) {
            return response()->json([
                'response' => -2,
                'success' => false,
                'message' => 'Ya existe un proveedor con este teléfono.'
            ], Response::HTTP_CREATED);
        }

        try {
            // Crear el proveedor si no existe ninguno con el mismo CIF o teléfono
            $proveedor = Proveedor::create($request->all());
            return response()->json([
                'response' => 1,
                'success' => true,
                'data' => $proveedor
            ], Response::HTTP_CREATED);
        } catch (QueryException $exception) {
            // Manejar errores de consulta SQL
            return response()->json([
                'response' => 0,
                'success' => false,
                'message' => "Error al crear el proveedor. Por favor, inténtalo de nuevo."
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    /**
     * Muestra los detalles de un proveedor, incluidos sus gastos asociados.
     *
     * @param  int  $id  El ID del proveedor.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON que contiene los detalles del proveedor y sus gastos.
     */
    public function show(int $id): JsonResponse
    {
        // Recuperar el proveedor con los gastos asociados
        $proveedor = Proveedor::with('gastos')->find($id);

        // Devolver una respuesta JSON con los detalles del proveedor
        return response()->json($proveedor);
    }


    /**
     * Elimina un proveedor de la base de datos.
     *
     * @param  int  $id  El ID del proveedor que se eliminará.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON que indica si la eliminación fue exitosa o no.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            // Buscar el proveedor por su ID
            $proveedor = Proveedor::findOrFail($id);

            // Eliminar el proveedor
            $proveedor->delete();

            // Devolver una respuesta JSON indicando que la eliminación fue exitosa
            return response()->json([
                'success' => true,
                'response' => 1
            ], 200);
        } catch (ModelNotFoundException $e) {
            // Si no se encuentra el proveedor, devolver un mensaje de error
            return response()->json([
                'success' => false,
                'error' => 'El proveedor con el ID especificado no fue encontrado.'
            ], 404);
        } catch (\Exception $e) {
            // Si se produce un error durante la eliminación, devolver un mensaje de error
            return response()->json([
                'success' => false,
                'error' => 'Se produjo un error al intentar eliminar el proveedor.'
            ], 500);
        }
    }



    /**
     * Actualiza un proveedor en la base de datos.
     *
     * @param  \App\Http\Requests\ProveedorRequest  $request  La solicitud que contiene los datos actualizados del proveedor.
     * @param  string  $id  El ID del proveedor que se actualizará.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON que indica si la actualización fue exitosa o no.
     */
    public function update(ProveedorRequest $request, string $id): JsonResponse
    {
        try {
            // Buscar el proveedor por su ID
            $proveedor = Proveedor::find($id);

            // Verificar si el proveedor existe
            if (!$proveedor) {
                // Si el proveedor no existe, devolver un mensaje de error
                return response()->json([
                    'response' => -1,
                    'success' => false,
                    'message' => 'Proveedor no encontrado',
                ], 404);
            }

            // Verificar si ya existe otro proveedor con el mismo CIF
            $existingProveedorWithCif = Proveedor::where('cif', $request->cif)
                ->where('id', '!=', $id)
                ->first();
            if ($existingProveedorWithCif) {
                // Si ya existe otro proveedor con el mismo CIF, devolver un mensaje de error
                return response()->json([
                    'response' => -2,
                    'success' => false,
                    'message' => 'Ya existe otro proveedor con este CIF.',
                ], Response::HTTP_CREATED);
            }

            // Verificar si ya existe otro proveedor con el mismo teléfono
            $existingProveedorWithTelefono = Proveedor::where('telefono', $request->telefono)
                ->where('id', '!=', $id)
                ->first();
            if ($existingProveedorWithTelefono) {
                // Si ya existe otro proveedor con el mismo teléfono, devolver un mensaje de error
                return response()->json([
                    'response' => -3,
                    'success' => false,
                    'message' => 'Ya existe otro proveedor con este teléfono.',
                ], Response::HTTP_CREATED);
            }

            // Actualizar los datos del proveedor con los datos de la solicitud
            $proveedor->nombre = $request->nombre;
            $proveedor->direccion = $request->direccion;
            $proveedor->telefono = $request->telefono;
            $proveedor->categoria = $request->categoria;
            $proveedor->cif = $request->cif;
            $proveedor->save();

            // Devolver una respuesta JSON indicando que la actualización fue exitosa
            return response()->json([
                'response' => 1,
                'success' => true,
                'data' => $proveedor,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            // Si se produce un error durante la actualización, devolver un mensaje de error
            return response()->json([
                'response' => 0,
                'success' => false,
                'message' => 'Error al actualizar el proveedor: ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
