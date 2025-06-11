<?php
namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
{
    $query = Ticket::with('attendant')->where('status', 'finalizado');

    $date = request('date');
    $month = request('month');

    if ($date) {
        $query->whereDate('finished_at', Carbon::parse($date));
    } elseif ($month) {
        $query->whereMonth('finished_at', Carbon::parse($month)->month)
              ->whereYear('finished_at', Carbon::parse($month)->year);
    } else {
        // Por padrão, mostra os de hoje
        $query->whereDate('finished_at', Carbon::today());
    }

    $tickets = $query->get();

    $atendimentosPorUsuario = $tickets->groupBy('attendant_id')->map(function ($group) {
        return [
            'nome' => optional($group->first()->attendant)->name ?? 'Desconhecido',
            'quantidade' => $group->count(),
        ];
    })->values();

    $tempoMedioPorUsuario = $tickets->groupBy('attendant_id')->map(function ($group) {
        $duracoes = $group->map(function ($ticket) {
            if ($ticket->called_at && $ticket->finished_at) {
                return Carbon::parse($ticket->finished_at)->diffInSeconds(Carbon::parse($ticket->called_at));
            }
            return 0;
        })->filter();

        return [
            'nome' => optional($group->first()->attendant)->name ?? 'Desconhecido',
            'media' => $duracoes->isNotEmpty() ? round($duracoes->avg() / 60, 2) : 0, // em minutos
        ];
    })->values();

    $atendimentosPorServico = $tickets->groupBy('service')->map(function ($group, $service) {
        return [
            'servico' => $service ?? 'Não informado',
            'quantidade' => $group->count(),
        ];
    })->values();

    $atendimentosPorServicoMap = $atendimentosPorServico->mapWithKeys(function ($item) {
        return [strtolower($item['servico']) => $item['quantidade']];
    });


    return view('dashboard', [
        'atendimentosPorUsuario' => $atendimentosPorUsuario,
        'tempoMedioPorUsuario' => $tempoMedioPorUsuario,
        'atendimentosPorServico' => $atendimentosPorServico,
        'atendimentosPorServicoMap' => $atendimentosPorServicoMap,
    ]);

}

}
