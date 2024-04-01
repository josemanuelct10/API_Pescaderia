<?php

namespace App\Http\Controllers;
use App\Models\pescado;
use Illuminate\Http\Request;
use App\Http\Requests\PescadoRequest;
use Illuminate\Http\JsonResponse; // Importa JsonResponse desde Illuminate\Http
use Symfony\Component\HttpFoundation\Response;


class PescadoController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(pescado::all(), 200);
    }


    public function store(PescadoRequest $request): JsonResponse
    {
        try{
            $pescado = Pescado::create($request->all());
            return response()->json([
                'success' => true,
                'data' => $pescado
            ], Response::HTTP_CREATED);
        } catch(QueryException $exception){
            return response()->json([
                'success' => false,
                'message' => "Error al crear el pescado. Por favor, intentalo de nuevo."
            ], Response:: HTTP_INTERNAL_SERVER_ERROR);
        }

    }



    public function show(string $id): JsonResponse
    {
        $pescado = Pescado::find($id);
        return response()->json($pescado, 200);
    }


    public function update(Request $request, string $id): JsonResponse
    {
        $pescado = Pescado::find($id);
        $pescado -> nombre = $request->nombre;
        $pescado -> descripcion = $request->descripcion;
        $pescado -> origen = $request->origen;
        $pescado -> precioKG = $request->precioKG;
        $pescado -> cantidad = $request->cantidad;
        $pescado -> fechaComprado = $request->fechaCompra;
        $pescado -> categoria = $request->categoria;
        $pescado -> save();

        return response()-> json([
            'success' => true,
            'data' => $pescado
        ], 200);
    }


    public function destroy(int $id): JsonResponse
    {
        Pescado::find($id)->delete();
        return response()->json([
            'success' => true
        ], 200);
    }
}
