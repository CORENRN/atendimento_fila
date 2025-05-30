<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Seu Ticket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-image: url('/images/bgticket.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        h1, p{
            font-family: 'Libre Baskerville', serif;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="flex flex-col gap-3 p-8 rounded text-center">
        <h1 class="text-7xl text-white font-bold mb-4">Sua Senha</h1>
        <p class="text-7xl text-white font-bold mb-6">#00{{ $ticket->id }}</p>
        <p class="mb-2 text-white text-4xl">Tipo: 
            <strong class="{{ $ticket->type === 'preferencial' ? 'text-red-600' : 'text-blue-300' }} bg-gray-800 py-3 px-4 rounded-lg">
                {{ ucfirst($ticket->type) }}
            </strong>
        </p>
        <p class="mb-8 text-4xl text-white/80 tracking-widest">Aguarde ser chamado na triagem.</p>

        <a href="{{ route('ticket.take') }}" class="bg-sky-500 text-3xl text-white px-4 py-8 rounded hover:bg-blue-600 transition duration-300">
            Voltar para o início
        </a>
    </div>

</body>
</html>
