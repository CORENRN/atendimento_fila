<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard de Atendimentos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">

    <div class="max-w-5xl mx-auto bg-white p-8 rounded shadow">
        <h1 class="text-3xl font-bold mb-6">Dashboard de Atendimentos</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total de Atendimentos -->
            <div class="bg-blue-500 text-white p-6 rounded shadow">
                <h2 class="text-xl">Atendimentos Realizados</h2>
                <p class="text-4xl font-bold">{{ $totalAtendimentos }}</p>
            </div>

            <!-- Tempo Médio -->
            <div class="bg-green-500 text-white p-6 rounded shadow">
                <h2 class="text-xl">Tempo Médio</h2>
                <p class="text-4xl font-bold">
                    @php
                        $tempo = max(0, intval($tempoMedioAtendimento));
                        $hours = floor($tempo / 3600);
                        $minutes = floor(($tempo % 3600) / 60);
                        $seconds = $tempo % 60;
                    @endphp
                    {{ sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds) }}
                </p>
            </div>

            <!-- Data Atual -->
            <div class="bg-purple-500 text-white p-6 rounded shadow">
                <h2 class="text-xl">Data</h2>
                <p class="text-4xl font-bold">{{ now()->format('d/m/Y') }}</p>
            </div>
        </div>

        <h2 class="text-2xl font-bold mb-4">Detalhes dos Atendimentos</h2>

        <table class="w-full table-auto border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2 border">ID</th>
                    <th class="p-2 border">Início</th>
                    <th class="p-2 border">Fim</th>
                    <th class="p-2 border">Duração</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                    <tr class="hover:bg-gray-100">
                        <td class="p-2 border text-center">{{ $ticket->id }}</td>
                        <td class="p-2 border text-center">
                            {{ $ticket->called_at ? \Carbon\Carbon::parse($ticket->called_at)->format('H:i:s') : '-' }}
                        </td>
                        <td class="p-2 border text-center">
                            {{ $ticket->finished_at ? \Carbon\Carbon::parse($ticket->finished_at)->format('H:i:s') : '-' }}
                        </td>
                        <td class="p-2 border text-center">
                            {{ $ticket->duration_formatted }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center text-gray-500">
                            Nenhum atendimento realizado hoje.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-6">
            <a href="{{ route('home') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Voltar
            </a>
        </div>
    </div>

</body>
</html>
