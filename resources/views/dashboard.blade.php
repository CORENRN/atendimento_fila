@extends('layouts.app')

@section('content')

<div class="p-10 w-full bg-blackPrimary -mt-4">

    <div class="flex items-center black gap-10 mb-5">
        <h1 class="text-3xl text-lightW font-bold">Dashboard de Atendimentos:</h1>
        <form method="GET" action="{{ route('dashboard') }}" class=" flex gap-4 items-end">
            <div>
                <label for="date" class="block font-medium text-lightW">Data:</label>
                <input type="date" id="date" name="date" value="{{ request('date') }}" class="border rounded px-3 py-2">
            </div>
            <div>
                <label for="month" class="block font-medium text-lightW">Mês:</label>
                <input type="month" id="month" name="month" value="{{ request('month') }}" class="border rounded px-3 py-2">
            </div>
            <div class="bg-blackSecondary hover:p-1 duration-300 rounded">
                <button type="submit" class="bg-blackThirdy text-white px-4 py-2 rounded">Filtrar</button>
            </div>
        </form>
            @if(request('date') || request('month'))
            <div class="text-sm text-gray-600 italic mt-6">
                Filtro aplicado: 
                @if(request('date'))
                    Dia: {{ \Carbon\Carbon::parse(request('date'))->format('d/m/Y') }}
                @endif
                @if(request('month'))
                    Mês: {{ \Carbon\Carbon::parse(request('month'))->translatedFormat('F Y') }}
                @endif
            </div>
        @endif

    </div>


    
    <div class="w-full flex flex-col h-[79vh] gap-5 rounded">



            <div class="w-[100%] h-72  bg-blackSecondary shadow-lg rounded-md p-10">
                <p class="text-xl font-semibold text-lightW mb-4">Atendimentos por Usuário</p>
                <canvas id="chartAtendimentosUsuario" class="w-full h-full"></canvas>
            </div>

            <div class="flex justify-between">
                <div class="bg-blackSecondary shadow-lg w-80 h-36 rounded-md p-3">
                    <p class="text-md tracking-wide uppercase text-lightW text-center font-semibold">Atendimentos de financias</p>
                    <p class="text-center text-3xl font-bold text-lightW mt-4">
                        {{ $atendimentosPorServicoMap['financeiro'] ?? 0 }}
                    </p>
                </div>
                <div class="bg-blackSecondary shadow-lg w-80 h-36 rounded-md p-3">
                    <p class="text-md tracking-wide uppercase text-lightW text-center font-semibold">Atendimentos de documentos</p>
                    <p class="text-center text-3xl font-bold text-lightW mt-4">
                        {{ $atendimentosPorServicoMap['documentacao'] ?? 0 }}
                    </p>
                </div>
                <div class="bg-blackSecondary shadow-lg w-80 h-36 rounded-md p-3">
                    <p class="text-md tracking-wide uppercase text-lightW text-center font-semibold">Atendimentos de informacoes</p>
                    <p class="text-center text-3xl font-bold text-lightW mt-4">
                        {{ $atendimentosPorServicoMap['informacoes'] ?? 0 }}
                    </p>
                </div>
                <div class="bg-blackSecondary shadow-lg w-80 h-36 rounded-md p-3">
                    <p class="text-md tracking-wide uppercase text-lightW text-center font-semibold">Atendimentos de cadastro</p>
                    <p class="text-center text-3xl font-bold text-lightW mt-4">
                        {{ $atendimentosPorServicoMap['cadastro'] ?? 0 }}
                    </p>
                </div>
                <div class="bg-blackSecondary shadow-lg w-80 h-36 rounded-md p-3">
                    <p class="text-md tracking-wide uppercase text-lightW text-center font-semibold">Atendimentos de suporte</p>
                    <p class="text-center text-3xl font-bold text-lightW mt-4">
                        {{ $atendimentosPorServicoMap['suporte'] ?? 0 }}
                    </p>
                </div>
            </div>

            <div class="w-full flex gap-10 h-56">
                <div class="bg-blackSecondary h-full w-[50%] shadow-lg rounded-md p-10">
                    <h2 class="text-xl text-lightW font-semibold mb-4">Tempo Médio de Atendimento (hh:mm:ss)</h2>
                    <canvas id="chartTempoMedioUsuario" class="w-full h-full"></canvas>
                </div>

                <div class="bg-blackSecondary h-full shadow-lg w-[50%] rounded-md p-10">
                    <h2 class="text-xl text-lightW font-semibold mb-4">Atendimentos por Categoria</h2>
                    <canvas id="chartAtendimentosCategoria" class="w-full h-full"></canvas>
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


        
        // Função para formatar segundos em hh:mm:ss
        function formatarTempo(segundos) {
            const h = Math.floor(segundos / 3600).toString().padStart(2, '0');
            const m = Math.floor((segundos % 3600) / 60).toString().padStart(2, '0');
            const s = (segundos % 60).toString().padStart(2, '0');
            return `${h}:${m}:${s}`;
        }

        // Gráfico 1: Atendimentos por usuário (barra)
        new Chart(document.getElementById('chartAtendimentosUsuario'), {
            type: 'bar',
            data: {
                labels: labelsUsuarios,
                datasets: [{
                    label: 'Atendimentos',
                    data: atendimentosUsuarios,
                    backgroundColor: 'rgba(59, 130, 246, 0.7)', // azul
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, precision: 0 } }
            }
        });

        // Gráfico 2: Tempo médio de atendimento por usuário (barra com label customizado)
        new Chart(document.getElementById('chartTempoMedioUsuario'), {
            type: 'bar',
            data: {
                labels: tempoMedioPorUsuario.map(item => item.nome),
                datasets: [{
                    label: 'Tempo Médio (segundos)',
                    data: tempoMedioUsuariosSeg,
                    backgroundColor: 'rgba(34, 197, 94, 0.7)', // verde
                    borderColor: 'rgba(34, 197, 94, 1)',
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
                            callback: function(value) {
                                return formatarTempo(value);
                            }
                        },
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: ctx => `Tempo Médio: ${formatarTempo(ctx.parsed.y)}`
                        }
                    }
                }
            }
        });

        // Gráfico 3: Atendimentos por categoria (pizza)
        new Chart(document.getElementById('chartAtendimentosCategoria'), {
            type: 'doughnut',
            data: {
                labels: atendimentosPorServico.map(item => {
                    // Capitalizar a primeira letra da categoria
                    return item.servico.charAt(0).toUpperCase() + item.servico.slice(1);
                }),
                datasets: [{
                    label: 'Atendimentos',
                    data: atendimentosPorServico.map(item => item.quantidade),
                    backgroundColor: [
                        '#3B82F6', // azul
                        '#F59E0B', // amarelo
                        '#10B981', // verde
                        '#EF4444', // vermelho
                        '#8B5CF6'  // roxo
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        console.log(labelsUsuarios); // mesmo tamanho que tempoMedioUsuariosSeg?
        console.log(tempoMedioUsuariosSeg);
    </script>

</div>

@endsection
