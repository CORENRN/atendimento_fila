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
            background-image: url('/images/bgticket.png');
            background-size: cover;
            background-position: bottom;
            background-repeat: no-repeat;
        }
        h1, p{
            font-family: 'Libre Baskerville', serif;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="flex flex-col gap-3 p-8 rounded text-center items-center">
        <h1 class="text-4xl text-[#213555] font-bold mb-2">Sua senha foi cadastrada com sucesso!</h1>
        <p class="mb-8 text-lg font-bold text-[#213555]/80 w-[60%] ">Aguarde seu numero ser chamado no painel de atendimento.</p>
        
        <div class="w-full max-w-sm overflow-hidden rounded-lg bg-white shadow-md duration-300 hover:scale-105 hover:shadow-xl">
          <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto mt-8 h-16 w-16 text-green-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
          <h1 class="mt-2 text-center text-2xl font-bold text-[#213555]">#00{{ $ticket->id }}</h1>
          <h1 class="mb-2 text-center text-lg font-bold text-[#213555]">{{ ucfirst($ticket->type) }}</h1>
          <p class="mb-4 text-center text-sm text-[#213555]/70">Sua senha foi gerada com sucesso!</p>

        </div>
    </div>


    <script>
    async function printTicket(id) {
    try {
        const res = await fetch(`/ticket/${id}/print`);
        const data = await res.json();
        if (data.success) {
        alert('Ticket impresso com sucesso no servidor!');
        } else {
        alert('Erro na impressão: ' + data.message);
        }
    } catch (error) {
        alert('Erro na requisição: ' + error.message);
    }
    }

    setTimeout(() => {
        window.location.href = "{{ route('ticket.take') }}";
    }, 5000);
    </script>
</body>
</html>
