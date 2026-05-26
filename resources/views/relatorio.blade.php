<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Fila de Atendimento') }}</title>
        
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ time() }}">

        <style>
            h2, h1 { font-family: "Lora", serif; }
            p { font-family: "Roboto Slab", serif; }
            /* Garante uma cor suave e alternada para o efeito zebrado */
            .table-striped > tbody > tr:nth-of-type(odd) {
                background-color: #f8fafc !important;
            }
            .table-striped > tbody > tr:nth-of-type(even) {
                background-color: #ffffff !important;
            }
        </style>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Lora:ital,wght@0,400..700;1,400..700&family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<div class="card shadow-sm border-0 rounded-3 overflow-hidden">
    <div class="card-header bg-dark-slate p-2" style="background-color: #1e293b; color: #ffff">
        <h3 class="text-white mb-0 fs-5 fw-semibold tracking-wide">
            Relatório de Produtividade dos Atendentes
        </h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0" style="min-width: 600px;">
                <thead style="background-color: #f1f5f9; color: #475569; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0;">
                    <tr>
                        <th class="py-3 px-4" style="width: 25%;">Atendente</th>
                        <th class="text-center py-3" style="width: 15%; text-align: center;">Tipo</th>
                        <th class="text-center py-3" style="width: 12%; text-align: center;">Finalizados</th>
                        <th class="text-center py-3" style="width: 12%; text-align: center;">Cancelados</th>
                        <th class="text-center py-3" style="width: 12%; text-align: center;">Total Geral</th>
                        <th class="text-center py-3" style="width: 24%; text-align: center;">Tempo Médio de Atendimento (TMA)</th>
                    </tr>
                </thead>
                <tbody style="color: #334155;">
                    @foreach($relatorioFormatado as $u)
                        <tr style="transition: background-color 0.2s ease; border-bottom: 1px solid #e2e8f0;">
                            <td class="py-3 px-4">
                                <strong style="color: #0f172a;">{{ $u['atendente'] }}</strong>
                            </td>
                            <td class="text-center py-3" style="text-align: center;">
                                @if($u['tipo'] === 'Triagem')
                                    <span class="badge px-2 py-1 rounded-2" style="background-color: #f3e8ff; color: #6b21a8; font-size: 0.8rem; font-weight: 600;">
                                        {{ $u['tipo'] }}
                                    </span>
                                @else
                                    <span class="badge px-2 py-1 rounded-2" style="background-color: #e0f2fe; color: #0369a1; font-size: 0.8rem; font-weight: 600;">
                                        {{ $u['tipo'] }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center text-success fw-bold py-3" style="color: #16a34a; text-align: center;">
                                {{ $u['finalizados'] }}
                            </td>
                            <td class="text-center py-3" style="color: #dc2626; font-weight: 500; text-align: center;">
                                {{ $u['cancelados'] }}
                            </td>
                            <td class="text-center fw-bold py-3" style="color: #0f172a; text-align: center;">
                                {{ $u['total_geral'] }}
                            </td>
                            <td class="text-center py-3" style="text-align: center;">
                                @if($u['tma'] > 15)
                                    <span class="badge px-3 py-2 rounded-2" style="background-color: #fef3c7; color: #92400e; font-size: 0.85rem; font-weight: 600; display: inline-block; min-width: 75px;">
                                        {{ $u['tma'] }} min
                                    </span>
                                @else
                                    <span class="badge px-3 py-2 rounded-2" style="background-color: #dbeafe; color: #1e40af; font-size: 0.85rem; font-weight: 600; display: inline-block; min-width: 75px;">
                                        {{ $u['tma'] }} min
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>