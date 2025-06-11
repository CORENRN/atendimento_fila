@extends('layouts.app')

@section('content')
<section class="h-screen w-screen relative">

  <!-- Sidebar -->
    <aside class="fixed top-[165px] left-0 w-64 h-full p-4 z-50">
        <nav class="flex flex-col h-fit p-5 rounded-md bg-white shadow-2xl gap-5">
            <h2 class="font-semibold text-lg tracking-widest text-gray-500/50">MENU</h2>
            @foreach([
                ['home', 'Home'],
                ['dashboard', 'Dashboard'],
                ['ticket.take', 'Retirar Senha'],
                ['queue', 'Fila de Triagem', 'triagem'],
                ['queue', 'Fila de Atendimento', 'atendimento'],
            ] as $item)
                @php
                    $isActive = false;

                    if ($item[0] === 'queue') {
                        $isActive = Route::currentRouteName() === 'queue' && (request()->route('stage') === ($item[2] ?? ''));
                    } else {
                        $isActive = Route::currentRouteName() === $item[0];
                    }

                    $baseClasses = 'h-10 transition duration-300 text-black px-4 py-2 rounded';
                    $activeClasses = $isActive ? 'bg-black text-white' : 'bg-white hover:bg-gray-200';
                @endphp

                <a 
                    href="{{ isset($item[2]) ? route($item[0], $item[2]) : route($item[0]) }}" 
                    class="{{ $baseClasses }} {{ $activeClasses }}"
                >
                    {{ $item[1] }}
                </a>
            @endforeach
        </nav>
    </aside>


    <!-- Título -->
     <div class="flex items-center mb-4 z-10 justify-center bg-[#edf3ff] h-48 w-full">
        <h1 class="text-4xl text-[#142136] font-bold px-4 py-2 -mt-24">
            Fila de {{ $stage === 'triagem' ? 'Triagem' : 'Atendimento' }}
        </h1>
     </div>


    <!-- Conteúdo Principal-->
    <section class="flex ml-64 h-full z-10 -mt-28">

        <!-- Painel da fila -->
        <div class="min-w-[50%] h-[75vh] bg-white p-8 rounded shadow">

        <h1 class="w-full text-center text-3xl font-bold mb-5">Chamada de Tickets</h1>
        <div class="mb-4 flex space-x-4">
            <form action="{{ route('queue.call', $stage) }}" method="POST" class="flex-1">
                @csrf
                <button class="bg-black transition duration-300 text-white uppercase tracking-wider font-black px-4 py-5 w-full rounded hover:bg-gray-600">
                    Chamar Próximo
                </button>
            </form>

            <form action="{{ route('queue.priority', $stage) }}" method="POST" class="flex-1">
                @csrf
                <button class="bg-red-600 transition duration-300 text-white uppercase tracking-wider font-black px-4 py-5 w-full rounded hover:bg-red-700">
                    Chamar Prioritário
                </button>
            </form>

            <form action="{{ route('queue.recall', $calledTicket?->id ?? 0) }}" method="POST" class="flex-1">
                @csrf
                <button 
                    type="submit"
                    @if(!$calledTicket) disabled class="bg-gray-400 text-white w-full uppercase tracking-wider font-black px-4 py-5 rounded cursor-not-allowed" 
                    @else class="bg-blue-500 transition duration-300 text-white w-full py-5 rounded uppercase tracking-wider font-black hover:bg-blue-600" 
                    @endif
                >
                    Chamar Novamente
                </button>
            </form>
        </div>

            <!-- Tabela de tickets -->
            <table class="w-full text-sm text-left rtl:text-right text-gray-900">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr class="bg-gray-200">
                        <th class="p-2 border">ID</th>
                        <th class="p-2 border">Tipo</th>
                        @if($stage === 'atendimento')
                            <th class="p-2 border">Serviço</th>
                        @endif
                        <th class="p-2 border">Status</th>
                        <th class="p-2 border">Ações</th>
                    </tr>
                </thead>
                <tbody id="tickets-body">
                    @forelse ($tickets as $ticket)
                        @if(!isset($called_id) || $ticket->id != $called_id)
                            @php
                                $isActive = 
                                    ($stage === 'triagem' && $ticket->status === 'triagem') ||
                                    ($stage === 'atendimento' && $ticket->status === 'atendimento');

                                $canFinish = $stage === 'triagem' 
                                    ? $isActive 
                                    : ($isActive && $ticket->called_at !== null);
                            @endphp

                            <tr id="ticket-{{ $ticket->id }}" >
                                <td class="p-2 border text-center">{{ $ticket->id }}</td>
                                <td class="p-2 border">{{ $ticket->type }}</td>

                                @if($stage === 'atendimento')
                                    <td class="p-2 border">{{ $services[$ticket->service] ?? '-' }}</td>
                                @endif

                                <td class="p-2 border text-center">{{ strtoupper($ticket->status) }}</td>

                                <td class="p-2 border text-center space-x-2">
                                    @if($canFinish)
                                        <form action="{{ route('queue.finish', $ticket->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button class="bg-green-500 text-white px-5 py-5 rounded hover:bg-green-600">
                                                Finalizar
                                            </button>
                                        </form>

                                        <form action="{{ route('queue.cancel', $ticket->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button class="bg-red-500 text-white px-5 py-5 rounded hover:bg-red-600">
                                                Cancelar
                                            </button>
                                        </form>

                                        @if($stage === 'triagem')
                                            <div class="flex bg-red-500">
                                                <form 
                                                    action="{{ route('queue.advance', $ticket->id) }}" 
                                                    method="POST" 
                                                    class="inline"
                                                >
                                                    @csrf
                                                    <select 
                                                        name="service" 
                                                        required
                                                        onchange="document.getElementById('advance-btn-{{ $ticket->id }}').disabled = (this.value === '')"
                                                        class="border rounded px-2 py-1 mr-2"
                                                    >
                                                        <option value="">Selecione o serviço</option>
                                                        @foreach($services as $key => $label)
                                                            <option 
                                                                value="{{ $key }}" 
                                                                {{ $ticket->service === $key ? 'selected' : '' }}
                                                            >
                                                                {{ $label }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <button 
                                                        id="advance-btn-{{ $ticket->id }}" 
                                                        class="bg-purple-500 text-white px-3 py-1 rounded hover:bg-purple-600" 
                                                        disabled 
                                                        type="submit"
                                                    >
                                                        Avançar
                                                    </button>
                                                </form>
                                            </div>
                                           
                                        @endif
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr id="empty-row">
                            <td colspan="6" class="p-4 text-center text-gray-500">
                                Nenhum ticket na fila.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex w-full">


        <!-- Painel do ticket chamado -->
        @if(isset($called_id))
            @php
                $calledTicket = $tickets->firstWhere('id', $called_id);
                $isActive = 
                    ($stage === 'triagem' && $calledTicket->status === 'triagem') ||
                    ($stage === 'atendimento' && $calledTicket->status === 'atendimento');

                $canFinish = $stage === 'triagem' 
                    ? $isActive 
                    : ($isActive && $calledTicket->called_at !== null);
            @endphp

            @if($calledTicket)
                <div class="ml-6 p-6 h-fit bg-white border border-white/30 rounded shadow-md w-[95%]">
                    <div class="flex gap-3 items-center bg mb-3">
                        <h2 class="font-bold text-xl">Ficha em Atendimento:</h2>
                        <div class="w-10 h-10 flex items-center justify-center font-semibold bg-black rounded-full text-white">
                            #{{ $calledTicket->id }}
                        </div>
                    </div>

                    <div class="flex flex-col gap-3  justify-between">
                        <div class="flex items-center gap-1">
                            <div class="font-bold">Tipo de ticket:</div>
                            <div class="font-semibold">{{ $calledTicket->type }}</div>
                        </div>

                        @if($stage === 'atendimento')
                            <div>{{ $services[$calledTicket->service] ?? '-' }}</div>
                        @endif

                        <div class="flex flex-col gap-3">
                            @if($canFinish)
                                <div>
                                    <h3 class="font-bold mb-1">Encerrar:</h3>
                                    <form action="{{ route('queue.finish', $calledTicket->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button class="bg-green-500 w-full transition duration-300 text-white px-3 py-3 rounded hover:bg-green-600 mb-3">
                                            Finalizar
                                        </button>
                                    </form>
                                    <form action="{{ route('queue.cancel', $calledTicket->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button class="bg-red-500 transition duration-300 w-full text-white px-3 py-3 rounded hover:bg-red-600">
                                            Cancelar
                                        </button>
                                    </form>
                                </div>

                                @if($stage === 'triagem')
                                    <div>
                                        <h3 class="font-bold mb-1">Avançar:</h3>

                                        <form 
                                            action="{{ route('queue.advance', $calledTicket->id) }}" 
                                            method="POST"
                                            class="flex  gap-3"
                                        >
                                            @csrf
                                            <select 
                                                name="service" 
                                                required
                                                onchange="document.getElementById('advance-btn-{{ $calledTicket->id }}').disabled = (this.value === '')"
                                                class="border rounded px-24 py-3 mr-2"
                                            >
                                                <option value="">Selecione o serviço</option>
                                                @foreach($services as $key => $label)
                                                    <option 
                                                        value="{{ $key }}" 
                                                        {{ $calledTicket->service === $key ? 'selected' : '' }}
                                                    >
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button 
                                                id="advance-btn-{{ $calledTicket->id }}" 
                                                class="bg-purple-500 transition duration-300 text-white px-5 py-3 w-full rounded hover:bg-purple-600" 
                                                disabled 
                                                type="submit"
                                            >
                                                Avançar
                                            </button>
                                        </form>
                                        </div>
                                        
                                    </div>
                                @endif
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <p class="text-red-600 ml-6">Ticket chamado não encontrado.</p>
            @endif
        @endif

        </div>  
    </section>

    <script>
    const ctx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: [
                'Aguardando',
                'Triagem',
                'Atendimento',
                'Finalizado',
                'Cancelado'
            ],
            datasets: [{
                label: 'Tickets',
                data: [
                    {{ $statusCounts['aguardando'] ?? 0 }},
                    {{ $statusCounts['triagem'] ?? 0 }},
                    {{ $statusCounts['atendimento'] ?? 0 }},
                    {{ $statusCounts['finalizado'] ?? 0 }},
                    {{ $statusCounts['cancelado'] ?? 0 }}
                ],
                backgroundColor: [
                    '#fbbf24', // amarelo
                    '#3b82f6', // azul
                    '#6366f1', // roxo
                    '#22c55e', // verde
                    '#ef4444'  // vermelho
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: 'Distribuição dos Tickets por Status'
                }
            }
        }
    });
</script>

</section>

  

@endsection