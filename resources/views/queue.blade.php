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
                @if($stage === 'carteira')
                    <button
                        type="button"
                        id="btn-call-selected"
                        disabled
                        onclick="submitSelected()"
                        class=" flex-1 border-2 border-purple-500 bg-[#202e36] transition duration-300 text-white uppercase tracking-wider font-black rounded hover:bg-purple-600 disabled:opacity-40 disabled:cursor-not-allowed"
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
                        <button class="bg-[#202e36] border-2 border-purple-500 hover:bg-purple-600 transition duration-300 text-white uppercase tracking-wider font-black px-4 py-5 w-full rounded hover:text-black">
                            Renovação
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

            <div class="overflow-y-auto flex-grow pr-2 custom-scrollbar">
                <table class="w-full text-sm text-left text-lightW">
                    <thead class="text-xs text-gray-700 uppercase sticky top-0 z-20 bg-[#141e22]">
                        <tr class="bg-[#202e36]">
                            @if($stage === 'carteira')
                                <th class="p-2 text-[#eceef0] border-[6px] border-blackThirdy text-center">
                                    <input type="checkbox" id="check-all" class="w-4 h-4 cursor-pointer accent-purple-500">
                                </th>
                            @endif
                            <!-- Alterado de ID para SENHA para clareza -->
                            <th class="p-2 text-[#eceef0] border-[6px] border-blackThirdy">SENHA</th>
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
                                            <input type="checkbox" class="ticket-checkbox w-4 h-4 cursor-pointer accent-purple-500" value="{{ $ticket->id }}">
                                        </td>
                                    @endif
                                    <!-- CORREÇÃO: ticket_number -->
                                    <td class="p-2 border-[6px] border-blackThirdy text-center font-bold">{{ $ticket->ticket_number }}</td>
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
                                <td colspan="7" class="p-4 text-center text-gray-500">Nenhum ticket na fila.</td>
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
                <div class="p-6 bg-blackSecondary rounded shadow-md w-full border-l-4 {{ $calledTicket->type === 'preferencial' ? 'border-red' : 'border-green' }}">
                    <div class="flex gap-3 items-center mb-3">
                        <h2 class="font-bold text-xl text-lightW">Ficha em Atendimento:</h2>
                        <!-- CORREÇÃO: ticket_number -->
                        <div class="w-12 h-12 flex items-center justify-center font-bold bg-black rounded-full text-white text-xl">
                            #{{ $calledTicket->ticket_number }}
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

                                @if($stage === 'atendimento' && in_array($calledTicket->service, ['renovacao', 'regularizacao']))
                                    <div class="flex-1">
                                        <button type="button" onclick="mostrarCardCpf()" class="w-full bg-purple-600 transition duration-300 text-lightW py-3 rounded font-black uppercase tracking-wider hover:bg-purple-700">
                                            Confecção
                                        </button>
                                    </div>
                                @endif
                                
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
                <div class="p-10 text-center text-gray-500 italic">Aguardando chamada...</div>
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
    function mostrarCardCpf() {
        const wrapper = document.getElementById('wrapper-modal-cpf');
        const input = document.getElementById('inline-cpf');
        if (wrapper) {
            wrapper.classList.remove('hidden');
            if (input) {
                input.value = '';
                input.focus();
            }
        }
    }

    function esconderCardCpf() {
        const wrapper = document.getElementById('wrapper-modal-cpf');
        if (wrapper) {
            wrapper.classList.add('hidden');
        }
    }

    function handleCpfInput(input) {
        let raw = input.value.replace(/\D/g, "");

        if (raw.length <= 3) input.value = raw;
        else if (raw.length <= 6) input.value = raw.substring(0, 3) + "." + raw.substring(3);
        else if (raw.length <= 9) input.value = raw.substring(0, 3) + "." + raw.substring(3, 6) + "." + raw.substring(6);
        else input.value = raw.substring(0, 3) + "." + raw.substring(3, 6) + "." + raw.substring(6, 9) + "-" + raw.substring(9, 11);

        const hiddenField = document.getElementById('hidden-cpf-field');
        const btn = document.getElementById('btn-confeccao');

        if (hiddenField) {
            hiddenField.value = input.value;
        }

        if (btn) {
            if (raw.length === 11) {
                btn.disabled = false;
                btn.classList.remove('bg-purple-900', 'opacity-40', 'cursor-not-allowed');
                btn.classList.add('bg-purple-600', 'hover:bg-purple-700');
            } else {
                btn.disabled = true;
                btn.classList.add('bg-purple-900', 'opacity-40', 'cursor-not-allowed');
                btn.classList.remove('bg-purple-600', 'hover:bg-purple-700');
            }
        }
    }

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
        checkboxes.forEach(cb => cb.addEventListener('change', updateSelectionState));
        if (checkAll) {
            checkAll.addEventListener('change', () => {
                document.querySelectorAll('.ticket-checkbox:not(:disabled)').forEach(cb => cb.checked = checkAll.checked);
                updateSelectionState();
            });
        }
    }

    function updateSelectionState() {
        const checkboxes = document.querySelectorAll('.ticket-checkbox');
        const btn = document.getElementById('btn-call-selected');
        const countEl = document.getElementById('selected-count');
        selectedTicketIds.clear();
        checkboxes.forEach(cb => { if (cb.checked) selectedTicketIds.add(cb.value); });
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
            if (lastTicketCount !== null && availableTickets.length > lastTicketCount) {
                if (!calledId && Notification.permission === "granted") {
                    new Notification("Novo ticket na fila", { body: "Fila: " + currentStage });
                }
            }
            lastTicketCount = availableTickets.length;
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
            
            html += `<td class="p-2 border-[6px] border-blackThirdy text-center font-bold">${ticket.ticket_number}</td>
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

    @if($stage === 'atendimento' && isset($calledTicket) && in_array($calledTicket->service, ['renovacao', 'regularizacao']))
        <div id="wrapper-modal-cpf" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50">
            
            <div class="bg-blackSecondary border border-blackThirdy rounded-xl shadow-2xl p-6 w-[450px] flex flex-col gap-4">
                <div class="flex justify-between items-center border-b border-blackThirdy pb-3">
                    <h3 class="text-lg font-black uppercase tracking-wider text-lightW">Inserir CPF do Cliente</h3>
                    <button type="button" onclick="esconderCardCpf()" class="text-gray-400 hover:text-white text-2xl font-bold transition">&times;</button>
                </div>
                
                <form action="{{ route('queue.redirect', $calledTicket->id) }}" method="POST" id="form-confeccao" class="flex flex-col gap-4">
                    @csrf
                    <div class="flex flex-col gap-2">
                        <label for="inline-cpf" class="text-xs uppercase tracking-widest text-gray-400 font-bold text-left w-full pl-1">CPF do Cliente</label>
                        <input 
                            type="text" 
                            id="inline-cpf" 
                            name="cpf" 
                            placeholder="000.000.000-00" 
                            maxlength="14"
                            oninput="handleCpfInput(this)"
                            class="w-full bg-[#141e22] border border-blackThirdy rounded p-4 text-2xl text-center text-lightW font-mono focus:outline-none focus:border-purple-500 tracking-widest"
                            autocomplete="off"
                            required
                        >
                    </div>

                    <div class="flex gap-3 mt-2">
                        <button type="button" onclick="esconderCardCpf()" class="flex-1 bg-blackThirdy text-lightW py-3 rounded font-bold uppercase tracking-wider hover:opacity-80 transition">
                            Cancelar
                        </button>
                        <button 
                            type="submit" 
                            id="btn-confeccao"
                            disabled 
                            class="flex-1 bg-purple-900 opacity-40 cursor-not-allowed transition duration-300 text-lightW py-3 rounded font-black uppercase tracking-wider"
                        >
                            Confirmar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

@endsection