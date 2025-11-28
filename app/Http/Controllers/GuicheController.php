<?php

namespace App\Http\Controllers;

use App\Models\Guiche;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuicheController extends Controller
{
    public function showSelectGuiche()
    {
        $guiches = Guiche::whereNotIn('id', function ($query) {
            $query->select('guiche_id')
                ->from('user_guiche')
                ->where('created_at', '>=', now()->subHours(12));
        })->get();

        return view('choose_guiche', compact('guiches'));
    }

    public function selectGuiche(Request $request)
    {
        $request->validate([
            'guiche_id' => 'required|exists:guiches,id',
        ]);

        $userId = auth()->id();

        // Verifica se o usuário já está associado a um guichê nas últimas 12 horas
        $exists = DB::table('user_guiche')
            ->where('user_id', $userId)
            ->where('created_at', '>=', now()->subHours(12))
            ->first();

        if ($exists) {
            return back()->with('error', 'Você já está associado a um guichê nas últimas 12 horas.');
        }

        // Faz a associação
        DB::table('user_guiche')->insert([
            'user_id' => $userId,
            'guiche_id' => $request->guiche_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('home')->with('success', 'Guichê selecionado com sucesso.');
    }
}
