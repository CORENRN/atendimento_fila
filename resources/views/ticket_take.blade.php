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
            <label class="block mb-2">Selecione o tipo de senha:</label>
            <div class="flex gap-4">
                <input 
                    type="submit" 
                    name="type" 
                    value="regular" 
                    class="flex-1 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 cursor-pointer" 
                />
                <input 
                    type="submit" 
                    name="type" 
                    value="preferencial" 
                    class="flex-1 bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 cursor-pointer" 
                />
            </div>
        </div>
    </form>
    </div>

</body>
</html>
