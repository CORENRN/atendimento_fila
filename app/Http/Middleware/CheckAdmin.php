<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if(!$user || $user->categoria !== 'admin'){
            abort(403, 'Acesso limitado apenas para admnistradores');
        }

        return $next($request);
    }
}
