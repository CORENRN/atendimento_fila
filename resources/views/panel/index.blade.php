<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel de Chamadas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center min-h-screen">

    <h1 class="text-4xl font-bold mb-10">Painel de Chamadas</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

        <!-- Card Triagem -->
    <div class="bg-white rounded-2xl shadow-xl p-8 w-96">
        <h2 class="text-3xl font-bold text-blue-600 mb-4">Triagem</h2>

        <p class="text-lg text-gray-600">Ticket:</p>
        <p id="triagem-ticket" class="text-5xl font-extrabold text-blue-800 my-4">----</p>
        <p id="triagem-hora" class="text-md text-gray-500">Chamado às --:--:--</p>
    </div>

    <!-- Card Atendimento -->
    <div class="bg-white rounded-2xl shadow-xl p-8 w-96">
        <h2 class="text-3xl font-bold text-green-600 mb-4">Atendimento</h2>

        <p class="text-lg text-gray-600">Ticket:</p>
        <p id="atendimento-ticket" class="text-5xl font-extrabold text-green-800 my-4">----</p>
        <p id="atendimento-hora" class="text-md text-gray-500">Chamado às --:--:--</p>
        <p id="atendimento-guiche" class="text-md text-gray-800 font-semibold">Guichê: --</p>
    </div>


    </div>

    <div class="mt-10">
        <p class="text-gray-500 text-sm">Atualizando automaticamente...</p>
    </div>

    <script>
        async function fetchData() {
            try {
                const response = await fetch('{{ route('panel.data') }}');
                const data = await response.json();

                if (data.triagem) {
                    document.getElementById('triagem-ticket').textContent = data.triagem.id;
                    document.getElementById('triagem-hora').textContent = 'Chamado às ' + data.triagem.called_at;
                } else {
                    document.getElementById('triagem-ticket').textContent = '----';
                    document.getElementById('triagem-hora').textContent = 'Nenhum chamado';
                }

                if (data.atendimento) {
                    document.getElementById('atendimento-ticket').textContent = data.atendimento.id;
                    document.getElementById('atendimento-hora').textContent = 'Chamado às ' + data.atendimento.called_at;
                    document.getElementById('atendimento-guiche').textContent = 'Guichê: ' + (data.atendimento.guiche ?? '--');
                } else {
                    document.getElementById('atendimento-ticket').textContent = '----';
                    document.getElementById('atendimento-hora').textContent = 'Nenhum chamado';
                    document.getElementById('atendimento-guiche').textContent = 'Guichê: --';
                }

            } catch (error) {
                console.error('Erro ao buscar dados:', error);
            }
        }

        setInterval(fetchData, 3000);
        fetchData();
        ;
    </script>

</body>
</html>
