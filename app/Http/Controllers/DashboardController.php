<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $attendantId = auth()->id();  //pega o id do usuário logado do breeze pra filtrar
        $today = Carbon::today();

        $tickets = Ticket::where('attendant_id', $attendantId)
            ->whereDate('finished_at', $today)
            ->where('status', 'finalizado')
            ->orderBy('finished_at')
            ->get();

        $totalAtendimentos = $tickets->count();

        $duracoes = $tickets->map(function ($ticket) {
            if ($ticket->called_at && $ticket->finished_at) {
                $inicio = Carbon::parse($ticket->called_at);
                $fim = Carbon::parse($ticket->finished_at);
                return $fim->diffInSeconds($inicio);
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
