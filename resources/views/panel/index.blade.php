<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel de Chamadas Profissional</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Lora:ital,wght@0,400..700;1,400..700&family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        @keyframes pulse-glow-blue {
            0%, 100% { box-shadow: 0 0 8px 4px rgba(59, 130, 246, 0.7); }
            50% { box-shadow: 0 0 12px 6px rgba(59, 130, 246, 1); }
        }
        @keyframes pulse-glow-green {
            0%, 100% { box-shadow: 0 0 8px 4px rgba(22, 163, 74, 0.7); }
            50% { box-shadow: 0 0 12px 6px rgba(22, 163, 74, 1); }
        }
        .custom-pulse-blue { animation: pulse-glow-blue 2s infinite; }
        .custom-pulse-green { animation: pulse-glow-green 2s infinite; }
        h2, h1 { font-family: "Lora", serif; }
        p { font-family: "Roboto Slab", serif; }
    </style>
</head>
<body class="bg-gray-100 overflow-hidden">
    <h1 class="text-5xl uppercase tracking-wide font-bold text-[#213555] text-center pt-7">Painel de Chamadas:</h1>
    
    <section class="flex items-center justify-center min-h-screen -mt-10">
        <div class="flex flex-col w-[50%] h-screen p-20">
            <div class="h-[50vh] w-[120%] bg-gray-600 rounded-lg flex justify-center items-center">
                <iframe class="w-[100%] h-[100%] rounded-lg" src="{{ $videoUrl }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
            </div>

            <div class="h-[30vh] w-[120%] shadow-2xl bg-white rounded-lg mt-5 p-8">
                <h3 class="text-2xl font-bold mb-4">Últimos atendimentos:</h3>
                <div id="last-atendimentos-list" class="flex gap-4">
                    </div>
            </div>
        </div>

        <div class="flex flex-col w-[50%] gap-10 h-screen p-20 items-center">
            <div id="card-triagem" class="bg-white rounded-2xl shadow-xl h-fit p-8 w-[80%]">
                <h2 class="text-3xl font-bold text-blue-600 mb-4">Triagem</h2>
                <div id="triagem-list" class="space-y-2">
                    <p class="text-gray-500">Nenhum chamado</p>
                </div>
            </div>

            <div id="card-atendimento" class="bg-white rounded-2xl shadow-xl p-8 w-[80%] h-fit">
                <h2 class="text-3xl font-bold text-green-600 mb-4">Atendimento</h2>
                <div id="atendimento-list" class="space-y-2">
                    <p class="text-gray-500">Nenhum chamado</p>
                </div>
            </div>

            <button id="enable-sound-btn" class="px-5 py-5 bg-[#527cd1] text-white rounded-md hover:bg-[#223458] transition duration-300 w-[80%] shadow-lg">
                Ativar Som de Notificação
            </button>
        </div>
    </section>

    <audio id="notification-sound" src="/sounds/notification.mp3" preload="auto"></audio>

