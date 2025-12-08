@extends('layouts.app')
@section('content')

<section class="h-screen bg-blackPrimary w-full p-5">
    <h1 class="text-4xl text-lightW font-bold px-10 py-2">Painel do administrador</h1>
    <main class="flex w-full h-full px-5">
        <aside class=" top-[165px] w-64 h-full p-4 z-50">
            <nav class="flex flex-col h-fit p-5 rounded-md bg-blackSecondary shadow-2xl gap-5">
                <h2 class="font-semibold text-lg tracking-widest text-lightW">MENU</h2>

                @php
                    // Função para verificar se usuário é admin ou superAdmin
                    $isAdmin = auth()->check() && in_array(auth()->user()->categoria ?? '', ['admin', 'superAdmin']);

                    // Monta o array de rotas conforme permissão
                    $menuItems = [
                        ['home', 'Home'],
                        // Só adiciona dashboard se for admin ou superAdmin
                        ...($isAdmin ? [['dashboard', 'Dashboard']] : []),
                        ['ticket.take', 'Retirar Senha'],
                        ['queue', 'Triagem', 'triagem'],
                        ['queue', 'Atendimento', 'atendimento'],
                    ];
                @endphp

                @foreach($menuItems as $item)
                    @php
                        $isActive = false;

                        if ($item[0] === 'queue') {
                            $isActive = Route::currentRouteName() === 'queue' && (request()->route('stage') === ($item[2] ?? ''));
                        } else {
                            $isActive = Route::currentRouteName() === $item[0];
                        }

                        $baseClasses = 'h-10 transition text-lightW bg-blackSecondary w-full px-4 py-2 rounded';
                        $divBaseClasses ='bg-blackThirdy flex items-center rounded justify-center duration-300';
                        $hoverClasses = !$isActive ? 'hover:p-[6px]' : '';
                        $activeClasses = $isActive ? 'border-[6px] p-0 border-blackThirdy ' : '';
                
                    @endphp
                    <div class="{{$divBaseClasses}} {{$hoverClasses}}">
                            <a 
                            href="{{ isset($item[2]) ? route($item[0], $item[2]) : route($item[0]) }}" 
                            class="trasition duration-300{{ $baseClasses }} {{ $activeClasses }}"
                        >
                            {{ $item[1] }}
                        </a>
                    </div>

                @endforeach
            </nav>
        </aside>
        <section class=" flex w-[80%] h-full gap-10">
            <div class="flex flex-col h-full w-[50%]">
                @if(auth()->user() && auth()->user()->categoria === 'superAdmin')
                    <div class="mt-4 bg-blackSecondary p-6 rounded-lg w-[100%] h-fit">
                        <h2 class="text-2xl text-lightW font-bold mb-4">Atualizar Vídeo do Painel</h2>
                        @if(session('success'))
                            <div class="bg-green-100 text-lightW p-2 rounded mb-4">
                                {{ session('success') }}
                            </div>
                        @endif
                        <form action="{{ route('panel.updateVideo') }}" method="POST">
                            @csrf
                            <input type="url" name="video_url" placeholder="https://www.youtube.com/embed/..." class="w-full p-3 border-4 text-lightW border-blackThirdy bg-blackSecondary rounded mb-4" value="{{ $videoUrl }}" required>
                            <div class="bg-blackThirdy flex flex-col rounded justify-center w-[25%] hover:p-1 duration-300 ">
                                <button type="submit" class="bg-blackSecondary border-blackThirdy shadow-sm text-lightW px-4 py-2 rounded">Atualizar Vídeo</button>
                            </div>
                            
                        </form>
                    </div>
                @endif
                    <div class="mt-4 bg-blackSecondary p-6 rounded-lg shadow-lg w-full h-[55%]">
                        <h2 class="text-2xl text-lightW font-bold mb-4">Impressoras cadastradas:</h2>

                        @if($printers->isEmpty())
                            <p class="text-lightW">Nenhuma impressora cadastrada.</p>
                        @else
                            <ul class="space-y-2">
                                @foreach($printers as $printer)
                                    <li class="bg-blackSecondary border-4 border-blackThirdy text-lightW p-2 rounded">
                                        <strong>{{ $printer->name }}</strong> - {{ $printer->ip }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
            </div>

            <div class="flex flex-col w-[50%] h-full gap-4">
                <div class="mt-4 bg-blackSecondary p-6 rounded-lg shadow-lg w-[100%] h-fit">
                    <h2 class="text-2xl text-lightW font-bold mb-4">Adicionar impressora de rede:</h2>
                    <form method="POST" action="{{ route('printers.store') }}" class="space-y-4">
                        @csrf
                        <label class="text-lightW">
                            Nome da Impressora:
                            <input type="text" name="name" placeholder="Digite o nome da impressora" class="border-blackThirdy border-4  p-2 w-full rounded bg-blackSecondary" required>
                        </label>

                        <label class="text-lightW">
                            IP da Impressora:
                            <input type="text" name="ip" placeholder="digite o ip da impressora Ex:192.168.0.1" class="border-blackThirdy border-4 p-2 w-full rounded bg-blackSecondary" required>
                        </label>
                        <div class="bg-blackThirdy hover:p-1 rounded duration-300 me-[500px] ">
                            <button type="submit" class="bg-blackSecondary w-full text-white px-4 py-2 rounded">Salvar Impressora</button>
                        </div>
                        
                    </form>
                </div>

                <div class="w-[100%] h-[46%] bg-blackSecondary shadow-lg rounded-md p-5">
                        <h2 class="text-2xl text-lightW font-bold mb-4">Usuários cadastrados:</h2>
                        
                        @if(session('success'))
                            <div class="bg-green-700 text-white p-2 rounded mb-3 text-sm">
                                {{ session('success') }}
                            </div>
                        @endif
                        
                        <div class="overflow-y-auto h-[calc(100%-60px)] space-y-3">
                            @if(isset($users) && $users->count())
                                @foreach($users as $user)
                                    <div class="p-3 rounded-md flex justify-between items-center border-4 border-blackThirdy">
                                        <div class="flex flex-col">
                                            <p class="text-lightW font-semibold">{{ $user->name }}</p>
                                            <span class="text-sm text-gray-400">{{ $user->email }}</span>
                                        </div>
                                        
                                        <form action="{{ route('users.updateCategory', $user->id) }}" method="POST" class="flex items-center gap-2">
                                            @csrf
                                            @method('PUT') 
                                            
                                            <div class="hover:p-[3px] duration-300 bg-blackThirdy rounded">
                                                <select 
                                                name="categoria" 
                                                class="p-1 border-blackSecondary border-transparent rounded bg-blackSecondary text-lightW text-sm"
                                                required
                                                >
                                                <option value="user" {{ ($user->categoria ?? 'user') === 'user' ? 'selected' : '' }}>Padrão</option>
                                                <option value="admin" {{ ($user->categoria ?? '') === 'admin' ? 'selected' : '' }}>Admin</option>
                                                <option value="superAdmin" {{ ($user->categoria ?? '') === 'superAdmin' ? 'selected' : '' }}>Super Admin</option>
                                                </select>
                                            </div>
                                            
                                            
                                            <div class="hover:p-[3px] duration-300 bg-blackThirdy rounded">
                                                <button type="submit" class="bg-green-700 hover:bg-green-600 bg-blackSecondary text-white text-sm px-3 py-1 rounded transition duration-200">
                                                    Salvar
                                                </button>
                                            </div>
                                            
                                        </form>

                                        @error('categoria')
                                            <div class="text-red-400 text-xs mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endforeach
                            @else
                                <p class="text-lightW">Nenhum usuário encontrado para listagem.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            

        </section>
    </main>
    
</section>

@endsection
