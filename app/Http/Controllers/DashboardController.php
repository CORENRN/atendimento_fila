<?php
namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with(['attendant', 'triagem'])->where('status', 'finalizado');

        $tickets = $query->get();

        // Atendimentos por usuário da triagem
        $atendimentosPorUsuario = $tickets->groupBy('triagem_id')->map(function ($group) {
            return [
                'nome' => optional($group->first()->triagem)->name ?? 'Desconhecido',
                'quantidade' => $group->count(),
            ];
        })->values();

        // Tempo médio por usuário da triagem
        $tempoMedioPorUsuario = $tickets->groupBy('triagem_id')->map(function ($group) {
            $duracoes = $group->map(function ($ticket) {
                $start = $ticket->called_tri_at; // chamado da triagem
                if ($start && $ticket->finished_at) {
                    return Carbon::parse($ticket->finished_at)->diffInSeconds(Carbon::parse($start));
                }
                return 0;
            })->filter();

            return [
                'nome' => optional($group->first()->triagem)->name ?? 'Desconhecido',
                'media' => $duracoes->isNotEmpty() ? round($duracoes->avg() / 60, 2) : 0,
            ];
        })->values();

        // Tempo médio por usuário (considerando o campo chamado correto)
        $tempoMedioPorUsuario = $tickets->groupBy('attendant_id')->map(function ($group) {
            $duracoes = $group->map(function ($ticket) {
                $start = $ticket->stage === 'triagem' 
                    ? $ticket->called_tri_at 
                    : $ticket->called_at;

                if ($start && $ticket->finished_at) {
                    return Carbon::parse($ticket->finished_at)->diffInSeconds(Carbon::parse($start));
                }
                return 0;
            })->filter();

            return [
                'nome' => optional($group->first()->attendant)->name ?? 'Desconhecido',
                'media' => $duracoes->isNotEmpty() ? round($duracoes->avg() / 60, 2) : 0, // média em minutos
            ];
        })->values();

        // Atendimentos por serviço
        $atendimentosPorServico = $tickets->groupBy('service')->map(function ($group, $service) {
            return [
                'servico' => $service ?? 'Não informado',
                'quantidade' => $group->count(),
            ];
        })->values();

        // Mapa para uso direto na view (ex: $atendimentosPorServicoMap['financeiro'])
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
