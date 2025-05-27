<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Seu Ticket</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="bg-white p-8 rounded shadow-lg text-center">
        <h1 class="text-2xl font-bold mb-4">Sua Senha</h1>
        <p class="text-4xl font-bold mb-6">#{{ $ticket->id }}</p>
        <p class="mb-2">Tipo: 
            <strong class="{{ $ticket->type === 'preferencial' ? 'text-red-600' : 'text-blue-600' }}">
                {{ ucfirst($ticket->type) }}
            </strong>
        </p>
        <p class="mb-8">Aguarde ser chamado na triagem.</p>

        <a href="{{ route('home') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Voltar para o início
        </a>
    </div>

</body>
</html>
