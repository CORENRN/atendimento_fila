<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel de Chamadas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>

        @keyframes pulse-glow-blue {
        0%, 100% {
            box-shadow: 0 0 8px 4px rgba(59, 130, 246, 0.7); /* azul */
        }
        50% {
            box-shadow: 0 0 12px 6px rgba(59, 130, 246, 1);
        }
        }

        @keyframes pulse-glow-green {
        0%, 100% {
            box-shadow: 0 0 8px 4px rgba(22, 163, 74, 0.7); /* verde */
        }
        50% {
            box-shadow: 0 0 12px 6px rgba(22, 163, 74, 1);
        }
        }

        .custom-pulse-blue {
        animation: pulse-glow-blue 2s infinite;
        }

        .custom-pulse-green {
        animation: pulse-glow-green 2s infinite;
        }

        h1, h3
        {
            font-family: 'Libre Baskerville', serif;
        }

    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="flex flex-col w-[50%] h-screen p-20">
        <h1 class="text-6xl font-bold mb-10">Painel de Chamadas:</h1>
        <div class="h-[50vh] w-[100%] bg-gray-600 rounded-lg flex justify-center items-center">
            <iframe class="w-[100%] h-[100%]" src="{{ $videoUrl }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

            
        </div>

        @if(auth()->user() && auth()->user()->categoria === 'superAdmin')
            <div class="mt-10 bg-white p-6 rounded-lg shadow-lg w-full">
                <h2 class="text-2xl font-bold mb-4">Atualizar Vídeo do Painel</h2>
                @if(session('success'))
                    <div class="bg-green-100 text-green-800 p-2 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif
                <form action="{{ route('panel.updateVideo') }}" method="POST">
                    @csrf
                    <input type="url" name="video_url" placeholder="https://www.youtube.com/embed/..." class="w-full p-3 border border-gray-300 rounded mb-4" value="{{ $videoUrl }}" required>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Atualizar Vídeo</button>
                </form>
            </div>
        @endif

        <div class="h-[20vh] w-[100%] shadow-2xl rounded-lg mt-5 p-8">
            <h3 class="text-2xl font-bold">Ultimos atendimentos:</h3>
        </div>
    </div>

    <div class="flex flex-col w-[50%] gap-10 h-screen p-20 items-center">
        <h1 class="text-6xl font-bold">Senhas:</h1>
         <!-- Card Triagem -->
        <div id="card-triagem" class="bg-white rounded-2xl shadow-xl p-8 w-[80%] min-h-[25vh]">
            <h2 class="text-3xl font-bold text-blue-600 mb-4">Triagem</h2>
            <div id="triagem-list" class="space-y-2">
                <p class="text-gray-500">Nenhum chamado</p>
            </div>
        </div>

        <!-- Card Atendimento -->
        <div id="card-atendimento" class="bg-white rounded-2xl shadow-xl p-8 w-[80%] min-h-[25vh]">
            <h2 class="text-3xl font-bold text-green-600 mb-4">Atendimento</h2>
            <div id="atendimento-list" class="space-y-2">
                <p class="text-gray-500">Nenhum chamado</p>
            </div>
        </div>

        <button id="enable-sound-btn" class="px-5 py-5 bg-blue-600 text-white rounded-md hover:bg-blue-700 w-[80%]">
            Ativar Som de Notificação
        </button>
    </div>
    

    <audio id="notification-sound" src="/sounds/notification.mp3" preload="auto"></audio>
