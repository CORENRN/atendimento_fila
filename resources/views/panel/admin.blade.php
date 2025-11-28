@extends('layouts.app')
@section('content')

<section class="h-screen w-full p-5">
    <h1 class="text-4xl text-[#142136] font-bold px-10 py-2">Painel do administrador</h1>
    <main class="flex w-full h-full px-5">
        <aside class=" top-[165px] w-64 h-full p-4 z-50">
            <nav class="flex flex-col h-fit p-5 rounded-md bg-white shadow-2xl gap-5">
                <h2 class="font-semibold text-lg tracking-widest text-gray-500/50">MENU</h2>

                @php
                    // Função para verificar se usuário é admin ou superAdmin
                    $isAdmin = auth()->check() && in_array(auth()->user()->categoria ?? '', ['admin', 'superAdmin']);

                    // Monta o array de rotas conforme permissão
                    $menuItems = [
                        ['home', 'Home'],
                        // Só adiciona dashboard se for admin ou superAdmin
                        ...($isAdmin ? [['dashboard', 'Dashboard']] : []),
                        ['ticket.take', 'Retirar Senha'],
                        ['queue', 'Fila de Triagem', 'triagem'],
                        ['queue', 'Fila de Atendimento', 'atendimento'],
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

                        $baseClasses = 'h-10 transition duration-300 text-black px-4 py-2 rounded';
                        $activeClasses = $isActive ? 'bg-black text-white' : 'bg-white hover:bg-gray-200';
                    @endphp

                    <a 
                        href="{{ isset($item[2]) ? route($item[0], $item[2]) : route($item[0]) }}" 
                        class="{{ $baseClasses }} {{ $activeClasses }}"
                    >
                        {{ $item[1] }}
                    </a>
                @endforeach
            </nav>
        </aside>
        <section class=" flex w-[80%] h-full gap-10">
            <div class="flex flex-col h-full w-[50%]">
                @if(auth()->user() && auth()->user()->categoria === 'superAdmin')
                    <div class="mt-4 bg-white p-6 rounded-lg shadow-lg w-[100%] h-fit">
                        <h2 class="text-2xl font-bold mb-4">Atualizar Vídeo do Painel</h2>
                        @if(session('success'))
                            <div class="bg-green-100 text-green-800 p-2 rounded mb-4">
                                {{ session('success') }}
                            </div>
                        @endif
                        <form action="{{ route('panel.updateVideo') }}" method="POST">
                            @csrf
                            <input type="url" name="video_url" placeholder="https://www.youtube.com/embed/..." class="w-full p-3 border border-gray-300 rounded mb-4" value="{{ $videoUrl }}" required>
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Atualizar Vídeo</button>
                        </form>
                    </div>
                @endif
                    <div class="mt-4 bg-white p-6 rounded-lg shadow-lg w-full h-[55%]">
                        <h2 class="text-2xl font-bold mb-4">Impressoras cadastradas:</h2>

                        @if($printers->isEmpty())
                            <p class="text-gray-500">Nenhuma impressora cadastrada.</p>
                        @else
                            <ul class="space-y-2">
                                @foreach($printers as $printer)
                                    <li class="border p-2 rounded">
                                        <strong>{{ $printer->name }}</strong> - {{ $printer->ip }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
            </div>

            <div class="flex flex-col w-[50%] h-full gap-4">
                <div class="mt-4 bg-white p-6 rounded-lg shadow-lg w-[100%] h-fit">
                    <h2 class="text-2xl font-bold mb-4">Adicionar impressora de rede:</h2>
                    <form method="POST" action="{{ route('printers.store') }}" class="space-y-4">
                        @csrf
                        <label>
                            Nome da Impressora:
                            <input type="text" name="name" placeholder="Digite o nome da impressora" class="border p-2 w-full" required>
                        </label>

                        <label>
                            IP da Impressora:
                            <input type="text" name="ip" placeholder="digite o ip da impressora Ex:192.168.0.1" class="border p-2 w-full" required>
                        </label>

                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Salvar Impressora</button>
                    </form>
                </div>

                <div class="w-[100%] h-[46%] bg-white shadow-lg rounded-md p-5">
                    <h2 class="text-2xl font-bold mb-4">Usuários cadastrados:</h2>
                    <div class="bg-white/80">

                    </div>
                </div>
            </div>
            
            
        </section>
    </main>
    
</section>

@endsection
