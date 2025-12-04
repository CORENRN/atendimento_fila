@extends('layouts.app')

@section('content')
<section class="h-screen w-screen relative bg-[#141e22]">

  <!-- Sidebar -->
   <aside class="fixed top-[165px] left-0 w-64 h-full p-4 z-50">
    <nav class="flex flex-col h-fit p-5 rounded-md bg-blackSecondary shadow-2xl gap-5">
        <h2 class="font-semibold text-lg tracking-widest text-[#eceef0]">MENU</h2>

        @php
            // Função para verificar se usuário é admin ou superAdmin
            $isAdmin = auth()->check() && in_array(auth()->user()->categoria ?? '', ['admin', 'superAdmin']);

            // Monta o array de rotas conforme permissão
            $menuItems = [
                ['home', 'Home'],
                // Só adiciona dashboard se for admin ou superAdmin
                ...($isAdmin ? [['dashboard', 'Dashboard']] : []),
                ['ticket.take', 'Retirar Senha'],
                ['queue', 'Triagem', 'triagem'],
                ['queue', 'Atendimento', 'atendimento'],
            ];
        @endphp

        @foreach($menuItems as $item)
            @php
                $isActive = false;

                if ($item[0] === 'queue') {
                    $isActive = Route::currentRouteName() === 'queue' && (request()->route('stage') === ($item[2] ?? ''));
                } else {
                    $isActive = Route::currentRouteName() === $item[0];
                }

                    $baseClasses = 'h-10 transition text-lightW bg-blackSecondary w-full px-4 py-2 rounded';
                    $divBaseClasses ='bg-blackThirdy flex items-center rounded justify-center duration-300';
                    $hoverClasses = !$isActive ? 'hover:p-[6px]' : '';
                    $activeClasses = $isActive ? 'border-[6px] p-0 border-blackThirdy ' : '';
                
            @endphp
            <div class="{{$divBaseClasses}} {{$hoverClasses}}">
                    <a 
                    href="{{ isset($item[2]) ? route($item[0], $item[2]) : route($item[0]) }}" 
                    class="trasition duration-300{{ $baseClasses }} {{ $activeClasses }}"
                >
                    {{ $item[1] }}
                </a>
            </div>
            
        @endforeach
    </nav>
