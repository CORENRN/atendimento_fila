<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasGuiche
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $stage = $request->route('stage');
        if($stage === 'atendimento'){
            
            if (!$user || $user->guiches()->count() === 0) {
                return redirect()->route('home') // Ou a rota de seleção de guichê
                ->with('error', 'Você precisa selecionar um guichê antes de acessar a fila.');
            }

        }
    
        return $next($request);
    }
}