<script>
    const sound = document.getElementById('notification-sound');
    const enableSoundBtn = document.getElementById('enable-sound-btn');

    let lastTriagemKeys = [];
    let lastAtendimentoKeys = [];

    let soundEnabled = false;

    enableSoundBtn.addEventListener('click', () => {
        sound.play().then(() => {
            sound.pause();
            sound.currentTime = 0;
            soundEnabled = true;
            enableSoundBtn.style.display = 'none';
        }).catch(() => {
            alert('Não foi possível ativar o som. Por favor, permita a reprodução.');
        });
    });

    function getIdWithTimestamp(list) {
        return list.map(item => `${item.id}-${item.called_at}`);
    }

    async function fetchData() {
        try {
            const response = await fetch('{{ route('panel.data') }}');
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            const data = await response.json();

            const triagemKeys = getIdWithTimestamp(data.triagem || []);
            const atendimentoKeys = getIdWithTimestamp(data.atendimento || []);

            // Verifica atualizações na triagem
            const novosTriagem = triagemKeys.filter(k => !lastTriagemKeys.includes(k));
            if (novosTriagem.length > 0) {
                playNotification('card-triagem', 'blue');
            }

            // Verifica atualizações no atendimento
            const novosAtendimento = atendimentoKeys.filter(k => !lastAtendimentoKeys.includes(k));
            if (novosAtendimento.length > 0) {
                playNotification('card-atendimento', 'green');
            }

            lastTriagemKeys = triagemKeys;
            lastAtendimentoKeys = atendimentoKeys;

            renderTickets('triagem', data.triagem || [], 'blue');
            renderTickets('atendimento', data.atendimento || [], 'green');

        } catch (error) {
            console.error('Erro ao buscar dados:', error);
        }
    }

    function renderTickets(type, tickets, color) {
        const listElement = document.getElementById(`${type}-list`);
        const cardElement = document.getElementById(`card-${type}`);

        if (!listElement || !cardElement) return;

        listElement.innerHTML = '';

        if (tickets.length === 0) {
            listElement.innerHTML = `<p class="text-gray-500">Nenhum chamado</p>`;
            cardElement.classList.remove(
                'custom-pulse-blue', 'custom-pulse-green', 'ring-blue-400', 'ring-green-400', 'ring-4'
            );
            return;
        }

        cardElement.classList.remove(
            'custom-pulse-blue', 'custom-pulse-green', 'ring-blue-400', 'ring-green-400', 'ring-4'
        );
        if (color === 'blue') {
            cardElement.classList.add('custom-pulse-blue', 'ring-blue-400', 'ring-4');
        } else if (color === 'green') {
            cardElement.classList.add('custom-pulse-green', 'ring-green-400', 'ring-4');
        }

        tickets.forEach(ticket => {
            const div = document.createElement('div');
            div.className = 'p-3 rounded-xl border border-gray-200 shadow-sm bg-white';

            div.innerHTML = `
                <p class="text-4xl font-bold ${color === 'blue' ? 'text-blue-800' : 'text-green-800'}">${ticket.id}</p>
                <p class="text-sm text-gray-500">Chamado às ${ticket.called_at}</p>
                ${ticket.guiche ? `<p class="text-2xl text-gray-800 font-bold">Guichê: ${ticket.guiche}</p>` : ''}
            `;

            listElement.appendChild(div);
        });
    }

    function playNotification(cardId, color) {
        if (!soundEnabled) return;

        const card = document.getElementById(cardId);
        if (!card) return;

        let playCount = 0;
        const maxPlays = 1;

        function playSoundRepeatedly() {
            if (playCount >= maxPlays) return;
            sound.currentTime = 0;
            sound.play().then(() => {
                playCount++;
                sound.onended = playSoundRepeatedly;
            }).catch(() => {
                // Falha ao reproduzir som (ex: bloqueio do navegador)
            });
        }
        playSoundRepeatedly();

        card.classList.remove('custom-pulse-blue', 'custom-pulse-green', 'ring-blue-400', 'ring-green-400', 'ring-4');

        if (color === 'blue') {
            card.classList.add('custom-pulse-blue', 'ring-blue-400', 'ring-4');
        } else if (color === 'green') {
            card.classList.add('custom-pulse-green', 'ring-green-400', 'ring-4');
        }
    }

    setInterval(fetchData, 3000);
    fetchData();
</script>







</body>
</html>
