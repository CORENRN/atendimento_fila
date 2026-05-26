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

        return $pdf->download('relatorio' . now()->format('Y-m-d') . '.pdf');
    }

    private function obterDadosRelatorio()
    {
        $desempenhoUsuarios = DB::table('tickets')
            ->join('users', 'tickets.attendant_id', '=', 'users.id')
            ->select(
                'users.name as atendente',

                DB::raw("SUM(CASE 
                    WHEN tickets.status = 'finalizado' 
                         AND tickets.called_at IS NOT NULL 
                         AND tickets.finished_at IS NOT NULL 
                         AND TIMESTAMPDIFF(MINUTE, tickets.called_at, tickets.finished_at) <= 180 
                    THEN 1 ELSE 0 
                END) as total_finalizados"),
             
                DB::raw("SUM(CASE WHEN tickets.status = 'cancelado' THEN 1 ELSE 0 END) as total_cancelados"),
         
                DB::raw("ROUND(AVG(CASE 
                    WHEN tickets.status = 'finalizado' 
                         AND tickets.called_at IS NOT NULL 
                         AND tickets.finished_at IS NOT NULL 
                         AND TIMESTAMPDIFF(MINUTE, tickets.called_at, tickets.finished_at) <= 180 
                    THEN TIMESTAMPDIFF(MINUTE, tickets.called_at, tickets.finished_at) 
                    ELSE NULL 
                END), 1) as tma_minutos")
            )
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_finalizados', 'DESC')
            ->get();

        return $desempenhoUsuarios->map(function ($usuario) {
            return [
                'atendente'   => $usuario->atendente,
                'finalizados' => (int) $usuario->total_finalizados,
                'cancelados'  => (int) $usuario->total_cancelados,
                'total_geral' => (int) ($usuario->total_finalizados + $usuario->total_cancelados),
                'tma'         => $usuario->tma_minutos ?? 0,
            ];
        });
    }
}