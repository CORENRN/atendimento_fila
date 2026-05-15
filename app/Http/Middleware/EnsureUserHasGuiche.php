<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasGuiche
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $stage = $request->route('stage');

        if ($stage === 'atendimento') {
            $userId = Auth::id();

            $hasActiveGuiche = DB::table('user_guiche')
                ->where('user_id', $userId)
                ->where('created_at', '>=', now()->subHours(12))
                ->exists();

  
            if (!$userId || !$hasActiveGuiche) {
                return redirect()->route('home') // Redireciona para a home/seleção
                    ->with('error', 'Você precisa selecionar um guichê antes de acessar a fila.');
            }
        }
    
        return $next($request);
    }
}