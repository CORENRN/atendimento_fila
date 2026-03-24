<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atribuir Categoria de Usuário</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">

    <div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow-md">
        <h1 class="text-2xl font-bold mb-6">Atribuir Categoria de Usuário</h1>

        <table class="w-full table-auto border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border px-4 py-2">ID</th>
                    <th class="border px-4 py-2">Nome</th>
                    <th class="border px-4 py-2">Email</th>
                    <th class="border px-4 py-2">Categoria Atual</th>
                    <th class="border px-4 py-2">Atribuir Categoria</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($allUsers as $user)
                    <tr>
                        <td class="border px-4 py-2">{{ $user->id }}</td>
                        <td class="border px-4 py-2">{{ $user->name }}</td>
                        <td class="border px-4 py-2">{{ $user->email }}</td>
                        <td class="border px-4 py-2">{{ $user->categoria ?? 'Nenhuma' }}</td>
                        <td class="border px-4 py-2">
                            <div class="flex items-center gap-2">
                                <form action="{{ route('users.updateCategory', $user->id) }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="categoria" class="border rounded px-2 py-1">
                                        <option value="">Selecione</option>
                                        <option value="user" @if($user->categoria === 'user') selected @endif>Atendente</option>
                                        <option value="supervisor" @if($user->categoria === 'supervisor') selected @endif>Supervisor</option>
                                        <option value="superAdmin" @if($user->categoria === 'superAdmin') selected @endif>Super Admin</option>
                                    </select>
                                    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                        Salvar
                                    </button>
                                </form>

                                @if(auth()->user()->isSuperAdmin())
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="if({{ $user->id }} === {{ auth()->id() }}){ 
                                        alert('Você não pode excluir sua própria conta!'); 
                                        return false; 
                                    } 
                                    return confirm('Tem certeza que deseja excluir este usuário?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
                                            Excluir
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</body>
</html>