</aside>



    <!-- Título -->
     <div class="flex items-center mb-4 z-10 bg-[#141e22] justify-center  h-48 w-full">
        <h1 class="text-4xl text-[#56cbec] font-bold px-4 py-2 -mt-24">
            Fila de {{ $stage === 'triagem' ? 'Triagem' : 'Atendimento' }}
        </h1>
     </div>


    <!-- Conteúdo Principal-->
    <section class="flex ml-64 h-full z-10 -mt-28">

        <!-- da fila -->
        <div class="min-w-[50%] h-[75vh] bg-blackSecondary p-8 rounded shadow-xl shadow-xl/30">

        <h1 class="w-full text-center text-3xl text-[#eceef0] font-bold mb-5">Chamada de Tickets</h1>
        <div class="mb-4 flex space-x-4">
            <div class= "mb-4 flex space-x-4 bg-[#202e36] p-5 rounded shadow w-full">
                <form action="{{ route('queue.call', $stage) }}" method="POST" class="flex-1">
                    @csrf
                    <button class="bg-[#202e36] border-2 border-[#39db7d] hover:bg-[#39db7d] transition duration-300 text-white uppercase tracking-wider font-black px-4 py-5 w-full rounded hover:text-black">
                        Próximo
                    </button>
                </form>

                <form action="{{ route('queue.priority', $stage) }}" method="POST" class="flex-1">
                    @csrf
                    <button class=" transition bg-[#202e36] border-2 border-red duration-300 text-white uppercase tracking-wider font-black px-4 py-5 w-full rounded hover:bg-red">
                        Prioritário
                    </button>
                </form>

                <form action="{{ route('queue.recall', $calledTicket?->id ?? 0) }}" method="POST" class="flex-1">
                    @csrf
                    <button 
                        type="submit"
                        @if(!$calledTicket) disabled class=" border-2 border-[#ffeb39] hover:bg-[#ffeb39] text-white w-full bg-[#202e36] uppercase tracking-wider font-black px-4 py-5 rounded cursor-not-allowed" 
                        @else class="bg-blue-500  transition duration-300 text-white w-full py-5 rounded uppercase tracking-wider font-black hover:bg-blue-600" 
                        @endif
                    >
                        Novamente
                    </button>
                </form>
            </div>
        </div>

            <!-- Tabela de tickets -->
            <table class="w-full text-sm text-left rtl:text-right text-gray-900">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr class="bg-[#202e36]">
                        <th class="p-2 text-[#eceef0]">ID</th>
                        <th class="p-2 text-[#eceef0]">Tipo</th>
                        @if($stage === 'atendimento')
                            <th class="p-2 text-[#eceef0]">Serviço</th>
                        @endif
                        <th class="p-2 text-[#eceef0]">Status</th>
                        <th class="p-2 text-[#eceef0]">Ações</th>
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


        <!-- do ticket chamado -->
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
                                    <div class="flex flex-row w-full gap-6">
                                        <form action="{{ route('queue.finish', $calledTicket->id) }}" method="POST" class="inline w-[50%]">
                                            @csrf
                                            <button class="bg-green-500 w-full transition duration-300 text-white px-3 py-3 rounded hover:bg-green-600 mb-3">
                                                Finalizar
                                            </button>
                                        </form>
                                        <form action="{{ route('queue.cancel', $calledTicket->id) }}" method="POST" class="inline w-[50%]">
                                            @csrf
                                            <button class="bg-red-500 transition duration-300 w-full text-white px-3 py-3 rounded hover:bg-red-600">
                                                Cancelar
                                            </button>
                                        </form>
                                    </div>

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

</section>

  <script>
    async function fetchTickets() {
        try {
            // Ajuste a URL para seu endpoint, usando o estágio atual da fila
            const stage = "{{ $stage }}";
            const url = `/queue/tickets/${stage}`;

            const response = await fetch(url);
            if (!response.ok) throw new Error('Erro ao buscar tickets');

            const data = await response.json();

            // Função para atualizar o tbody da tabela
            updateTable(data.tickets);
        } catch (error) {
            console.error('Erro ao atualizar fila:', error);
        }
    }

    function updateTable(tickets) {
        const tbody = document.getElementById('tickets-body');
        tbody.innerHTML = ''; // limpa tabela

        if (tickets.length === 0) {
            tbody.innerHTML = `
                <tr id="empty-row">
                    <td colspan="{{ $stage === 'atendimento' ? 6 : 5 }}" class="p-4 text-center text-gray-500">
                        Nenhum ticket na fila.
                    </td>
                </tr>`;
            return;
        }

        tickets.forEach(ticket => {
            // Ajuste as colunas conforme seu layout
            const tr = document.createElement('tr');
            tr.id = `ticket-${ticket.id}`;

            let innerHTML = `
                <td class="p-2 border text-center">${ticket.id}</td>
                <td class="p-2 border">${ticket.type}</td>`;

            if ("{{ $stage }}" === 'atendimento') {
                innerHTML += `<td class="p-2 border">${ticket.service ?? '-'}</td>`;
            }

            innerHTML += `
                <td class="p-2 border text-center">${ticket.status.toUpperCase()}</td>
                <td class="p-2 border text-center">-</td>`;

            tr.innerHTML = innerHTML;
            tbody.appendChild(tr);
        });
    }

    // Atualiza a tabela a cada 5 segundos (5000ms)
    setInterval(fetchTickets, 5000);

    // Busca inicial
    fetchTickets();
</script>


@endsection