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
            ->whereIn('status', ['triagem'])
            ->whereNotNull('called_at')
            ->latest('called_at')
            ->first();

        $atendimento = Ticket::where('stage', 'atendimento')
            ->whereIn('status', ['atendimento'])
            ->whereNotNull('called_at')
            ->latest('called_at')
            ->first();

        return response()->json([
            'triagem' => $triagem ? [
                'id' => sprintf('%04d', $triagem->id),
                'called_at' => $triagem->called_at->format('H:i:s'),
                'guiche' => $this->getGuicheName($triagem->attendant_id),
            ] : null,
            'atendimento' => $atendimento ? [
                'id' => sprintf('%04d', $atendimento->id),
                'called_at' => $atendimento->called_at->format('H:i:s'),
                'guiche' => $this->getGuicheName($atendimento->attendant_id),
            ] : null,
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
