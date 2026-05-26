<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; 

class RelatorioController extends Controller
{
    public function relatorioDesempenho()
    {
        $relatorioFormatado = $this->obterDadosRelatorio();

        return view('relatorio', compact('relatorioFormatado'));
    }

    public function exportarPdf()
    {
        $relatorioFormatado = $this->obterDadosRelatorio();

        $pdf = Pdf::loadView('relatorio', compact('relatorioFormatado'));

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('relatorio_' . now()->format('Y-m-d') . '.pdf');
    }

    private function obterDadosRelatorio()
    {
        $atendimentos = DB::table('tickets')
            ->join('users', 'tickets.attendant_id', '=', 'users.id')
            ->select(
                'users.id as user_id',
                'users.name as atendente',
                DB::raw("'Atendimento' as tipo"),
                DB::raw("SUM(CASE WHEN tickets.status = 'finalizado' AND tickets.called_at IS NOT NULL AND tickets.finished_at IS NOT NULL AND TIMESTAMPDIFF(MINUTE, tickets.called_at, tickets.finished_at) <= 180 THEN 1 ELSE 0 END) as finalizados"),
                DB::raw("SUM(CASE WHEN tickets.status = 'cancelado' THEN 1 ELSE 0 END) as cancelados"),
                DB::raw("CASE WHEN tickets.status = 'finalizado' AND tickets.called_at IS NOT NULL AND tickets.finished_at IS NOT NULL AND TIMESTAMPDIFF(MINUTE, tickets.called_at, tickets.finished_at) <= 180 THEN TIMESTAMPDIFF(MINUTE, tickets.called_at, tickets.finished_at) ELSE NULL END as tempo_ticket")
            )
            ->groupBy('users.id', 'users.name', 'tickets.status', 'tickets.called_at', 'tickets.finished_at');

        $triagens = DB::table('tickets')
            ->join('users', 'tickets.triagem_id', '=', 'users.id')
            ->select(
                'users.id as user_id',
                'users.name as atendente',
                DB::raw("'Triagem' as tipo"),
                DB::raw("SUM(CASE WHEN tickets.status = 'finalizado' AND tickets.called_tri_at IS NOT NULL AND tickets.finished_at IS NOT NULL THEN 1 ELSE 0 END) as finalizados"),
                DB::raw("SUM(CASE WHEN tickets.status = 'cancelado' THEN 1 ELSE 0 END) as cancelados"),
                DB::raw("CASE WHEN tickets.status = 'finalizado' AND tickets.called_tri_at IS NOT NULL AND tickets.finished_at IS NOT NULL THEN TIMESTAMPDIFF(MINUTE, tickets.called_tri_at, tickets.finished_at) ELSE NULL END as tempo_ticket")
            )
            ->groupBy('users.id', 'users.name', 'tickets.status', 'tickets.called_tri_at', 'tickets.finished_at');

        $subQuery = $atendimentos->unionAll($triagens);

        $resultadoRaw = DB::table(DB::raw("({$subQuery->toSql()}) as uniao"))
            ->mergeBindings($subQuery)
            ->select(
                'atendente',
                'tipo',
                DB::raw("SUM(finalizados) as total_finalizados"),
                DB::raw("SUM(cancelados) as total_cancelados"),
                DB::raw("ROUND(AVG(tempo_ticket), 1) as tma_minutos")
            )
            ->groupBy('user_id', 'atendente', 'tipo')
            ->orderBy('total_finalizados', 'DESC')
            ->get();

        return $resultadoRaw->map(function ($usuario) {
            return [
                'atendente'   => $usuario->atendente,
                'tipo'        => $usuario->tipo,
                'finalizados' => (int) $usuario->total_finalizados,
                'cancelados'  => (int) $usuario->total_cancelados,
                'total_geral' => (int) ($usuario->total_finalizados + $usuario->total_cancelados),
                'tma'         => $usuario->tma_minutos ?? 0,
            ];
        });
    }
}