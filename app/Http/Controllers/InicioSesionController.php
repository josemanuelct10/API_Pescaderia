<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


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
            return response()->json([
                'success' => true,
                'response' => 1,
                'data' => $usuario
            ], Response::HTTP_CREATED);
        } catch (QueryException $exception) {
            return response()->json([
                'success' => false,
                'response' => 0,
                'message' => "Error al crear el Usuario. Por favor, intentalo de nuevo."
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function me(Request $request)
    {
        // Obtener el usuario autenticado
        $user = $request->user();

        // Devolver los datos del usuario en la respuesta
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }


    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth()->user();

        $user->load('categoriaUsuario');


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

    public function updateProfile(Request $request) {
        $user = auth()->user(); // Obtener el usuario autenticado
        $user->update($request->all()); // Actualizar los datos del usuario con los datos recibidos en la solicitud

        return response()->json([
            'response' => 1,
            'success' => true,
            'message' => 'Perfil actualizado correctamente',
            'data' => $user
        ], 200);
    }

        /**
     * Update user's password.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePwd(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Comprobacion si la contraseña antigua es la misma contraseña
        if (!Hash::check($request->oldPassword, $user->password)) {
            return response()->json([
                'response' => 0,
                'success' => false,
                'message' => 'La contraseña antigua no coincide con la contraseña actual.',
            ], 400);
        }

        // Cambio de contraseña
        $user->password = Hash::make($request->newPassword);
        $user->save();

        return response()->json([
            'response' => 1,
            'success' => true,
            'message' => 'Contraseña actualizada correctamente.',
        ], 200);
    }

}
