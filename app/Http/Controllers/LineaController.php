<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; // Importa JsonResponse desde Illuminate\Http
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;
use App\Models\Linea;
use Illuminate\Support\Str;

class LineaController extends Controller
{
    public function create (Request $request): JsonResponse{
        try{
            $linea = Linea::create($request->all());
            return response()->json([
                'success' => true,
                'response' => 1,
                'data' => $linea
            ], Response::HTTP_CREATED);
        }catch(Exception $e){
            return response()->json([
                'success' => false,
                'response' => 0,
                'message' => "Error al crear la linea. Por favor, inténtalo de nuevo."
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id): JsonResponse {
        try {
            $linea = Linea::findOrFail($id);
            $linea->update($request->all());

            return response()->json([
                'success' => true,
                'response' => 1,
                'data' => $linea
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'response' => 0,
                'message' => "Error al actualizar la línea. Por favor, inténtalo de nuevo."
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function delete(int $id){

        try{
            $linea = Linea::findOrFail($id);

            $linea->delete();

            return response()->json([
                'success' => true,
                'response' => 1
            ], 200);
        }
        catch(Exception $e){
            return response()->json([
                'success' => false,
                'response' => 0,
                'message' => "Error al eliminar la linea. Por favor, inténtalo de nuevo."
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }


    }
}
