<?php

namespace App\Http\Controllers;

use App\Models\Guiche;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GuicheController extends Controller
{
    public function showSelectGuiche()
    {
        $userId = Auth::id();

      
        $exists = DB::table('user_guiche')
            ->where('user_id', $userId)
            ->where('created_at', '>=', now()->subHours(12))
            ->first();


        $guiches = Guiche::whereNotIn('id', function ($query) {
            $query->select('guiche_id')
                ->from('user_guiche')
                ->where('created_at', '>=', now()->subHours(12));
        })->get();

        return $exists ? redirect()->route('queue', 'atendimento') : view('choose_guiche', compact('guiches'));
    }

    public function selectGuiche(Request $request)
    {
        $request->validate([
            'guiche_id' => 'required|exists:guiches,id',
        ]);

        $userId = Auth::id();
        $guicheId = (int) $request->guiche_id;


        $isOccupied = DB::table('user_guiche')
            ->where('guiche_id', $guicheId)
            ->where('user_id', '!=', $userId) 
            ->where('created_at', '>=', now()->subHours(12))
            ->exists();

        if ($isOccupied) {
            return back()->with('error', 'Este guichê acabou de ser ocupado por outro atendente.');
        }

       
        DB::table('user_guiche')->updateOrInsert(
            ['user_id' => $userId],
            [
                'guiche_id'  => $guicheId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return redirect()->route('queue', ['stage' => 'atendimento'])
                    ->with('success', 'Guichê selecionado com sucesso.');
    }
}