<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminOrSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !in_array($user->categoria, ['superAdmin', 'admin'])) {
            abort(403, "Acesso limitado apenas para Administradores");
        }

        return $next($request);
    }
}
