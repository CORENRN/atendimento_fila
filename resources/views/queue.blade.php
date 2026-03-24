@extends('layouts.app')

@section('content')
<section class="h-screen w-screen relative bg-[#141e22]">

  <aside class="fixed top-[165px] left-0 w-64 h-full p-4 z-50">
    <nav class="flex flex-col h-fit p-5 rounded-md bg-blackSecondary shadow-2xl gap-5">
        <h2 class="font-semibold text-lg tracking-widest text-[#eceef0]">MENU</h2>

        @php
            $user = auth()->user();
            $hasAdminAccess = $user && $user->hasAdminAccess();
            $currentRoute = Route::currentRouteName();
            $currentStage = request()->route('stage');
            $isQueueContext = $currentRoute === 'queue';

            $menuItems = [
                ['route' => 'home', 'label' => 'Home', 'admin' => false, 'stage' => null],
                ['route' => 'dashboard', 'label' => 'Dashboard', 'admin' => true, 'stage' => null],
                ['route' => 'adminPanel', 'label' => 'Gestão', 'admin' => true, 'stage' => null],
                ['route' => 'panel.index', 'label' => 'Visor', 'admin' => true, 'stage' => null],
                ['route' => 'ticket.take', 'label' => 'Retirar Senha', 'admin' => true, 'stage' => null],
                ['route' => 'queue', 'label' => 'Triagem', 'admin' => false, 'stage' => 'triagem'],
                ['route' => 'queue', 'label' => 'Atendimento', 'admin' => false, 'stage' => 'atendimento'],
            ];
        @endphp

        @foreach($menuItems as $item)
            @php
                if ($item['admin'] && !$hasAdminAccess) continue;
                if ($isQueueContext && !$hasAdminAccess) {
                    $isHome = $item['route'] === 'home';
                    $isCurrentQueueStage = $item['route'] === 'queue' && $item['stage'] === $currentStage;
                    if (!$isHome && !$isCurrentQueueStage) continue; 
                }
                
                if ($item['stage'] !== null) {
                    $isActive = $currentRoute === 'queue' && $currentStage === $item['stage'];
                    $href = route($item['route'], ['stage' => $item['stage']]);
                } else {
                    $isActive = $currentRoute === $item['route'];
                    $href = route($item['route']);
                }
                
                $baseClasses = 'h-10 transition text-lightW bg-blackSecondary w-full px-4 py-2 rounded flex items-center';
                $divBaseClasses = 'bg-blackThirdy flex items-center rounded justify-center duration-300 w-full';
                $hoverClasses = !$isActive ? 'hover:p-[4px]' : 'p-[4px]';
                $activeClasses = $isActive ? 'border-1 border-blackThirdy' : '';
            @endphp

            <div class="{{ $divBaseClasses }} {{ $hoverClasses }}">
                <a href="{{ $href }}" class="transition duration-300 {{ $baseClasses }} {{ $activeClasses }}">
                    {{ $item['label'] }}
                </a>
            </div>
        @endforeach
    </nav>
