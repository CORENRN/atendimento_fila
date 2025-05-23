<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Retirar Senha</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="bg-white p-8 rounded shadow-lg">
        <h1 class="text-2xl font-bold mb-6 text-center">Retirar Senha</h1>

        <form action="{{ route('ticket.take.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block mb-1">Nome:</label>
                <input type="text" name="name" class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block mb-1">Documento (opcional):</label>
                <input type="text" name="document" class="w-full border rounded px-3 py-2">
            </div>

            <button class="w-full bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                Gerar Senha
            </button>
        </form>
    </div>

</body>
</html>
