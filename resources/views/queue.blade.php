@extends('layouts.app')

@section('content')
<section class="h-screen w-screen relative bg-[#141e22]">

    <aside class="fixed top-[165px] left-0 w-64 h-full p-4 z-50">
        <nav class="flex flex-col h-fit p-5 rounded-md bg-blackSecondary shadow-2xl gap-5">
            <h2 class="font-semibold text-lg tracking-widest text-[#eceef0]">MENU</h2>
            
            @php
            //Itens do menu de navegação.
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
                 //Restrições de direcionamento
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
              <!--Se estiver na área de carteira ativa as checkbox-->
                @if($stage === 'carteira')
                    <button
                        type="button"
                        id="btn-call-selected"
                        disabled
                        onclick="submitSelected()"
                        class=" border-2 border-purple-500 bg-[#202e36] transition duration-300 text-white uppercase tracking-wider font-black px-4 py-5 rounded hover:bg-purple-600 disabled:opacity-40 disabled:cursor-not-allowed"
                    >
                        Chamar Selecionados
                        <span id="selected-count" class="ml-1 text-sm font-normal"></span>
                    </button>

                    <form id="form-call-multiple" action="{{ route('queue.callMultiple', $stage) }}" method="POST" class="hidden">
                        @csrf
                        <div id="hidden-ids"></div>
                    </form>
                @endif  
                
                @if($stage === 'triagem' || $stage === 'atendimento')
                    <form action="{{ route('queue.call', $stage) }}" method="POST" class="flex-1">
                        @csrf
                        <button class="bg-[#202e36] border-2 border-[#39db7d] hover:bg-[#39db7d] transition duration-300 text-white uppercase tracking-wider font-black px-4 py-5 w-full rounded hover:text-black">
                            Próximo
                        </button>
                    </form>
                @endif
                @if(auth()->user()->categoria === 'renovacao' && $stage == 'atendimento')
                    <form action="{{ route('queue.renovacao', $stage) }}" method="POST" class="flex-1">
                        @csrf
                        <button class="bg-[#202e36] border-2 border-[#39db7d] hover:bg-[#39db7d] transition duration-300 text-white uppercase tracking-wider font-black px-4 py-5 w-full rounded hover:text-black">
                            R
                        </button>
                    </form>
                @endif
                @if($stage === 'triagem' || $stage === 'atendimento')
                <form action="{{ route('queue.priority', $stage) }}" method="POST" class="flex-1">
                    @csrf
                    <button class="transition bg-[#202e36] border-2 border-red duration-300 text-white uppercase tracking-wider font-black px-4 py-5 w-full rounded hover:bg-red">
                        Prioritário
                    </button>
                </form>
                @endif
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

            <!--Área dos tickets disponíveis-->

            <div class="overflow-y-auto flex-grow pr-2 custom-scrollbar">
                <table class="w-full text-sm text-left rtl:text-right text-lightW">
                    <thead class="text-xs text-gray-700 uppercase sticky top-0 z-20 bg-[#141e22]">
                        <tr class="bg-[#202e36]">
                            @if($stage === 'carteira')
                                <th class="p-2 text-[#eceef0] border-[6px] border-blackThirdy text-center">
                                    <input
                                        type="checkbox"
                                        id="check-all"
                                        class="w-4 h-4 cursor-pointer accent-purple-500"
                                        title="Selecionar todos"
                                    >
                                </th>
                            @endif
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
                        
                            @if($ticket->status === 'aguardando' || $ticket->status === 'triagem_pendente')
                                <tr>
                                    @if($stage === 'carteira')
                                        <td class="p-2 border-[6px] border-blackThirdy text-center">
                                            <input
                                                type="checkbox"
                                                class="ticket-checkbox w-4 h-4 cursor-pointer accent-purple-500"
                                                value="{{ $ticket->id }}"
                                            >
                                        </td>
                                    @endif
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

        <div class="flex w-full flex-col gap-4 overflow-y-auto h-[75vh] custom-scrollbar px-6">
            @php
                $activeTickets = collect();
                
                if ($stage === 'carteira') {
                    $activeTickets = $tickets->where('status', 'carteira');
                }
                
                if ($activeTickets->isEmpty() && isset($called_id)) {
                    $mainTicket = \App\Models\Ticket::find($called_id);
                    if ($mainTicket) $activeTickets->push($mainTicket);
                }
            @endphp

            @forelse($activeTickets as $calledTicket)
                @php
                    $isCorrectStage = ($stage === 'triagem' && $calledTicket->status === 'triagem') || 
                                     ($stage === 'atendimento' && $calledTicket->status === 'atendimento') || 
                                     ($stage === 'carteira' && $calledTicket->status === 'carteira');
                @endphp

                <div class="p-6 bg-blackSecondary rounded shadow-md w-full border-l-4 {{ $calledTicket->type === 'preferencial' ? 'border-red' : 'border-green' }}">
                    <div class="flex gap-3 items-center mb-3">
                        <h2 class="font-bold text-xl text-lightW">Ficha em Atendimento:</h2>
                        <div class="w-10 h-10 flex items-center justify-center font-semibold bg-black rounded-full text-white">
                            #{{ $calledTicket->id }}
                        </div>
                    </div>

                    <div class="flex flex-col gap-3">
                        <div class="flex items-center gap-1">
                            <div class="font-bold text-lightW">Tipo:</div>
                            <div class="font-semibold {{ $calledTicket->type === 'preferencial' ? 'text-red' : 'text-green' }}">
                                {{ strtoupper($calledTicket->type) }}
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
                            <div class="flex flex-row w-full gap-4">
                                <form action="{{ route('queue.finish', $calledTicket->id) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button class="w-full bg-green-600 transition duration-300 text-lightW py-3 rounded font-black uppercase tracking-wider hover:bg-green-700">
                                        Finalizar
                                    </button>
                                </form>
                                <form action="{{ route('queue.cancel', $calledTicket->id) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button class="w-full bg-red transition duration-300 text-lightW py-3 rounded font-black uppercase tracking-wider hover:opacity-80">
                                        Cancelar
                                    </button>
                                </form>
                            </div>

                            @if($stage === 'triagem')
                                <form action="{{ route('queue.advance', $calledTicket->id) }}" method="POST" class="flex gap-3 mt-2">
                                    @csrf
                                    <select name="service" required class="rounded w-full bg-blackSecondary border-blackThirdy text-lightW text-center">
                                        <option value="">Selecione o serviço</option>
                                        @foreach($services as $key => $label)
                                            <option value="{{ $key }}" {{ $calledTicket->service == $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <button class="bg-blue-500 transition duration-300 text-white px-4 py-2 rounded font-black uppercase tracking-wider">
                                        Avançar
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-10 text-center text-gray-500 italic">
                    Aguardando chamada...
                </div>
            @endforelse
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
    let selectedTicketIds = new Set();
    const currentStage = "{{ $stage }}";
    const calledId = @json($called_id ?? null);

    document.addEventListener('DOMContentLoaded', () => {
        Notification.requestPermission();
        fetchTickets();
        if (currentStage === 'carteira') {
            bindCheckboxEvents();
        }
    });

    function bindCheckboxEvents() {
        const checkAll = document.getElementById('check-all');
        const checkboxes = document.querySelectorAll('.ticket-checkbox');
        
        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateSelectionState);
        });

        if (checkAll) {
            checkAll.addEventListener('change', () => {
                document.querySelectorAll('.ticket-checkbox:not(:disabled)').forEach(cb => {
                    cb.checked = checkAll.checked;
                });
                updateSelectionState();
            });
        }
    }

    function updateSelectionState() {
        const checkboxes = document.querySelectorAll('.ticket-checkbox');
        const btn = document.getElementById('btn-call-selected');
        const countEl = document.getElementById('selected-count');
        
        selectedTicketIds.clear();
        checkboxes.forEach(cb => {
            if (cb.checked) selectedTicketIds.add(cb.value);
        });

        if (btn) {
            btn.disabled = selectedTicketIds.size === 0;
            countEl.textContent = selectedTicketIds.size > 0 ? `(${selectedTicketIds.size})` : '';
        }
    }

    function submitSelected() {
        const ids = Array.from(selectedTicketIds);
        if (ids.length === 0) return;

        const container = document.getElementById('hidden-ids');
        container.innerHTML = ids.map(id => `<input type="hidden" name="ticket_ids[]" value="${id}">`).join('');
        document.getElementById('form-call-multiple').submit();
    }

    async function fetchTickets() {
        try {
            const response = await fetch(`/queue/tickets/${currentStage}`);
            const data = await response.json();
            const currentTickets = data.tickets || [];
            
            const availableTickets = currentTickets.filter(t => t.status === 'aguardando' || t.status === 'triagem_pendente');
            const currentCount = availableTickets.length;

            if (lastTicketCount !== null && currentCount > lastTicketCount) {
                if (!calledId && Notification.permission === "granted") {
                    new Notification("Novo ticket na fila", { body: "Fila: " + currentStage });
                }
            }

            lastTicketCount = currentCount;
            updateTable(currentTickets);
        } catch (error) { console.error(error); }
    }

    function updateTable(tickets) {
        const tbody = document.getElementById('tickets-body');
        const filtered = tickets.filter(t => t.status === 'aguardando' || t.status === 'triagem_pendente');
        
        if (filtered.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="p-4 text-center text-gray-500">Nenhum ticket na fila.</td></tr>';
            return;
        }

        tbody.innerHTML = filtered.map(ticket => {
            const isChecked = selectedTicketIds.has(ticket.id.toString()) ? 'checked' : '';
            
            let html = `<tr>`;
            if (currentStage === 'carteira') {
                html += `<td class="p-2 border-[6px] border-blackThirdy text-center">
                    <input type="checkbox" class="ticket-checkbox w-4 h-4 cursor-pointer accent-purple-500" value="${ticket.id}" ${isChecked}>
                </td>`;
            }
            html += `<td class="p-2 border-[6px] border-blackThirdy text-center">${ticket.id}</td>
                <td class="p-2 border-[6px] border-blackThirdy">${ticket.type}</td>`;
            
            if (currentStage === 'atendimento') html += `<td class="p-2 border-[6px] border-blackThirdy">${ticket.service ?? '-'}</td>`;
            if (currentStage === 'carteira') html += `<td class="p-2 border-[6px] border-blackThirdy text-center">${ticket.cpf ?? '-'}</td>`;
            
            html += `<td class="p-2 border-[6px] border-blackThirdy text-center">${ticket.status.toUpperCase()}</td>
                <td class="p-2 border-[6px] border-blackThirdy text-center">-</td></tr>`;
            return html;
        }).join('');

        if (currentStage === 'carteira') bindCheckboxEvents();
    }

    setInterval(fetchTickets, 5000);
</script>
@endsection