</aside>

    <div class="flex items-center mb-4 z-10 bg-[#141e22] justify-center h-48 w-full">
        <h1 class="text-4xl text-lightW font-bold px-4 py-2 -mt-24">
            Fila de {{ $stage === 'triagem' ? 'Triagem' : 'Atendimento' }}
        </h1>
     </div>

    <section class="flex ml-64 h-full z-10 -mt-28">

        <div class="min-w-[50%] h-[75vh] bg-blackSecondary p-8 rounded shadow-xl flex flex-col">
            <h1 class="w-full text-center text-3xl text-[#eceef0] font-bold mb-5">Chamada de Tickets</h1>
            <div class="mb-4 flex space-x-4 bg-[#202e36] p-5 rounded shadow w-full">
                <form action="{{ route('queue.call', $stage) }}" method="POST" class="flex-1">
                    @csrf
                    <button class="bg-[#202e36] border-2 border-[#39db7d] hover:bg-[#39db7d] transition duration-300 text-white uppercase tracking-wider font-black px-4 py-5 w-full rounded hover:text-black">
                        Próximo
                    </button>
                </form>

                <form action="{{ route('queue.priority', $stage) }}" method="POST" class="flex-1">
                    @csrf
                    <button class="transition bg-[#202e36] border-2 border-red duration-300 text-white uppercase tracking-wider font-black px-4 py-5 w-full rounded hover:bg-red">
                        Prioritário
                    </button>
                </form>

                <form action="{{ route('queue.recall', $called_id ?? 0) }}" method="POST" class="flex-1">
                    @csrf
                    <button 
                        type="submit"
                        @if(!$called_id) 
                            disabled 
                            class="border-2 border-[#ffeb39] opacity-50 cursor-not-allowed text-white w-full bg-[#202e36] uppercase tracking-wider font-black px-4 py-5 rounded" 
                        @else 
                            class="bg-blue-500 transition duration-300 text-white w-full py-5 rounded uppercase tracking-wider font-black hover:bg-blue-600" 
                        @endif
                    >
                        Novamente
                    </button>
                </form>
            </div>

            <div class="overflow-y-auto flex-grow pr-2 custom-scrollbar">
                <table class="w-full text-sm text-left rtl:text-right text-lightW">
                    <thead class="text-xs text-gray-700 uppercase sticky top-0 z-20 bg-[#141e22]">
                        <tr class="bg-[#202e36]">
                            <th class="p-2 text-[#eceef0] border-[6px] border-blackThirdy">ID</th>
                            <th class="p-2 text-[#eceef0] border-[6px] border-blackThirdy">Tipo</th>
                            @if($stage === 'atendimento')
                                <th class="p-2 text-[#eceef0] border-[6px] border-blackThirdy">Serviço</th>
                            @endif
                            <th class="p-2 text-[#eceef0] border-[6px] border-blackThirdy">Status</th>
                            <th class="p-2 text-[#eceef0] border-[6px] border-blackThirdy">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="tickets-body">
                        @forelse ($tickets as $ticket)
                            @if(!isset($called_id) || $ticket->id != $called_id)
                                <tr>
                                    <td class="p-2 border-[6px] border-blackThirdy text-center">{{ $ticket->id }}</td>
                                    <td class="p-2 border-[6px] border-blackThirdy">{{ $ticket->type }}</td>
                                    @if($stage === 'atendimento')
                                        <td class="p-2 border-[6px] border-blackThirdy">{{ $services[$ticket->service] ?? '-' }}</td>
                                    @endif
                                    <td class="p-2 border-[6px] border-blackThirdy text-center">{{ strtoupper($ticket->status) }}</td>
                                    <td class="p-2 border-[6px] border-blackThirdy text-center">-</td>
                                </tr>
                            @endif
                        @empty
                            <tr id="empty-row">
                                <td colspan="6" class="p-4 text-center text-gray-500">Nenhum ticket na fila.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex w-full">
        @if(isset($called_id))
            @php
                $calledTicket = $tickets->firstWhere('id', $called_id);
                $isActive = ($stage === 'triagem' && $calledTicket?->status === 'triagem') || ($stage === 'atendimento' && $calledTicket?->status === 'atendimento');
                $canFinish = $stage === 'triagem' ? $isActive : ($isActive && $calledTicket?->called_at !== null);
            @endphp

            @if($calledTicket)
                <div class="ml-6 p-6 h-fit bg-blackSecondary rounded shadow-md w-[95%]">
                    <div class="flex gap-3 items-center bg mb-3">
                        <h2 class="font-bold text-xl text-lightW">Ficha em Atendimento:</h2>
                        <div class="w-10 h-10 flex items-center justify-center font-semibold bg-black rounded-full text-white">
                            #{{ $calledTicket->id }}
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 justify-between">
                        <div class="flex items-center gap-1">
                            <div class="font-bold text-lightW">Tipo de ticket:</div>
                            <div class="font-semibold {{ $calledTicket->type === 'preferencial' ? 'text-red' : ($calledTicket->type === 'regular' ? 'text-green' : 'text-lightW') }}">
                            {{ $calledTicket->type }}
                            </div>
                        </div>

                        @if($stage === 'atendimento')
                            <div class="text-lightW">{{ $services[$calledTicket->service] ?? '-' }}</div>
                        @endif

                        <div class="flex flex-col gap-3">
                            @if($canFinish)
                                <div>
                                    <h3 class="font-bold mb-1 text-lightW">Encerrar:</h3>
                                    <div class="flex flex-row w-full gap-6">
                                        <form action="{{ route('queue.finish', $calledTicket->id) }}" method="POST" class="inline w-[50%]">
                                            @csrf
                                            <div class="hover:p-1 bg-blackThirdy rounded duration-300">
                                                <button class="border-3 border-blackThirdy bg-blackSecondary w-full transition duration-300 text-lightW px-3 py-3 rounded hover:bg-green-600 font-black uppercase tracking-wider">
                                                    Finalizar
                                                </button>
                                            </div>
                                        </form>
                                        <form action="{{ route('queue.cancel', $calledTicket->id) }}" method="POST" class="inline w-[50%]">
                                            @csrf
                                            <div class="hover:p-1 bg-blackThirdy rounded duration-300">
                                                <button class="border-3 border-blackThirdy bg-blackSecondary w-full transition duration-300 text-lightW px-3 py-3 rounded hover:bg-green-600 font-black uppercase tracking-wider">
                                                    Cancelar
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                @if($stage === 'triagem')
                                    <div>
                                        <h3 class="font-bold mb-1 text-lightW">Tipo de serviço:</h3>
                                        <form action="{{ route('queue.advance', $calledTicket->id) }}" method="POST" class="flex gap-3">
                                            @csrf
                                            <select 
                                                name="service" 
                                                required
                                                onchange="document.getElementById('advance-btn-{{ $calledTicket->id }}').disabled = (this.value === '')"
                                                class="rounded w-full bg-blackSecondary border-blackThirdy text-lightW text-center"
                                            >
                                                <option value="">Selecione o serviço</option>
                                                @foreach($services as $key => $label)
                                                    <option value="{{ $key }}" {{ $calledTicket->service === $key ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="hover:p-1 bg-blackThirdy w-full duration-300 w-50%">
                                                <button 
                                                    id="advance-btn-{{ $calledTicket->id }}" 
                                                    class="bg-blackSecondary transition duration-300 text-white w-full px- py-3  rounded font-black uppercase tracking-wider" 
                                                    disabled 
                                                    type="submit"
                                                >
                                                    Avançar
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endif
        </div>  
    </section>
</section>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #141e22; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #56cbec; border-radius: 10px; }
</style>

<script>
    async function fetchTickets() {
        try {
            const stage = "{{ $stage }}";
            const response = await fetch(`/queue/tickets/${stage}`);
            const data = await response.json();
            updateTable(data.tickets);
        } catch (error) { console.error('Erro:', error); }
    }

    function updateTable(tickets) {
        const tbody = document.getElementById('tickets-body');
        const calledId = "{{ $called_id ?? 'null' }}";
        tbody.innerHTML = '';
        const filtered = tickets.filter(t => t.id != calledId);
        
        if (filtered.length === 0) {
            tbody.innerHTML = '<tr id="empty-row"><td colspan="6" class="p-4 text-center text-gray-500">Nenhum ticket na fila.</td></tr>';
            return;
        }

        filtered.forEach(ticket => {
            const tr = document.createElement('tr');
            let html = `
                <td class="p-2 border-[6px] border-blackThirdy text-center">${ticket.id}</td>
                <td class="p-2 border-[6px] border-blackThirdy">${ticket.type}</td>`;
            if ("{{ $stage }}" === 'atendimento') {
                html += `<td class="p-2 border-[6px] border-blackThirdy">${ticket.service ?? '-'}</td>`;
            }
            html += `
                <td class="p-2 border-[6px] border-blackThirdy text-center">${ticket.status.toUpperCase()}</td>
                <td class="p-2 border-[6px] border-blackThirdy text-center">-</td>`;
            tr.innerHTML = html;
            tbody.appendChild(tr);
        });
    }
    setInterval(fetchTickets, 5000);
</script>
@endsection