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
        p{
            font-family: "Roboto Slab", serif;
        }
        h1{
            font-family: 'Libre Baskerville', serif;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen w-screen">

    <div class="p-2 rounded">
        <h1 class="text-6xl mb-2 text-center text-[#213555] font-black uppercase">Retirar Senha</h1>

       <form action="{{ route('ticket.take.post') }}" method="POST">
        @csrf

            <label class="block mb-10 text-xl text-[#213555]/80 text-center uppercase font-bold">Toque na tela para escolher o tipo de senha:</label>
            <div class="flex justify-center gap-5">
                <!-- Botão Regular -->
                <button 
                    type="submit" 
                    name="type" 
                    value="regular"
                    class="group flex w-[700px] h-[400px] flex-col font-semibold text-lg items-center justify-center shadow-xl bg-white text-black px-10 py-8 rounded-[25px] hover:bg-[#7cccfa] hover:text-white transition duration-300"
                >
                    <img src="{{ asset('images/regular.png') }}" alt="atendente" class="w-35">
                    <h3 class="text-2xl font-black uppercase tracking-wider text-[#213555] group-hover:text-[#f3f8ff] transition duration-300">
                        Regular
                    </h3>
                    <p class="text-md text-[#213555]/70 group-hover:text-[#f9f9fa] transition duration-300">
                        A ficha regular é destinada ao atendimento padrão, seguindo a ordem de chegada.
                        
                    </p>
                </button>

                <!-- Botão Preferencial -->
                <button 
                    type="submit" 
                    name="type" 
                    value="preferencial"
                    class="group flex w-[700px] h-[400px] flex-col font-semibold text-lg items-center justify-center shadow-xl bg-white text-black px-10 py-8 rounded-[25px] hover:bg-[#7cccfa] hover:text-white transition duration-300"
                >
                    <img src="{{ asset('images/preferencial.png') }}" alt="atendente" class="w-35">
                    <h3 class="text-2xl font-black uppercase tracking-wider text-[#213555] group-hover:text-[#f3f8ff] transition duration-300">
                        Preferencial
                    </h3>
                    <p class="text-md text-[#213555]/70 group-hover:text-[#f9f9fa] transition duration-300">
                        A ficha preferencial é destinada a pessoas que têm direito a atendimento prioritário.
                    </p>
                </button>

        </div>
    </form>
    </div>

</body>
</html>
