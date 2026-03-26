@extends('layouts.app')
@section('content')

<section class="h-screen overflow-hidden bg-blackPrimary w-full relative">
    <aside class="fixed top-[165px] left-0 w-64 h-full p-4 z-50">
        <nav class="flex flex-col h-fit p-5 rounded-md bg-blackSecondary shadow-2xl gap-5">
            <h2 class="font-semibold text-lg tracking-widest text-[#eceef0]">MENU</h2>
            @php
                // Esta variável guarda APENAS o usuário logado para controle de menu
                $user = auth()->user();
                $hasAdminAccess = $user && $user->hasAdminAccess();
                $menuItems = [['home', 'Home']];
                if ($hasAdminAccess) {
                    $menuItems[] = ['dashboard', 'Dashboard'];
                    $menuItems[] = ['adminPanel', 'Gestão'];
                    $menuItems[] = ['panel.index', 'Visor'];
                    $menuItems[] = ['ticket.take', 'Retirar Senha'];
                }

                $menuItems[] = ['queue', 'Triagem', 'triagem'];
                $menuItems[] = ['queue', 'Atendimento', 'atendimento'];
                $menuItems[] = ['queue', 'Carteira', 'carteira']
            @endphp

            @foreach($menuItems as $item)
            @php
                $isActive = false;

                if ($item[0] === 'queue') {
                    $isActive = Route::currentRouteName() === 'queue' && (request()->route('stage') === ($item[2] ?? ''));
                } else {
                    $isActive = Route::currentRouteName() === $item[0];
                }

                $baseClasses = 'h-10 transition text-lightW bg-blackSecondary w-full px-4 py-2 rounded flex items-center';
                $divBaseClasses ='bg-blackThirdy flex items-center rounded justify-center duration-300 w-full';
                $hoverClasses = !$isActive ? 'hover:p-[4px]' : 'p-[4px]'; 
                $activeClasses = $isActive ? 'border-1 border-blackThirdy' : ''; 
            @endphp
            <div class="{{$divBaseClasses}} {{$hoverClasses}}">
                <a 
                    href="{{ isset($item[2]) ? route($item[0], $item[2]) : route($item[0]) }}" 
                    class="{{ $baseClasses }} {{ $activeClasses }} transition-all duration-300"
                >
                    {{ $item[1] }}
                </a>
            </div>
            @endforeach
        </nav>
    </aside>

    <div class="flex items-center mb-4 z-10 bg-blackPrimary justify-center h-48 w-full">
        <h1 class="text-4xl text-lightW font-bold px-4 py-2 -mt-24">Painel do Administrador</h1>
    </div>

    <section class="flex ml-64 h-full z-10 -mt-28">
        
        <div class="min-w-[50%] h-[75vh] bg-blackSecondary p-8 rounded shadow-xl flex flex-col">
            @if(auth()->user()->hasAdminAccess())
                <div class="shrink-0">
                    <h2 class="text-2xl text-lightW font-bold mb-4">Atualizar Vídeo do Painel</h2>
                    <form class="" action="{{ route('panel.updateVideo') }}" method="POST">
                        @csrf
                        <input type="url" name="video_url" class="w-full p-3 text-lightW border border-white/10 bg-blackPrimary mb-2 rounded" value="{{ $videoUrl }}" required>
                        <div class="w-fit hover:p-1 duration-300 bg-blackThirdy border border-blackThirdy mb-2 rounded">
                            <button type="submit" class="bg-[#1a262d] text-white px-3 py-2 rounded">Atualizar Vídeo</button>
                        </div>
                    </form>
                </div>

                <div class="flex-1 bg-blackPrimary border border-white/10 p-4 rounded shadow-xl">
                    <iframe class="w-full h-full rounded-lg" 
                            src="{{ $videoUrl }}" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                    </iframe>
                </div>
            @endif
        </div>

        <div class="flex flex-col w-full px-8 gap-8">
         
            <style>
                .custom-scrollbar::-webkit-scrollbar { width: 6px; }
                .custom-scrollbar::-webkit-scrollbar-track { background: #141e22; }
                .custom-scrollbar::-webkit-scrollbar-thumb { background: #56cbec; border-radius: 10px; }
            </style>
            @if(auth()->user()->isSuperAdmin())
                <div class="min-w-[50%] h-[75vh] custom-scrollbar bg-blackSecondary p-8 rounded shadow-xl gap-2 flex flex-col">
                    <h2 class="text-2xl text-lightW font-bold mb-6">Usuários cadastrados</h2>
                        @foreach($allUsers as $u)
                            <div class="flex justify-between items-center pb-4 border-b border-white/5">
                                <div>
                                    <p class="text-lightW font-semibold">{{ $u->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $u->email }}</p>
                                </div>
                                <form action="{{ route('users.updateCategory', $u->id) }}" method="POST" class="flex items-center gap-4">
                                    @csrf 
                                    @method('PUT')
                                    <select name="categoria" class="bg-transparent text-gray-400 text-xs border-none focus:ring-0 cursor-pointer">
                                        <option value="user" class="bg-blackSecondary" {{ $u->categoria === 'user' ? 'selected' : '' }}>Atendente</option>
                                        <option value="supervisor" class="bg-blackSecondary" {{ $u->categoria === 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                        <option value="superAdmin" class="bg-blackSecondary" {{ $u->categoria === 'superAdmin' ? 'selected' : '' }}>Super Admin</option>
                                    </select>
                                    <button type="submit" class="text-[#56cbec] text-xs font-bold hover:brightness-125">Salvar</button>
                                </form>
                            </div>
                        @endforeach
                </div>
            @endif

            
            
            @if(auth()->user()->isSupervisor())
                <div class="flex align-center justify-center bg-blackSecondary p-[138px] rounded shadow-xl">
                    <h2 class="text-lightW">Acesso restrito apenas a usuários do Departamento de Tecnologia da Informação e Comunicação!🔐</h2>
                </div>
            @endif
        </div>
    </section>
</section>
@endsection