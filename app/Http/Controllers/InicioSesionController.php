<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\categoriaUsuario;
use App\Mail\DemoEmail;
use Illuminate\Support\Facades\Hash;


class InicioSesionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['login', 'store', 'resetPassword']);
    }

    /**
     * Almacena un nuevo usuario en la base de datos, evitando duplicados en los campos de DNI, email y teléfono.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP que contiene los datos del usuario a almacenar.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON indicando el resultado de la operación.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Comprobar si ya existe un usuario con el mismo DNI
            $existingUserByDni = User::where('dni', $request->input('dni'))->first();
            if ($existingUserByDni) {
                return response()->json([
                    'success' => false,
                    'response' => 0,
                    'message' => 'El DNI ya está registrado.'
                ], 201);
            }

            // Comprobar si ya existe un usuario con el mismo email
            $existingUserByEmail = User::where('email', $request->input('email'))->first();
            if ($existingUserByEmail) {
                return response()->json([
                    'success' => false,
                    'response' => -1,
                    'message' => 'El email ya está registrado.'
                ], 201);
            }

            // Comprobar si ya existe un usuario con el mismo teléfono
            $existingUserByTelefono = User::where('telefono', $request->input('telefono'))->first();
            if ($existingUserByTelefono) {
                return response()->json([
                    'success' => false,
                    'response' => -2,
                    'message' => 'El teléfono ya está registrado.'
                ], 201);
            }

            // Crear el usuario
            $usuario = User::create($request->all());

            return response()->json([
                'success' => true,
                'response' => 1,
                'data' => $usuario
            ], 200);
        } catch (QueryException $exception) {
            // Manejar la excepción de consulta SQL
            return response()->json([
                'success' => false,
                'response' => -2,
                'message' => 'Error al crear el Usuario. Por favor, inténtalo de nuevo.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Devuelve los datos del usuario autenticado.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON con los datos del usuario autenticado.
     */
    public function me(Request $request)
    {
        // Obtener el usuario autenticado
        $user = $request->user();

        // Devolver los datos del usuario en la respuesta JSON
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }



    /**
     * Maneja el proceso de inicio de sesión de un usuario.
     *
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON que contiene el token de autenticación y los datos del usuario.
     */
    public function login()
    {
        // Obtiene las credenciales de inicio de sesión del request
        $credentials = request(['email', 'password']);

        // Intenta autenticar al usuario con las credenciales proporcionadas
        if (! $token = auth()->attempt($credentials)) {
            // Si la autenticación falla, devuelve una respuesta de error 401 (Unauthorized)
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Si la autenticación tiene éxito, obtiene el usuario autenticado
        $user = auth()->user();

        // Carga la relación 'categoriaUsuario' del usuario autenticado
        $user->load('categoriaUsuario');

        // Devuelve una respuesta JSON que incluye el token de autenticación y los datos del usuario
        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }



    /**
     * Maneja el proceso de cierre de sesión de un usuario.
     *
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON con un mensaje de éxito.
     */
    public function logout()
    {
        // Cierra la sesión del usuario actual
        auth()->logout();

        // Devuelve una respuesta JSON con un mensaje de éxito
        return response()->json(['message' => 'Successfully logged out']);
    }


    /**
     * Responde a la solicitud con el token de acceso proporcionado.
     *
     * @param  string $token El token de acceso generado.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON con el token de acceso, tipo de token y tiempo de expiración.
     */
    protected function respondWithToken($token)
    {
        // Devuelve una respuesta JSON con el token de acceso, tipo de token y tiempo de expiración
        return response()->json([
            'access_token' => $token,                     // El token de acceso generado
            'token_type' => 'bearer',                      // El tipo de token
            'expires_in' => auth()->factory()->getTTL() * 60   // El tiempo de expiración del token en segundos
        ]);
    }

    /**
     * Actualiza el perfil del usuario autenticado.
     *
     * @param  \Illuminate\Http\Request  $request La solicitud que contiene los nuevos datos del perfil.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON indicando el resultado de la actualización del perfil.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user(); // Obtener el usuario autenticado

        // Verificar si el email está en uso por otro usuario
        $existingUserByEmail = User::where('email', $request->input('email'))
            ->where('id', '!=', $user->id)
            ->first();
        if ($existingUserByEmail) {
            return response()->json([
                'success' => false,
                'response' => -1,
                'message' => 'El email ya está registrado.'
            ], 200);
        }

        // Verificar si el teléfono está en uso por otro usuario
        $existingUserByTelefono = User::where('telefono', $request->input('telefono'))
            ->where('id', '!=', $user->id)
            ->first();
        if ($existingUserByTelefono) {
            return response()->json([
                'success' => false,
                'response' => -2,
                'message' => 'El teléfono ya está registrado.'
            ], 200);
        }

        // Actualizar los datos del usuario con los datos recibidos en la solicitud
        try {
            $user->update($request->all());

            return response()->json([
                'response' => 1,
                'success' => true,
                'message' => 'Perfil actualizado correctamente',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'response' => -3,
                'message' => 'Error al actualizar el perfil.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    /**
     * Actualiza la contraseña del usuario autenticado.
     *
     * @param  \Illuminate\Http\Request  $request La solicitud que contiene la contraseña antigua y la nueva contraseña.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON indicando el resultado de la actualización de la contraseña.
     */
    public function updatePwd(Request $request)
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Comprobación si la contraseña antigua coincide con la contraseña actual
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


    /**
     * Inicia el proceso de restablecimiento de contraseña para un usuario.
     *
     * @param  \Illuminate\Http\Request  $request La solicitud HTTP que contiene el correo electrónico del usuario.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON indicando el resultado del proceso de restablecimiento de contraseña.
     */
    public function resetPassword(Request $request)
    {
        // Obtener el correo electrónico desde la solicitud
        $email = $request->input('email');

        // Verificar si existe un usuario con el correo electrónico proporcionado
        $user = User::where('email', $email)->first();

        if (!$user) {
            // Si no se encuentra ningún usuario con ese correo electrónico, devolver un error
            return response()->json([
                'response' => -1,
                'message' => 'No se encontró ningún usuario con este correo electrónico'
            ], 200);
        }

        // Generar un token de restablecimiento de contraseña
        $token = Str::random(60);

        // Calcular la fecha de expiración (1 hora desde ahora)
        $expiration = now()->addHours(1);

        // Almacenar el token y la fecha de expiración en la base de datos del usuario
        $user->reset_password_token = $token;
        $user->reset_password_token_expires_at = $expiration;
        $user->save();

        // Enviar el correo electrónico de recuperación de contraseña con el token generado
        try {
            $details = [
                'title' => 'Correo de recuperación de contraseña',
                'body' => 'Haga clic en el siguiente enlace para recuperar su contraseña.',
                'reset_url' => 'http://80.31.158.21/change-password/' . $token
            ];

            // Enviar el correo electrónico
            Mail::to($email)->send(new DemoEmail($details));

            // Devolver una respuesta JSON en caso de éxito
            return response()->json([
                'response' => 1,
                'message' => 'Correo electrónico enviado correctamente'
            ], 200);

        } catch (\Exception $e) {
            // Manejar cualquier error y devolver una respuesta JSON de error
            return response()->json([
                'response'=> 0,
                'message' => 'Error al enviar el correo electrónico: ' . $e->getMessage()
            ], 500);
        }
    }


}
