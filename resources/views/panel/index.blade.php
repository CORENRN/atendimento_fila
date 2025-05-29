<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel de Chamadas</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

    </style>
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center min-h-screen">

    <h1 class="text-4xl font-bold mb-10">Painel de Chamadas</h1>

    <button id="enable-sound-btn" class="mb-6 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Ativar Som de Notificação
    </button>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

    <!-- Card Triagem -->
    <div id="card-triagem" class="bg-white rounded-2xl shadow-xl p-8 w-96">
        <h2 class="text-3xl font-bold text-blue-600 mb-4">Triagem</h2>
        <div id="triagem-list" class="space-y-2">
            <p class="text-gray-500">Nenhum chamado</p>
        </div>
    </div>

    <!-- Card Atendimento -->
    <div id="card-atendimento" class="bg-white rounded-2xl shadow-xl p-8 w-96">
        <h2 class="text-3xl font-bold text-green-600 mb-4">Atendimento</h2>
        <div id="atendimento-list" class="space-y-2">
            <p class="text-gray-500">Nenhum chamado</p>
        </div>
    </div>



    </div>

    <div class="mt-10">
        <p class="text-gray-500 text-sm">Atualizando automaticamente...</p>
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
        }).catch(e => {
            alert('Não foi possível ativar o som. Por favor, permita a reprodução.');
        });
    });

    function getIdWithTimestamp(list) {
        return list.map(item => `${item.id}-${item.called_at}`);
    }

    async function fetchData() {
        try {
            const response = await fetch('{{ route('panel.data') }}');
            const data = await response.json();

            const triagemKeys = getIdWithTimestamp(data.triagem);
            const atendimentoKeys = getIdWithTimestamp(data.atendimento);

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

            // Atualiza os estados
            lastTriagemKeys = triagemKeys;
            lastAtendimentoKeys = atendimentoKeys;

            renderTickets('triagem', data.triagem, 'blue');
            renderTickets('atendimento', data.atendimento, 'green');

        } catch (error) {
            console.error('Erro ao buscar dados:', error);
        }
    }

    function renderTickets(type, tickets, color) {
        const listElement = document.getElementById(`${type}-list`);
        const cardElement = document.getElementById(`card-${type}`);
        
        listElement.innerHTML = '';

        if (tickets.length === 0) {
            listElement.innerHTML = `<p class="text-gray-500">Nenhum chamado</p>`;
            cardElement.classList.remove(
                'custom-pulse-blue', 'custom-pulse-green', 'ring-blue-400', 'ring-green-400', 'ring-4'
            );
            return;
        }

        // Adiciona efeito visual
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
                ${ticket.guiche ? `<p class="text-sm text-gray-800 font-semibold">Guichê: ${ticket.guiche}</p>` : ''}
            `;

            listElement.appendChild(div);
        });
    }

    function playNotification(cardId, color) {
        if (!soundEnabled) return;

        let playCount = 0;
        const maxPlays = 1;

        function playSoundRepeatedly() {
            if (playCount >= maxPlays) return;
            sound.currentTime = 0;
            sound.play().then(() => {
                playCount++;
                sound.onended = playSoundRepeatedly;
            });
        }
        playSoundRepeatedly();

        const card = document.getElementById(cardId);

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
