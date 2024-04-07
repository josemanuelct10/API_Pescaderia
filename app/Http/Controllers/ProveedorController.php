<?php


namespace App\Http\Controllers;
use App\Models\proveedor;
use Illuminate\Http\Request;
use App\Http\Requests\ProveedorRequest;
use Illuminate\Http\JsonResponse; // Importa JsonResponse desde Illuminate\Http
use Symfony\Component\HttpFoundation\Response;

class ProveedorController extends Controller
{
    /**
     * Metodo que devuelve un Json con todos los proveedores encontrados en la base de datos
    */
    public function index(): JsonResponse
    {
        return response()->json(proveedor::all(), 200);
    }

    /**
    * Método que crea nuevos mariscos
    * Metodo que Recibe un Request Personalizado de Marisco
    * Devuelve una Respuesta Json
    */
    public function store(ProveedorController $request): JsonResponse
    {
        try{
            $proveedor = proveedor::create($request->all());
            return response()->json([
                'success' => true,
                'data' => $proveedor
            ], Response::HTTP_CREATED);
        } catch(QueryException $exception){
            return response()->json([
                'success' => false,
                'message' => "Error al crear el proveedor. Por favor, intentalo de nuevo."
            ], Response:: HTTP_INTERNAL_SERVER_ERROR);
        }
    }




}
