<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class InicioSesionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['login', 'store']);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $usuario = User::create($request->all());
            $usuario->load('categoriaUsuario');
            return response()->json([
                'success' => true,
                'data' => $usuario
            ], Response::HTTP_CREATED);
        } catch (QueryException $exception) {
            return response()->json([
                'success' => false,
                'message' => "Error al crear el Usuario. Por favor, intentalo de nuevo."
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function me()
    {
        return response()->json(auth()->user());
    }


    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth()->user();

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }


    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
