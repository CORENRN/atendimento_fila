<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Fila de {{ $stage === 'triagem' ? 'Triagem' : 'Atendimento' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">

    <div class="max-w-4xl mx-auto bg-white p-8 rounded shadow">
        <h1 class="text-2xl font-bold mb-6">
            Fila de {{ $stage === 'triagem' ? 'Triagem' : 'Atendimento' }}
        </h1>

        <div class="mb-4 flex space-x-4">
            <form action="{{ route('queue.call', $stage) }}" method="POST">
                @csrf
                <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Chamar Próximo
                </button>
            </form>

            <a href="{{ route('home') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Voltar
            </a>
        </div>

        <table class="w-full table-auto border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2 border">ID</th>
                    <th class="p-2 border">Nome</th>
                    <th class="p-2 border">Documento</th>
                    <th class="p-2 border">Status</th>
                    <th class="p-2 border">Ações</th>
                </tr>
            </thead>
            <tbody id="tickets-body">
                @forelse ($tickets as $ticket)
                    <tr id="ticket-{{ $ticket->id }}">
                        <td class="p-2 border text-center">{{ $ticket->id }}</td>
                        <td class="p-2 border">{{ $ticket->name }}</td>
                        <td class="p-2 border">{{ $ticket->document ?? '' }}</td>
                        <td class="p-2 border text-center">{{ strtoupper($ticket->status) }}</td>
                        <td class="p-2 border text-center space-x-2">

                            @php
                                // Define se o ticket está ativo no estágio atual (triagem ou atendimento)
                                $isActive = 
                                    ($stage === 'triagem' && $ticket->status === 'triagem') ||
                                    ($stage === 'atendimento' && $ticket->status === 'atendimento');

                                // Para atendimento, só mostra "Finalizar" se já foi chamado (called_at != null)
                                $canFinish = $stage === 'triagem' 
                                    ? $isActive 
                                    : ($isActive && $ticket->called_at !== null);
                            @endphp

                            @if($canFinish)
                                <form action="{{ route('queue.finish', $ticket->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">
                                        Finalizar
                                    </button>
                                </form>
                                
                                <form action="{{ route('queue.cancel', $ticket->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                        cancelar
                                    </button>
                                </form>

                                @if($stage === 'triagem')
                                    <form action="{{ route('queue.advance', $ticket->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button class="bg-purple-500 text-white px-3 py-1 rounded hover:bg-purple-600">
                                            Avançar
                                        </button>
                                    </form>
                                @endif
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr id="empty-row">
                        <td colspan="5" class="p-4 text-center text-gray-500">
                            Nenhum ticket na fila.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</body>
</html>
