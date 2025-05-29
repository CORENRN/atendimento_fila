<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PanelController extends Controller
{
    public function index()
    {
        return view('panel.index');
    }

    public function data()
    {
        $triagem = Ticket::where('stage', 'triagem')
            ->where('status', 'triagem')
            ->whereNotNull('called_at')
            ->whereNull('finished_at')
            ->latest('called_at')
            ->get()
            ->map(fn($t) => [
                'id' => sprintf('%04d', $t->id),
                'called_at' => $t->called_at->format('H:i:s'),
                'guiche' => $this->getGuicheName($t->attendant_id),
            ]);

        $atendimento = Ticket::where('stage', 'atendimento')
            ->where('status', 'atendimento')
            ->whereNotNull('called_at')
            ->whereNull('finished_at')
            ->latest('called_at')
            ->get()
            ->map(fn($t) => [
                'id' => sprintf('%04d', $t->id),
                'called_at' => $t->called_at->format('H:i:s'),
                'guiche' => $this->getGuicheName($t->attendant_id),
            ]);

        return response()->json([
            'triagem' => $triagem,
            'atendimento' => $atendimento,
        ]);
    }

    private function getGuicheName($userId)
    {
        if (!$userId) return null;

        $guiche = DB::table('user_guiche')
            ->join('guiche', 'user_guiche.guiche_id', '=', 'guiche.id')
            ->where('user_guiche.user_id', $userId)
            ->where('user_guiche.created_at', '>=', now()->subHours(12)) // pega guichê nas últimas 12 horas
            ->select('guiche.name')
            ->orderByDesc('user_guiche.created_at')
            ->first();

        return $guiche?->name;
    }
}