<script>
    const sound = document.getElementById('notification-sound');
    const enableSoundBtn = document.getElementById('enable-sound-btn');
    
    // VARIÁVEIS PARA CONTROLAR O QUE JÁ FOI TOCADO
    let lastTriagemKey = null;
    let lastAtendimentoKey = null;
    
    let soundEnabled = false;
    let speechQueue = [];
    let isSpeaking = false;

    function loadVoices() { window.speechSynthesis.getVoices(); }
    window.speechSynthesis.onvoiceschanged = loadVoices;
    loadVoices();

    enableSoundBtn.addEventListener('click', () => {
        soundEnabled = true;
        enableSoundBtn.style.display = 'none';
        sound.play().then(() => { sound.pause(); sound.currentTime = 0; });
        window.speechSynthesis.speak(new SpeechSynthesisUtterance(""));
    });

    async function fetchData() {
        try {
            const response = await fetch('{{ route('panel.data') }}');
            if (!response.ok) return;
            const data = await response.json();

            // VERIFICA TRIAGEM
            if (data.triagem && data.triagem.length > 0) {
                const t = data.triagem[0];
                // A CHAVE USA ID + TIMESTAMP (ESSENCIAL PARA O RECALL)
                const currentKey = `${t.id}-${t.last_called_at || t.updated_at}`;
                
                if (currentKey !== lastTriagemKey) {
                    lastTriagemKey = currentKey;
                    playNotification('card-triagem', 'blue', t, 'Triagem');
                }
            } else { lastTriagemKey = null; }

            // VERIFICA ATENDIMENTO
            if (data.atendimento && data.atendimento.length > 0) {
                const t = data.atendimento[0];
                const currentKey = `${t.id}-${t.last_called_at || t.updated_at}`;
                
                if (currentKey !== lastAtendimentoKey) {
                    lastAtendimentoKey = currentKey;
                    playNotification('card-atendimento', 'green', t, 'Atendimento');
                }
            } else { lastAtendimentoKey = null; }

            renderTickets('triagem', data.triagem || [], 'blue');
            renderTickets('atendimento', data.atendimento || [], 'green');
            renderLastAtendimentos(data.lastAtendimentos || []);

        } catch (error) { console.error('Erro ao buscar dados:', error); }
    }
    
    function renderLastAtendimentos(atendimentos) {
        const container = document.getElementById('last-atendimentos-list');
        if (!container) return;
        let html = atendimentos.length ? '' : '<p class="text-gray-500">Nenhum atendimento registrado</p>';
        atendimentos.forEach(atendimento => {
            html += `
                <div class="flex-shrink-0 w-[32%] bg-white p-3 rounded-lg shadow-md border border-gray-300">
                    <p class="font-bold text-3xl text-[#213555] mb-2">#${atendimento.id}</p>
                    <p class="text-gray-600 mb-2">${atendimento.finished_at || ''}</p>
                    ${atendimento.guiche ? `<p class="text-green-600 font-semibold">${atendimento.guiche}</p>` : ''}
                </div>`;
        });
        container.innerHTML = html;
    }

    function renderTickets(type, tickets, color) {
        const listElement = document.getElementById(`${type}-list`);
        const cardElement = document.getElementById(`card-${type}`);
        if (!listElement) return;

        listElement.innerHTML = tickets.length ? '' : '<p class="text-gray-500">Nenhum chamado</p>';
        
        if (tickets.length) {
            cardElement.classList.add(color === 'blue' ? 'custom-pulse-blue' : 'custom-pulse-green', 'ring-4', color === 'blue' ? 'ring-blue-400' : 'ring-green-400');
        } else {
            cardElement.classList.remove('custom-pulse-blue', 'custom-pulse-green', 'ring-4', 'ring-blue-400', 'ring-green-400');
        }

        tickets.forEach(ticket => {
            const div = document.createElement('div');
            div.className = 'p-3 rounded-xl border border-gray-200 shadow-sm bg-white';
            div.innerHTML = `
                <p class="text-4xl font-bold ${color === 'blue' ? 'text-blue-800' : 'text-green-800'}">#${ticket.id}</p>
                <p class="text-sm text-gray-500">Chamado às ${ticket.called_at || ''}</p>
                ${ticket.guiche ? `<p class="text-2xl text-gray-800 font-bold">${ticket.guiche}</p>` : ''}
            `;
            listElement.appendChild(div);
        });
    }

    function playNotification(cardId, color, ticket, tipo) {
        if (!soundEnabled || !ticket) return;
        sound.pause();
        sound.currentTime = 0;
        let frase = `Senha número ${ticket.id}. ` + (tipo === 'Triagem' ? `Dirija-se à triagem.` : `Dirija-se ao ${ticket.guiche || 'atendimento'}.`);
        sound.play().then(() => {
            speechQueue.push(frase);
            setTimeout(processQueue, 1500);
        }).catch(() => { speechQueue.push(frase); processQueue(); });
    }

    function processQueue() {
        if (isSpeaking || speechQueue.length === 0) return;
        isSpeaking = true;
        const msg = new SpeechSynthesisUtterance(speechQueue.shift());
        msg.lang = 'pt-BR';
        msg.onend = () => { isSpeaking = false; setTimeout(processQueue, 300); };
        window.speechSynthesis.speak(msg);
    }

    setInterval(fetchData, 3000); 
    fetchData();
</script>
</body>
</html>