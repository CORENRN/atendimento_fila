@extends('layouts.app')

@section('content')

<section class="h-screen w-screen relative bg-[#141e22]">

  <aside class="fixed top-[165px] left-0 w-64 h-full p-4 z-50">
    <nav class="flex flex-col h-fit p-5 rounded-md bg-blackSecondary shadow-2xl gap-5">
        <h2 class="font-semibold text-lg tracking-widest text-[#eceef0]">MENU</h2>

                @php
                    $user = auth()->user();
                    $hasAdminAccess = $user && $user->hasAdminAccess();

                    if (!isset($menuItems)) {
                        $menuItems = [
                            ['home', 'Home'],
                        ];

                    if ($hasAdminAccess) {
                            $menuItems[] = ['dashboard', 'Dashboard'];
                            $menuItems[] = ['adminPanel', 'Gestão'];
                            $menuItems[] = ['panel.index', 'Visor'];
                            $menuItems[] = ['ticket.take', 'Retirar Senha'];
                        }

                        $menuItems[] = ['queue', 'Triagem', 'triagem'];
                        $menuItems[] = ['queue', 'Atendimento', 'atendimento'];
                    }
                @endphp

                @foreach($menuItems as $item)
                    @php
                        $isActive = false;

                        if ($item[0] === 'queue') {
                            $isActive = Route::currentRouteName() === 'queue' && (request()->route('stage') === ($item[2] ?? ''));
                        } else {
                            $isActive = Route::currentRouteName() === $item[0];
                        }

                        $baseClasses = 'h-10 transition text-lightW bg-blackSecondary w-full px-4 py-2 rounded flex items-center';
                        
                        $divBaseClasses ='bg-blackThirdy flex items-center rounded justify-center duration-300 w-full';
                    
                        $hoverClasses = !$isActive ? 'hover:p-[4px]' : 'p-[4px]'; 
                        $activeClasses = $isActive ? 'border-2 border-blackThirdy' : ''; 
                        
                    @endphp
                    <div class="{{$divBaseClasses}} {{$hoverClasses}}">
                            <a 
                            href="{{ isset($item[2]) ? route($item[0], $item[2]) : route($item[0]) }}" 
                            class="transition duration-300 {{ $baseClasses }} {{ $activeClasses }}"
                        >
                            {{ $item[1] }}
                        </a>
                    </div>
                @endforeach
    </nav>
