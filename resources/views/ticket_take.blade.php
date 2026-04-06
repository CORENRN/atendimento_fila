<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Retirar Senha</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Lora:ital,wght@0,400..700;1,400..700&family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-image: url('/images/bgticket.png');
            background-size: cover;
            background-position: bottom;
            background-repeat: no-repeat;
        }
        p { font-family: "Roboto Slab", serif; }
        h1 { font-family: 'Libre Baskerville', serif; }
        
        .no-select {
            user-select: none;
            -webkit-user-select: none;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen w-screen overflow-hidden">

    <div class="p-2 rounded">
        <h1 class="text-6xl mb-2 text-center text-[#213555] font-black uppercase">Retirar Senha</h1>

        <form action="{{ route('ticket.take.post') }}" method="POST">
            @csrf
            <label class="block mb-10 text-xl text-[#213555]/80 text-center uppercase font-bold">Toque na tela para escolher o tipo de senha:</label>
            <div class="flex justify-center gap-5">

                <button type="submit" name="type" value="regular" class="group flex w-[550px] h-[400px] flex-col font-semibold text-lg items-center justify-center shadow-xl bg-white text-black px-10 py-8 rounded-[25px] hover:bg-[#7cccfa] hover:text-white transition duration-300">
                    <img src="{{ asset('images/regular.png') }}" alt="atendente" class="w-35">
                    <h3 class="text-2xl font-black uppercase tracking-wider text-[#213555] group-hover:text-[#f3f8ff] transition duration-300">Regular</h3>
                    <p class="text-md text-[#213555]/70 group-hover:text-[#f9f9fa] transition duration-300">A ficha regular é destinada ao atendimento padrão, seguindo a ordem de chegada.</p>
                </button>

                <button type="submit" name="type" value="preferencial" class="group flex w-[550px] h-[400px] flex-col font-semibold text-lg items-center justify-center shadow-xl bg-white text-black px-10 py-8 rounded-[25px] hover:bg-[#7cccfa] hover:text-white transition duration-300">
                    <img src="{{ asset('images/preferencial.png') }}" alt="atendente" class="w-35">
                    <h3 class="text-2xl font-black uppercase tracking-wider text-[#213555] group-hover:text-[#f3f8ff] transition duration-300">Preferencial</h3>
                    <p class="text-md text-[#213555]/70 group-hover:text-[#f9f9fa] transition duration-300">A ficha preferencial é destinada a pessoas que têm direito a atendimento prioritário.</p>
                </button>

                <button type="button" onclick="abrirModal()" class="group flex w-[550px] h-[400px] flex-col font-semibold text-lg items-center justify-center shadow-xl bg-white text-black px-10 py-8 rounded-[25px] hover:bg-[#7cccfa] hover:text-white transition duration-300">
                    <img src="{{ asset('images/cartao.png') }}" alt="atendente" class="w-24">
                    <h3 class="text-2xl font-black uppercase tracking-wider text-[#213555] group-hover:text-[#f3f8ff] transition duration-300">Retirada de Carteira</h3>
                    <p class="text-md text-red-500 group-hover:text-red transition duration-300">Destinado para pessoas que solicitaram a confecção de nova carteira de enfermagem.</p>
                </button>

            </div>
        </form>
    </div>

    <div id="modal-cpf" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50">
        <div class="bg-white rounded-[30px] shadow-2xl p-8 w-[500px] flex flex-col items-center gap-4">

            <h2 class="text-3xl font-black uppercase text-[#213555] tracking-wider text-center">Digite seu CPF</h2>
            
            <form action="{{ route('ticket.take.post') }}" method="POST" id="form-cpf" onsubmit="return validarEnvio()" class="w-full flex flex-col gap-4">
                @csrf
                <input type="hidden" name="type" value="carteira">

                <input 
                    type="text" 
                    name="cpf"
                    id="cpf-input"
                    placeholder="000.000.000-00"
                    readonly
                    class="w-full border-2 border-[#213555]/20 rounded-2xl px-5 py-4 text-3xl text-center tracking-widest text-[#213555] bg-gray-50 focus:outline-none"
                    required
                >

                <div class="grid grid-cols-3 gap-3 w-full mt-2 no-select">
                    <button type="button" onclick="addNumero('1')" class="h-16 bg-gray-100 rounded-xl text-2xl font-bold text-[#213555] active:bg-[#7cccfa] active:text-white transition">1</button>
                    <button type="button" onclick="addNumero('2')" class="h-16 bg-gray-100 rounded-xl text-2xl font-bold text-[#213555] active:bg-[#7cccfa] active:text-white transition">2</button>
                    <button type="button" onclick="addNumero('3')" class="h-16 bg-gray-100 rounded-xl text-2xl font-bold text-[#213555] active:bg-[#7cccfa] active:text-white transition">3</button>
                    <button type="button" onclick="addNumero('4')" class="h-16 bg-gray-100 rounded-xl text-2xl font-bold text-[#213555] active:bg-[#7cccfa] active:text-white transition">4</button>
                    <button type="button" onclick="addNumero('5')" class="h-16 bg-gray-100 rounded-xl text-2xl font-bold text-[#213555] active:bg-[#7cccfa] active:text-white transition">5</button>
                    <button type="button" onclick="addNumero('6')" class="h-16 bg-gray-100 rounded-xl text-2xl font-bold text-[#213555] active:bg-[#7cccfa] active:text-white transition">6</button>
                    <button type="button" onclick="addNumero('7')" class="h-16 bg-gray-100 rounded-xl text-2xl font-bold text-[#213555] active:bg-[#7cccfa] active:text-white transition">7</button>
                    <button type="button" onclick="addNumero('8')" class="h-16 bg-gray-100 rounded-xl text-2xl font-bold text-[#213555] active:bg-[#7cccfa] active:text-white transition">8</button>
                    <button type="button" onclick="addNumero('9')" class="h-16 bg-gray-100 rounded-xl text-2xl font-bold text-[#213555] active:bg-[#7cccfa] active:text-white transition">9</button>
                    
                    <button type="button" onclick="limparCpf()" class="h-16 bg-red-50 text-red-500 rounded-xl font-bold uppercase text-xs active:bg-red-500 active:text-white transition">Limpar</button>
                    <button type="button" onclick="addNumero('0')" class="h-16 bg-gray-100 rounded-xl text-2xl font-bold text-[#213555] active:bg-[#7cccfa] active:text-white transition">0</button>
                    <button type="button" onclick="apagarUltimo()" class="h-16 bg-gray-200 rounded-xl text-[#213555] active:bg-gray-300 transition">
                        <i class="fa-solid fa-delete-left text-xl"></i>
                    </button>
                </div>

                <button 
                    id="btn-confirmar"
                    type="submit"
                    class="w-full bg-[#213555] text-white text-xl font-black uppercase tracking-wider py-5 rounded-2xl opacity-50 cursor-not-allowed transition duration-300 mt-4 shadow-lg"
                    disabled
                >
                    Confirmar
                </button>
            </form>

            <button onclick="fecharModal()" class="text-[#213555]/50 hover:text-red-500 text-sm uppercase font-bold tracking-widest transition">Cancelar</button>
        </div>
    </div>
 <script>
        const cpfInput = document.getElementById('cpf-input');
        const btnConfirmar = document.getElementById('btn-confirmar');

        function abrirModal() {
            document.getElementById('modal-cpf').classList.remove('hidden');
        }

        function fecharModal() {
            document.getElementById('modal-cpf').classList.add('hidden');
            limparCpf();
        }

        function addNumero(num) {
            let valor = cpfInput.value.replace(/\D/g, '');
            if (valor.length < 11) {
                valor += num;
                cpfInput.value = formatarStringCpf(valor);
            }
            validarBotao();
        }

        function apagarUltimo() {
            let valor = cpfInput.value.replace(/\D/g, '');
            valor = valor.slice(0, -1);
            cpfInput.value = formatarStringCpf(valor);
            validarBotao();
        }

        function limparCpf() {
            cpfInput.value = '';
            validarBotao();
        }

        function formatarStringCpf(v) {
            v = v.replace(/\D/g, '');
            if (v.length > 9) v = v.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
            else if (v.length > 6) v = v.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
            else if (v.length > 3) v = v.replace(/(\d{3})(\d{1,3})/, '$1.$2');
            return v;
        }

        // Habilita/Desabilita o botão visualmente
        function validarBotao() {
            const valor = cpfInput.value.replace(/\D/g, '');
            if (valor.length === 11) {
                btnConfirmar.disabled = false;
                btnConfirmar.classList.remove('opacity-50', 'cursor-not-allowed');
                btnConfirmar.classList.add('hover:bg-[#7cccfa]');
            } else {
                btnConfirmar.disabled = true;
                btnConfirmar.classList.add('opacity-50', 'cursor-not-allowed');
                btnConfirmar.classList.remove('hover:bg-[#7cccfa]');
            }
        }

        // Bloqueia o envio se o formulário for forçado (Enter, etc) sem 11 dígitos
        function validarEnvio() {
            const valor = cpfInput.value.replace(/\D/g, '');
            if (valor.length !== 11) {
                alert("Por favor, preencha o CPF completo.");
                return false;
            }
            return true;
        }

        document.getElementById('modal-cpf').addEventListener('click', function(e) {
            if (e.target === this) fecharModal();
        });
    </script>
</body> 
