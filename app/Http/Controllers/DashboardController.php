<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with(['attendant', 'triagem'])
            ->where('status', 'finalizado');

        // Filtro por dia
        if ($request->filled('date')) {
            $data = Carbon::parse($request->input('date'));
            $query->whereDate('finished_at', $data->toDateString());
        }

        // Filtro por mês
        if ($request->filled('month')) {
            $mes = Carbon::parse($request->input('month'));
            $query->whereMonth('finished_at', $mes->month)
                  ->whereYear('finished_at', $mes->year);
        }

        $tickets = $query->get();

        // Agrupando por usuários (triagem e atendimento)
        $usuarios = collect();

        foreach ($tickets as $ticket) {
            // Triagem
            if ($ticket->triagem_id && $ticket->called_tri_at) {
                $usuarios->push([
                    'id' => $ticket->triagem_id,
                    'nome' => optional($ticket->triagem)->name ?? "ID {$ticket->triagem_id}",
                    'duracao' => $ticket->finished_at
                        ? Carbon::parse($ticket->finished_at)->diffInSeconds(Carbon::parse($ticket->called_tri_at))
                        : null,
                ]);
            }

            // Atendimento
            if ($ticket->attendant_id && $ticket->called_at) {
                $usuarios->push([
                    'id' => $ticket->attendant_id,
                    'nome' => optional($ticket->attendant)->name ?? "ID {$ticket->attendant_id}",
                    'duracao' => $ticket->finished_at
                        ? Carbon::parse($ticket->finished_at)->diffInSeconds(Carbon::parse($ticket->called_at))
                        : null,
                ]);
            }
        }

        // Agrupar por usuário e calcular quantidade e média
        $atendimentosPorUsuario = $usuarios
            ->groupBy('id')
            ->map(function ($group) {
                $duracoes = $group->pluck('duracao')->filter();
                return [
                    'nome' => $group->first()['nome'],
                    'quantidade' => $group->count(),
                    'media' => $duracoes->isNotEmpty()
                        ? max(0, round($duracoes->avg(), 2))
                        : 0,
                ];
            })->values();

        $graficoAtendimentosPorUsuario = $atendimentosPorUsuario->map(fn($item) => [
            'nome' => $item['nome'],
            'quantidade' => $item['quantidade'],
        ]);

        // $graficoTempoMedioPorUsuario = $atendimentosPorUsuario->map(fn($item) => [
        //     'nome' => $item['nome'],
        //     'media' => $item['media'],
        // ]);

        $tempoMedioPorUsuario = $this->calcularTempoMedioPorAtendente($tickets);

        // Atendimentos por serviço
        $atendimentosPorServico = $tickets
            ->groupBy('service')
            ->map(function ($group, $service) {
                return [
                    'servico' => $service ?? 'Não informado',
                    'quantidade' => $group->count(),
                ];
            })->values();

        $atendimentosPorServicoMap = $atendimentosPorServico->mapWithKeys(fn($item) => [
            strtolower($item['servico']) => $item['quantidade']
        ]);

        return view('dashboard', [
            'atendimentosPorUsuario' => $graficoAtendimentosPorUsuario,
            'tempoMedioPorUsuario' => $tempoMedioPorUsuario,
            'atendimentosPorServico' => $atendimentosPorServico,
            'atendimentosPorServicoMap' => $atendimentosPorServicoMap,
        ]);
    }

        private function calcularTempoMedioPorAtendente($tickets)
    {
        $usuarios = collect();

        foreach ($tickets as $ticket) {
            if ($ticket->attendant_id && $ticket->called_at && $ticket->finished_at) {
                $usuarios->push([
                    'id' => $ticket->attendant_id,
                    'nome' => optional($ticket->attendant)->name ?? "ID {$ticket->attendant_id}",
                    'duracao' => Carbon::parse($ticket->finished_at)->diffInSeconds(Carbon::parse($ticket->called_at)),
                ]);
            }
        }

        return $usuarios
            ->groupBy('id')
            ->map(function ($group) {
                $duracoes = $group->pluck('duracao')->filter();
                return [
                    'nome' => $group->first()['nome'],
                    'quantidade' => $group->count(),
                    'media' => $duracoes->isNotEmpty()
                        ? round($duracoes->avg(), 2)
                        : 0,
                ];
            })->values();
    }
}
