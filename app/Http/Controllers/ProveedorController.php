<?php
namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\JsonResponse;
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
        return response()->json(Proveedor::all(), 200);
    }

    /**
     * Método que crea un nuevo proveedor
     * Recibe un ProveedorRequest para validar y crear el proveedor
     * Devuelve una respuesta JSON
     */
    public function store(ProveedorRequest $request): JsonResponse
    {
        try {
            $proveedor = Proveedor::create($request->validated());
            return response()->json([
                'success' => true,
                'data' => $proveedor
            ], Response::HTTP_CREATED);
        } catch (QueryException $exception) {
            return response()->json([
                'success' => false,
                'message' => "Error al crear el proveedor. Por favor, inténtalo de nuevo."
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Metodo que recibe un Id
     * Devuelve un Json con el marisco encontrado
     */

     public function show(int $id): JsonResponse
     {
         $proveedor = Proveedor::find($id);
         return response()->json($proveedor);
     }


    public function destroy(int $id): JsonResponse
    {
        try {
            $proveedor = Proveedor::findOrFail($id);
            $proveedor->delete();

            return response()->json([
                'success' => true
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'El proveedor con el ID especificado no fue encontrado.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Se produjo un error al intentar eliminar el proveedorº.'
            ], 500);
        }
    }


    public function update(ProveedorRequest $request, string $id): JsonResponse
    {
        try {
            $proveedor = Proveedor::find($id);

            if (!$proveedor) {
                return response()->json([
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
                'success' => true,
                'data' => $proveedor,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el proveedor: ' . $e->getMessage(),
            ], 500);
        }
    }
}
