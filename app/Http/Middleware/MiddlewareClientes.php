<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class MiddlewareClientes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si el usuario está autenticado
        if (Auth::check()) {
            // Obtener el usuario actual
            $user = Auth::user();

            // Verificar si el usuario tiene una categoría específica (por ejemplo, admin)
            if ($user->categoria_usuario_id == 1 || $user->categoria_usuario_id == 3 || $user->categoria_usuario_id == 2) {

                // Si el usuario tiene la categoría correcta, continuar con la solicitud
                return $next($request);

            } else {
                // Si el usuario no tiene la categoría correcta, puedes devolver una respuesta de error o redirigirlo a una página de acceso denegado
                return response()->json(['error' => 'Acceso denegado.'], 403);
            }
        } else {
            // Si el usuario no está autenticado, puedes devolver una respuesta de error o redirigirlo a la página de inicio de sesión
            return response()->json(['error' => 'Debe estar autenticado.'], 401);
        }
    }
}
