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
                    ['route' => 'queue', 'label' => 'Carteira', 'admin' => false, 'stage' => 'carteira'],
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
            Fila de {{ ucfirst($stage)}}
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
                            @if($stage === 'carteira')
                                <th class="p-2 text-[#eceef0] border-[6px] border-blackThirdy">CPF</th>
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
                                    @if($stage === 'carteira')
                                        <td class="p-2 border-[6px] border-blackThirdy text-center">{{ $ticket->cpf ?? '-' }}</td>
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
                $isActive = ($stage === 'triagem' && $calledTicket?->status === 'triagem') || ($stage === 'atendimento' && $calledTicket?->status === 'atendimento') || ($stage === 'carteira' && $calledTicket?->status === 'carteira');
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
                            <div class="font-semibold {{ $calledTicket->type === 'preferencial' ? ($calledTicket->type === 'carteira' ? 'text-red' : 'text-blue-500') : ($calledTicket->type === 'regular' ? 'text-green' : 'text-lightW') }}">
                            {{ $calledTicket->type }}
                            </div>
                        </div>

                        @if($stage === 'atendimento')
                            <div class="text-lightW">{{ $services[$calledTicket->service] ?? '-' }}</div>
                        @endif

                        @if($stage === 'carteira')
                            <div class="mt-2 p-4 bg-blackThirdy rounded-lg border-l-4 border-blue-500">
                                <h3 class="text-xs uppercase tracking-widest text-gray-400 font-bold mb-1">Documento (CPF)</h3>
                                <p class="text-2xl text-lightW font-mono tracking-tighter">
                                    {{ $calledTicket->cpf ?? 'Não informado' }}
                                </p>
                            </div>
                        @endif

                        <div class="flex flex-col gap-3 mt-4">
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
                                            <div class="hover:p-1 bg-blackThirdy w-full duration-300">
                                                <button 
                                                    id="advance-btn-{{ $calledTicket->id }}" 
                                                    class="bg-blackSecondary transition duration-300 text-white w-full py-3 rounded font-black uppercase tracking-wider" 
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
    let lastTicketCount = null;
    const currentStage = "{{ $stage }}";
    const calledId = @json($called_id ?? null);

    document.addEventListener('DOMContentLoaded', () => {
        const permissaoAtual = Notification.permission;

        if (permissaoAtual === "denied" || permissaoAtual === "default") {
            localStorage.removeItem('notificacao_ativada_v1');
        }

        if(permissaoAtual === "granted"){
            localStorage.removeItem('aviso_ativado_v1');
        }

        Notification.requestPermission().then(permissao => {
            if (permissao === "granted") {
                const jaNotificou = localStorage.getItem('notificacao_ativada_v1');
                if (!jaNotificou) {
                    localStorage.setItem('notificacao_ativada_v1', 'true');
                    new Notification("Notificação ativada", {
                        body: "Notificação ativada com sucesso!",
                        icon: "/favicon.ico"
                    });
                }
            } else {
                const jaAvisou = localStorage.getItem('aviso_ativado_v1');
                if(!jaAvisou){
                    localStorage.setItem('aviso_ativado_v1', 'true');
                    alert('Por favor ative as notificações.');
                }   
            }
        });

        fetchTickets();
    });

    async function fetchTickets() {
        try {
            const response = await fetch(`/queue/tickets/${currentStage}`);
            const data = await response.json();
            const currentTickets = data.tickets || [];
            
            const availableTickets = currentTickets.filter(t => t.id != calledId);
            const currentCount = availableTickets.length;

            if (lastTicketCount !== null && currentCount > lastTicketCount) {
                if (!calledId) {
                    if (Notification.permission === "granted") {
                        const msg = currentStage === 'triagem' 
                            ? "Novo ticket aguardando triagem." 
                            : "Novo ticket disponível.";

                        new Notification("Fila de " + currentStage.charAt(0).toUpperCase() + currentStage.slice(1), {
                            body: msg,
                            icon: "/favicon.ico",
                            tag: "novo-ticket-" + currentStage
                        });
                    }
                }
            }

            lastTicketCount = currentCount;
            updateTable(currentTickets);
        } catch (error) { 
            console.error('Erro ao buscar tickets:', error); 
        }
    }

    function updateTable(tickets) {
        const tbody = document.getElementById('tickets-body');
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
            
            if (currentStage === 'atendimento') {
                html += `<td class="p-2 border-[6px] border-blackThirdy">${ticket.service ?? '-'}</td>`;
            }

            if (currentStage === 'carteira') {
                html += `<td class="p-2 border-[6px] border-blackThirdy text-center">${ticket.cpf ?? '-'}</td>`;
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