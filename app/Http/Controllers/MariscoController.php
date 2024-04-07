<?php

namespace App\Http\Controllers;
use App\Models\marisco;
use Illuminate\Http\Request;
use App\Http\Requests\MariscoRequest;
use Illuminate\Http\JsonResponse; // Importa JsonResponse desde Illuminate\Http
use Symfony\Component\HttpFoundation\Response;

class MariscoController extends Controller
{
    /**
     * Metodo que devuelve un Json con todos los mariscos encontrados en la base de datos
     */
    public function index(): JsonResponse
    {
        return response()->json(marisco::all(), 200);
    }

    /**
    * Método que crea nuevos mariscos
    * Metodo que Recibe un Request Personalizado de Marisco
    * Devuelve una Respuesta Json
    */
    public function store(MariscoRequest $request): JsonResponse
    {
        try{
            $marisco = Marisco::create($request->all());
            return response()->json([
                'success' => true,
                'data' => $marisco
            ], Response::HTTP_CREATED);
        } catch(QueryException $exception){
            return response()->json([
                'success' => false,
                'message' => "Error al crear el marisco. Por favor, intentalo de nuevo."
            ], Response:: HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $marisco = Marisco::findOrFail($id);
            $marisco->delete();

            return response()->json([
                'success' => true
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'El marisco con el ID especificado no fue encontrado.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Se produjo un error al intentar eliminar el marisco.'
            ], 500);
        }
    }

        /**
     * Metodo que recibe un Id
     * Devuelve un Json con el marisco encontrado
     */

     public function show(int $id): JsonResponse
     {
         $marisco = Marisco::find($id);
         return response()->json($marisco, 200);
     }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $marisco = Marisco::find($id);

            if (!$marisco) {
                return response()->json([
                    'success' => false,
                    'message' => 'Marisco no encontrado',
                ], 404);
            }

            $marisco->nombre = $request->nombre;
            $marisco->descripcion = $request->descripcion;
            $marisco->origen = $request->origen;
            $marisco->precioKG = $request->precioKG;
            $marisco->cantidad = $request->cantidad;
            $marisco->fechaCompra = $request->fechaCompra;
            $marisco->categoria = $request->categoria;
            $marisco->cocido = $request->cocido;
            $marisco->save();

            return response()->json([
                'success' => true,
                'data' => $marisco,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el marisco: ' . $e->getMessage(),
            ], 500);
        }
    }



}
