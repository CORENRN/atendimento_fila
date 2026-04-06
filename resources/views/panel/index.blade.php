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
    <script src="https://www.youtube.com/iframe_api"></script>
    <style>
        @keyframes pulse-glow-blue { 0%, 100% { box-shadow: 0 0 8px 4px rgba(59, 130, 246, 0.7); } 50% { box-shadow: 0 0 12px 6px rgba(59, 130, 246, 1); } }
        @keyframes pulse-glow-green { 0%, 100% { box-shadow: 0 0 8px 4px rgba(22, 163, 74, 0.7); } 50% { box-shadow: 0 0 12px 6px rgba(22, 163, 74, 1); } }
        @keyframes pulse-glow-amber { 0%, 100% { box-shadow: 0 0 8px 4px rgba(245, 158, 11, 0.7); } 50% { box-shadow: 0 0 12px 6px rgba(245, 158, 11, 1); } }
        .custom-pulse-blue { animation: pulse-glow-blue 2s infinite; }
        .custom-pulse-green { animation: pulse-glow-green 2s infinite; }
        .custom-pulse-amber { animation: pulse-glow-amber 2s infinite; }
        h2, h1 { font-family: "Lora", serif; }
        p { font-family: "Roboto Slab", serif; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="bg-gray-100 overflow-hidden">
    <h1 class="text-5xl uppercase tracking-wide font-bold text-[#213555] text-center pt-7">Painel de Chamadas</h1>
    
    <section class="flex items-center justify-center min-h-screen -mt-12">
        <div class="flex flex-col w-[50%] h-screen p-20">
            <div class="h-[50vh] w-[120%] bg-gray-600 rounded-lg flex justify-center items-center overflow-hidden shadow-2xl">
                <iframe id="youtube-player" class="w-full h-full" src="{{ $videoUrl }}?enablejsapi=1&autoplay=1&mute=1" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
            </div>

            <div class="h-[30vh] w-[120%] shadow-2xl bg-white rounded-lg mt-5 p-8 overflow-hidden">
                <h3 class="text-2xl font-bold mb-4">Últimas fichas finalizadas:</h3>
                <div id="last-atendimentos-list" class="flex gap-4 overflow-x-auto hide-scrollbar">
                    <p class="text-gray-500 text-sm">Carregando...</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col w-[50%] gap-6 h-screen p-10 items-center mt-20">
            <div id="card-triagem" class="bg-white rounded-2xl shadow-xl p-6 w-[85%] transition-all duration-500">
                <h2 class="text-3xl font-bold text-blue-600 mb-2 border-b pb-2">Triagem</h2>
                <div id="triagem-list" class="space-y-2 mt-3">
                    <p class="text-gray-500 text-sm italic">Aguardando chamado...</p>
                </div>
            </div>

            <div id="card-atendimento" class="bg-white rounded-2xl shadow-xl p-6 w-[85%] transition-all duration-500">
                <h2 class="text-3xl font-bold text-green-600 mb-2 border-b pb-2">Atendimento</h2>
                <div id="atendimento-list" class="grid grid-cols-1 gap-3 mt-3">
                    <p class="text-gray-500 text-sm italic">Aguardando chamado...</p>
                </div>
            </div>

            <div id="card-carteira" class="bg-white rounded-2xl shadow-xl p-6 w-[85%] transition-all duration-500">
                <h2 class="text-3xl font-bold text-amber-500 mb-2 border-b pb-2">Retirada de Carteira</h2>
                <div id="carteira-list" class="grid grid-cols-1 gap-3 mt-3">
                    <p class="text-gray-500 text-sm italic">Aguardando chamado...</p>
                </div>
            </div>

            <button id="enable-sound-btn" class="px-5 py-4 bg-[#527cd1] text-white rounded-md hover:bg-[#223458] transition duration-300 w-[85%] shadow-lg font-bold uppercase tracking-widest mt-4">
                🔈 Ativar Som e Voz
            </button>
        </div>
    </section>

    <audio id="notification-sound" src="/sounds/notification.mp3" preload="auto"></audio>

<script>
    let ytPlayer;
    let voices = [];

    function onYouTubeIframeAPIReady() {
        ytPlayer = new YT.Player('youtube-player', {
            events: { 'onReady': (event) => { event.target.playVideo(); } }
        });
    }

    function loadVoices() { voices = window.speechSynthesis.getVoices(); }
    window.speechSynthesis.onvoiceschanged = loadVoices;
    loadVoices();

    const sound = document.getElementById('notification-sound');
    const enableSoundBtn = document.getElementById('enable-sound-btn');
    
    let lastTriagemKey = null;
    let lastAtendimentoKey = null;
    let knownCarteiraKeys = new Set();
    let soundEnabled = false;
    let speechQueue = [];
    let isSpeaking = false;

    enableSoundBtn.addEventListener('click', () => {
        soundEnabled = true;
        enableSoundBtn.style.display = 'none';
        sound.play().then(() => { sound.pause(); sound.currentTime = 0; });
        window.speechSynthesis.speak(new SpeechSynthesisUtterance(""));
    });

    async function fetchData() {
        try {
            const response = await fetch('{{ route("panel.data") }}');
            if (!response.ok) return;
            const data = await response.json();

            // 1. TRIAGEM
            const triagemTickets = data.triagem || [];
            renderTickets('triagem', triagemTickets, 'blue');
            if (triagemTickets.length > 0) {
                const t = triagemTickets[0];
                const key = `${t.id}-${t.last_called_at || t.updated_at}`;
                if (key !== lastTriagemKey) {
                    lastTriagemKey = key;
                    notify(t, 'Triagem');
                }
            }

            // 2. ATENDIMENTO
            const atendimentoTickets = data.atendimento || [];
            renderTickets('atendimento', atendimentoTickets, 'green');
            if (atendimentoTickets.length > 0) {
                const t = atendimentoTickets[0];
                const key = `${t.id}-${t.last_called_at || t.updated_at}`;
                if (key !== lastAtendimentoKey) {
                    lastAtendimentoKey = key;
                    notify(t, 'Atendimento');
                }
            }

            // 3. CARTEIRA
            const carteiraTickets = data.carteira || [];
            renderTickets('carteira', carteiraTickets, 'amber');
            carteiraTickets.forEach(t => {
                const key = `${t.id}-${t.last_called_at || t.updated_at}`;
                if (!knownCarteiraKeys.has(key)) {
                    knownCarteiraKeys.add(key);
                    notify(t, 'Carteira');
                }
            });

            const currentCarteiraKeys = new Set(carteiraTickets.map(t => `${t.id}-${t.last_called_at || t.updated_at}`));
            knownCarteiraKeys.forEach(k => { if(!currentCarteiraKeys.has(k)) knownCarteiraKeys.delete(k); });

            if (data.lastAtendimentos) renderLastAtendimentos(data.lastAtendimentos);

        } catch (error) { console.error('Erro:', error); }
    }

    function renderTickets(type, tickets, color) {
        const list = document.getElementById(`${type}-list`);
        const card = document.getElementById(`card-${type}`);
        
        if (tickets.length === 0) {
            list.innerHTML = `<p class="text-gray-500 text-sm italic col-span-2">Aguardando chamado...</p>`;
            card.classList.remove(`custom-pulse-${color}`, 'ring-4', `ring-${color}-400`);
            return;
        }

        // LÓGICA DE COLUNAS: Se houver mais de 1 ticket, quebra em 2 colunas. Se tiver 1, ocupa a linha toda.
        if (tickets.length > 1) {
            list.classList.remove('grid-cols-1');
            list.classList.add('grid-cols-2');
        } else {
            list.classList.remove('grid-cols-2');
            list.classList.add('grid-cols-1');
        }

        card.classList.add(`custom-pulse-${color}`, 'ring-4', `ring-${color}-400`);
        list.innerHTML = tickets.map(t => `
            <div class="p-3 rounded-xl border border-gray-100 shadow-sm bg-gray-50 flex justify-between items-center">
                <div>
                    <p class="text-4xl font-black text-gray-800">#${t.id}</p>
                    <p class="text-[12px] text-gray-400 uppercase font-bold">${t.type || 'REGULAR'}</p>
                </div>
                <div class="text-right">
                    <p class="text-lg font-bold text-${color}-600">${t.guiche || ''}</p>
                    <p class="text-[20px] text-gray-400 italic">${t.called_at || ''}</p>
                </div>
            </div>
        `).join('');
    }

    function renderLastAtendimentos(atendimentos) {
        const container = document.getElementById('last-atendimentos-list');
        container.innerHTML = atendimentos.map(a => `
            <div class="flex-shrink-0 w-40 bg-gray-50 p-4 rounded-lg border-t-4 border-[#213555] shadow-sm">
                <p class="font-black text-2xl text-[#213555]">#${a.id}</p>
                <p class="text-[10px] text-gray-500 font-bold uppercase">${a.guiche || 'Finalizado'}</p>
            </div>
        `).join('');
    }

    function notify(ticket, tipo) {
        if (!soundEnabled) return;
        if (ytPlayer && ytPlayer.setVolume) ytPlayer.setVolume(10);

        sound.pause();
        sound.currentTime = 0;
        sound.play().catch(() => {});

        let frase = `Senha número ${ticket.id}. `;
        if (tipo === 'Triagem') frase += "Dirija-se à triagem.";
        else if (tipo === 'Carteira') frase += "Por favor, dirija-se ao guichê para retirar sua carteira.";
        else frase += `Dirija-se ao ${ticket.guiche || 'atendimento'}.`;

        speechQueue.push(frase);
        if (!isSpeaking) setTimeout(processQueue, 1500);
    }

    function processQueue() {
        if (isSpeaking || speechQueue.length === 0) {
            if (speechQueue.length === 0 && !isSpeaking && ytPlayer && ytPlayer.setVolume) ytPlayer.setVolume(100);
            return;
        }

        isSpeaking = true;
        const msg = new SpeechSynthesisUtterance(speechQueue.shift());
        const naturalVoice = voices.find(v => v.lang === 'pt-BR' && v.name.includes('Google')) 
                          || voices.find(v => v.lang === 'pt-BR' && v.name.includes('Maria'))
                          || voices.find(v => v.lang === 'pt-BR');

        if (naturalVoice) msg.voice = naturalVoice;
        msg.lang = 'pt-BR';
        msg.rate = 0.85;
        msg.pitch = 1.0; 

        msg.onend = () => {
            isSpeaking = false;
            if (speechQueue.length > 0) setTimeout(processQueue, 600);
            else if (ytPlayer && ytPlayer.setVolume) ytPlayer.setVolume(100);
        };
        window.speechSynthesis.speak(msg);
    }

    setInterval(fetchData, 4000);
    fetchData();
</script>
</body>
</html>