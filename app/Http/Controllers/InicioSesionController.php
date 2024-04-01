<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse; // Asegúrate de importar la clase correcta
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class InicioSesionController extends Controller
{
    public function store(Request $request): JsonResponse{
        try{
            $usuario = User::create($request->all());
            return response()->json([
                'success' => true,
                'data' => $usuario
            ], Response::HTTP_CREATED);
        }catch(QueryException $exception){
            return response()->json([
                'success' => false,
                'message' => "Error al crear el Usuario. Por favor, intentalo de nuevo."
            ], Response:: HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            // Aquí puedes realizar acciones adicionales si la autenticación es exitosa
            return response()->json(['message' => 'Autenticación exitosa', 'user' => $user], 200);
        }

        return response()->json(['error' => 'Credenciales incorrectas'], 401);
    }

}
