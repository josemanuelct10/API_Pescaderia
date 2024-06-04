<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ChangePwdController extends Controller
{
    /**
     * Valida un token de restablecimiento de contraseña.
     *
     * @param Request $request Solicitud HTTP que contiene el token de restablecimiento de contraseña.
     * @return JsonResponse Respuesta en formato JSON que indica si el token es válido o si ocurrió un error.
     */
    public function validateResetToken(Request $request)
    {
        try {
            // Buscar el usuario por el token de restablecimiento de contraseña
            $user = User::where('reset_password_token', $request->input('token'))->first();

            // Verificar si el token es inválido o expirado
            if (!$user || !$user->reset_password_token_expires_at || now() > $user->reset_password_token_expires_at) {
                // Token inválido o expirado
                return response()->json(['response' => 0]);
            }

            // Token válido y no expirado
            return response()->json(['response' => 1]);
        } catch (\Exception $e) {
            // Captura cualquier excepción y envía un mensaje de error
            return response()->json(['error' => 'Ocurrió un error al validar el token de restablecimiento de contraseña.']);
        }
    }


    /**
     * Cambia la contraseña de un usuario utilizando un token de restablecimiento de contraseña.
     *
     * @param Request $request Solicitud HTTP que contiene el token de restablecimiento de contraseña y la nueva contraseña.
     * @return JsonResponse Respuesta en formato JSON que indica si la contraseña se cambió correctamente o si ocurrió un error.
     */
    public function changePwd(Request $request)
    {
        try {
            // Buscar el usuario por el token de restablecimiento de contraseña
            $user = User::where('reset_password_token', $request->input('token'))->first();

            // Verificar si el token es inválido o expirado
            if (!$user || !$user->reset_password_token_expires_at || now() > $user->reset_password_token_expires_at) {
                // Token inválido o expirado
                return response()->json(['response' => 0]);
            }

            // Si el token es válido y no ha expirado, cambiar la contraseña
            $user->password = Hash::make($request->input('newPassword'));
            // Invalidar el token después de cambiar la contraseña
            $user->reset_password_token = null;
            $user->reset_password_token_expires_at = null;
            $user->save();

            // Devolver una respuesta con un 1 si la contraseña se cambió correctamente
            return response()->json(['response' => 1]);
        } catch (\Exception $e) {
            // Manejar cualquier excepción y devolver un error genérico
            return response()->json(['response' => -1, 'error' => 'An error occurred while changing the password.']);
        }
    }


}
