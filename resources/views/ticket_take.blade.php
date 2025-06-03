<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Retirar Senha</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-image: url('/images/bgticket.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        h1{
            font-family: 'Libre Baskerville', serif;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="p-8 rounded">
        <h1 class="text-7xl mb-2 text-center text-white font-black uppercase">Retirar Senha</h1>

       <form action="{{ route('ticket.take.post') }}" method="POST">
        @csrf

            <label class="block mb-2 text-3xl text-white/80 text-center tracking-wider uppercase font-bold">Toque na tela para escolher o tipo de senha:</label>
            <div class="flex justify-center gap-4">
                <!-- Botão Regular -->
                <button 
                    type="submit" 
                    name="type" 
                    value="regular"
                    class="group flex flex-col font-semibold text-lg items-center justify-center bg-white text-black px-10 py-8 w-60 h-32 rounded hover:bg-sky-400 hover:text-white transition duration-300"
                >
                    <!-- Ícone de usuário -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mb-2 transition group-hover:fill-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                    Regular
                </button>

                <!-- Botão Preferencial -->
                <button 
                    type="submit" 
                    name="type" 
                    value="preferencial"
                    class="group flex flex-col font-semibold text-lg items-center justify-center bg-white text-black px-10 py-8 w-60 h-32 rounded hover:bg-sky-400 hover:text-white transition duration-300"
                >
                    <!-- Ícone de idoso com bengala -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mb-2 transition group-hover:fill-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                    </svg>
                    Preferencial
                </button>

        </div>
    </form>
    </div>

</body>
</html>
