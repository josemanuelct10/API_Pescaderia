<?php
namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\ProveedorRequest; // Importa ProveedorRequest desde App\Http\Requests

class ProveedorController extends Controller
{
    /**
     * Método que devuelve un Json con todos los proveedores encontrados en la base de datos
     */
    public function index(): JsonResponse
    {
        $proveedoresConGastos = Proveedor::with('gastos')->get();

        return response()->json($proveedoresConGastos, 200);
    }

    /**
     * Método que crea un nuevo proveedor
     * Recibe un Request  para crear el proveedor
     * Devuelve una respuesta JSON
     */
    public function store(Request $request): JsonResponse
    {
        $cif = $request->input('cif');

        // Verificar si ya existe un proveedor con el mismo CIF
        $existingProveedor = Proveedor::where('cif', $cif)->first();
        if ($existingProveedor) {
            return response()->json([
                'response' => -1,
                'success' => false,
                'message' => 'Ya existe un proveedor con este CIF.'
            ], 201);
        }

        try {
            // Crear el proveedor si no existe
            $proveedor = Proveedor::create($request->all());
            return response()->json([
                'response' => 1,
                'success' => true,
                'data' => $proveedor
            ], Response::HTTP_CREATED);
        } catch (QueryException $exception) {
            return response()->json([
                'response' => 0,
                'success' => false,
                'message' => "Error al crear el proveedor. Por favor, inténtalo de nuevo."
            ], Response:: HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Metodo que recibe un Id
     * Devuelve un Json con el marisco encontrado
     */

     public function show(int $id): JsonResponse
     {
        $proveedor = Proveedor::with('gastos')->find($id);
        return response()->json($proveedor);
     }


    public function destroy(int $id): JsonResponse
    {
        try {
            $proveedor = Proveedor::findOrFail($id);
            $proveedor->delete();

            return response()->json([
                'success' => true,
                'response' => 1
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'El proveedor con el ID especificado no fue encontrado.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Se produjo un error al intentar eliminar el proveedor.'
            ], 500);
        }
    }


    public function update(ProveedorRequest $request, string $id): JsonResponse
    {
        try {
            $proveedor = Proveedor::find($id);

            if (!$proveedor) {
                return response()->json([
                    'response' => -1,
                    'success' => false,
                    'message' => 'Proveedor no encontrado',
                ], 404);
            }

            $proveedor->nombre = $request->nombre;
            $proveedor->direccion = $request->direccion;
            $proveedor->telefono = $request->telefono;
            $proveedor->categoria = $request->categoria;
            $proveedor->cif = $request->cif;
            $proveedor->save();

            return response()->json([
                'response' => 1,
                'success' => true,
                'data' => $proveedor,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'response' => 0,
                'success' => false,
                'message' => 'Error al actualizar el proveedor: ' . $e->getMessage(),
            ], 500);
        }
    }
}
