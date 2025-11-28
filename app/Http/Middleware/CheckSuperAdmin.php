<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if(!$user || $user->categoria !== 'superAdmin'){
            abort(403, "Acesso limitado apenas para Administradores");
        }

        return $next($request);
    }
}
