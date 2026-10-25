<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureValidApiToken
{
    /**
     * Autenticación de la API mediante token personal (header Authorization: Bearer <token>).
     *
     * Los tokens se generan con:  php artisan ultranews:issue-token {email}
     * En base de datos se guarda el hash SHA-256 del token.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        $user = filled($token)
            ? User::where('api_token', hash('sha256', $token))->first()
            : null;

        if ($user === null) {
            abort(401, 'Token de API inválido o ausente.');
        }

        Auth::setUser($user);

        return $next($request);
    }
}
