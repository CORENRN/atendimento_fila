<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $attendantId = 1; // Ajuste conforme sua lógica
        $today = Carbon::today();

        $tickets = Ticket::where('attendant_id', $attendantId)
            ->whereDate('finished_at', $today)
            ->where('status', 'finalizado')
            ->orderBy('finished_at')
            ->get();

        $totalAtendimentos = $tickets->count();

        // Calcular tempo médio ignorando tickets inválidos
        $duracoes = $tickets->map(function ($ticket) {
            if ($ticket->called_at && $ticket->finished_at) {
                $inicio = Carbon::parse($ticket->called_at);
                $fim = Carbon::parse($ticket->finished_at);

                // Diferença absoluta em segundos para evitar valores negativos
                return abs($fim->diffInSeconds($inicio));
            }
            return null;
        })->filter();

        $tempoMedioAtendimento = $duracoes->isNotEmpty()
            ? round($duracoes->avg())
            : 0;

        return view('dashboard', [
            'tickets' => $tickets,
            'totalAtendimentos' => $totalAtendimentos,
            'tempoMedioAtendimento' => $tempoMedioAtendimento,
        ]);
    }
}