</aside>

    <div class="ml-[216px] mt-[54px] p-10 flex flex-col h-[79vh] gap-5 rounded overflow-y-auto">

            <div class="w-[100%] h-72 bg-blackSecondary shadow-lg rounded-md p-10">
                <p class="text-xl font-semibold text-lightW mb-4">Atendimentos por Usuário</p>
                <div class="h-44"> <canvas id="chartAtendimentosUsuario"></canvas>
                </div>
            </div>



            <div class="w-full flex gap-10 h-80">
                <div class="bg-blackSecondary h-full w-[50%] shadow-lg rounded-md p-10">
                    <h2 class="text-xl text-lightW font-semibold mb-4">Tempo Médio (hh:mm:ss)</h2>
                    <div class="h-48">
                        <canvas id="chartTempoMedioUsuario"></canvas>
                    </div>
                </div>

                <div class="bg-blackSecondary h-full shadow-lg w-[50%] rounded-md p-10">
                    <h2 class="text-xl text-lightW font-semibold mb-4">Atendimentos por Categoria</h2>
                    <div class="h-48">
                        <canvas id="chartAtendimentosCategoria"></canvas>
                    </div>
                </div>
            </div>
            <div class="grid gap-2" style="grid-template-columns: repeat(1, minmax(0, 1fr));">
                @php
                $cards = [
                    ['Finanças', 'financeiro'],
                    ['Documentos', 'documentacao'],
                    ['Informações', 'informacoes'],
                    ['Cadastro', 'cadastro'],
                    ['Suporte', 'suporte'],
                    ['Inscrição', 'inscricao'],
                    ['Renovação', 'renovacao'],
                    ['Regularização', 'regularizacao'],
                    ['Transferência', 'transferencia'],
                    ['Secundária', 'secundaria'],
                    ['Especialização', 'especializacao'],
                    ['Cancelamento', 'cancelamento'],
                    ['Remida', 'remida'],
                    ['Reativação', 'reativacao'],
                    ['Certidão', 'certidao'],
                    ['ART', 'art'],
                    ['Outros', 'outros'],
                ];
                @endphp
                
            <form method="GET" action="{{ route('relatorio.desempenho.pdf') }}" target="_blank" class="flex flex-wrap justify-between items-center mb-4 gap-3 bg-blackSecondary p-4 rounded-md shadow-2xl border border-blackThirdy">
                <div class="flex items-center gap-3">
                    <label for="month_select" class="font-semibold tracking-wide text-[#eceef0]" style="font-size: 0.9rem;">
                        Selecionar Mês do PDF:
                    </label>
                    
                    @php
                        $anoAtual = now()->year;
                        $mesSelecionado = request('month', 'geral');

                        $mesesAno = [
                            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
                            5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
                            9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
                        ];
                    @endphp

                    <select name="month" 
                            id="month_select" 
                            class="bg-blackPrimary text-[#eceef0] border border-blackThirdy rounded px-3 py-1.5 font-medium outline-none cursor-pointer duration-300 hover:border-[#56cbec]">
                        <option value="geral" {{ $mesSelecionado === 'geral' ? 'selected' : '' }}>
                            (GERAL)
                        </option>
                        @foreach($mesesAno as $numero => $nome)
                            @php
                                $valorFormatado = $anoAtual . '-' . str_pad($numero, 2, '0', STR_PAD_LEFT);
                            @endphp
                            <option value="{{ $valorFormatado }}" {{ $mesSelecionado === $valorFormatado ? 'selected' : '' }}>
                                {{ $nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" 
                        class="text-white w-fit bg-blackSecondary border border-blackThirdy hover:border-[#39db7d] duration-300 flex items-center gap-2 font-semibold px-4 py-2 rounded-md shadow-md">
                    📃 Exportar Relatório em PDF
                </button>
            </form>
                <div class="w-[100%] h-fit bg-blackSecondary shadow-lg rounded-md p-10 grid grid-cols-2">
                   
                    @foreach($cards as $card)
                    
                            <p class="text-[17px] tracking-wide uppercase text-lightW font-semibold p-5 bg-blackPrimary border border-blackSecondary">
                                Atendimentos de {{ $card[0] }} :  {{ $atendimentosPorServicoMap[$card[1]] ?? 0 }}
                            </p>
                    @endforeach
                </div>
            </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const atendimentosPorUsuario = @json($atendimentosPorUsuario);
        const tempoMedioPorUsuario = @json($tempoMedioPorUsuario);
        const atendimentosPorServico = @json($atendimentosPorServico);

        const labelsUsuarios = atendimentosPorUsuario.map(item => item.nome);
        const atendimentosUsuarios = atendimentosPorUsuario.map(item => item.quantidade);
        const tempoMedioUsuariosSeg = tempoMedioPorUsuario.map(item => Math.abs(item.media));
        
        function formatarTempo(segundos) {
            const h = Math.floor(segundos / 3600).toString().padStart(2, '0');
            const m = Math.floor((segundos % 3600) / 60).toString().padStart(2, '0');
            const s = (segundos % 60).toString().padStart(2, '0');
            return `${h}:${m}:${s}`;
        }

        new Chart(document.getElementById('chartAtendimentosUsuario'), {
            type: 'bar',
            data: {
                labels: labelsUsuarios,
                datasets: [{
                    label: 'Atendimentos',
                    data: atendimentosUsuarios,
                    backgroundColor: 'rgba(86, 203, 236, 0.7)',
                    borderColor: '#56cbec',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { 
                    y: { 
                        beginAtZero: true, 
                        precision: 0,
                        ticks: { color: '#eceef0' }
                    },
                    x: { ticks: { color: '#eceef0' } }
                },
                plugins: { legend: { display: false } }
            }
        });

        new Chart(document.getElementById('chartTempoMedioUsuario'), {
            type: 'bar',
            data: {
                labels: tempoMedioPorUsuario.map(item => item.nome),
                datasets: [{
                    label: 'Tempo Médio',
                    data: tempoMedioUsuariosSeg,
                    backgroundColor: 'rgba(57, 219, 125, 0.7)',
                    borderColor: '#39db7d',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#eceef0',
                            callback: value => formatarTempo(value)
                        },
                    },
                    x: { ticks: { color: '#eceef0' } }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => `Tempo Médio: ${formatarTempo(ctx.parsed.y)}`
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('chartAtendimentosCategoria'), {
            type: 'doughnut',
            data: {
                labels: atendimentosPorServico.map(item => item.servico.charAt(0).toUpperCase() + item.servico.slice(1)),
                datasets: [{
                    data: atendimentosPorServico.map(item => item.quantidade),
                    backgroundColor: ['#3B82F6', '#F59E0B', '#10B981', '#EF4444', '#8B5CF6'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'right',
                        labels: { color: '#eceef0' }
                    }
                }
            }
        });
    </script>
</section>
@endsection