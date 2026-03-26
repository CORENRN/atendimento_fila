@extends('layouts.app')

@section('content')
<div class="flex min-h-screen w-full">
    <div class="bg-[#141e22] w-full min-h-screen flex flex-col items-center overflow-y-auto py-10">

        <div class="p-10 rounded text-center">
            <div class="flex justify-center items-center flex-col gap-3 mb-3">
                <h1 class="text-5xl font-semibold text-[#eceef0]">Olá! Seja Bem-Vindo(a)!</h1>
                <div class="h-2 w-5/6 bg-[#56cbec] rounded-full"></div>
            </div>
            <p class="text-lg text-[#eceef0]">Por favor, selecione uma das opções abaixo:</p>
        </div>

        <div class="space-y-4 m-1 p-1 mt-0 w-[70%] rounded">

            @if(auth()->user() && auth()->user()->categoria === 'user')
                <a href="{{ route('queue', 'triagem') }}"
                   class="flex items-center gap-2 bg-[#1a262d] text-white px-6 py-7 rounded transition duration-300 hover:bg-[#22313a] hover:text-[#eef4ff]">
                    <img src="{{ asset('images/triagemico.png') }}" alt="atendente" class="w-14">
                    Triagem
                </a>

                <a href="{{ route('guiche.select.view') }}"
                   class="flex items-center gap-2 bg-[#1a262d] text-white px-6 py-7 rounded transition duration-300 hover:bg-[#22313a] hover:text-[#eef4ff]">
                    <img src="{{ asset('images/atendenteico.png') }}" alt="atendente" class="w-14">
                    Atendimento
                </a>

                <a href="{{ route('queue', 'carteira') }}"
                   class="flex items-center gap-2 bg-[#1a262d] text-white px-6 py-7 rounded transition duration-300 hover:bg-[#22313a] hover:text-[#eef4ff]">
                    <img src="{{ asset('images/cartao.png') }}" alt="atendente" class="w-14">
                    Carteira
                </a>
            @endif

            @if(auth()->user() && (auth()->user()->categoria === 'superAdmin' || auth()->user()->categoria === 'supervisor'))
                <a href="{{ route('queue', 'triagem') }}"
                   class="flex items-center gap-2 bg-[#1a262d] text-white px-6 py-7 rounded transition duration-300 hover:bg-[#22313a] hover:text-[#eef4ff]">
                    <img src="{{ asset('images/triagemico.png') }}" alt="atendente" class="w-14">
                    Triagem
                </a>

                <a href="{{ route('queue', 'atendimento') }}"
                   class="flex items-center gap-2 bg-[#1a262d] text-white px-6 py-7 rounded transition duration-300 hover:bg-[#22313a] hover:text-[#eef4ff]">
                    <img src="{{ asset('images/atendenteico.png') }}" alt="atendente" class="w-14">
                    Atendimento
                </a>

                <a href="{{ route('queue', 'carteira') }}"
                   class="flex items-center gap-2 bg-[#1a262d] text-white px-6 py-7 rounded transition duration-300 hover:bg-[#22313a] hover:text-[#eef4ff]">
                    <img src="{{ asset('images/cartao.png') }}" alt="atendente" class="w-14">
                    Carteira
                </a>

                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-2 bg-[#1a262d] text-white px-6 py-7 rounded transition duration-300 hover:bg-[#22313a] hover:text-[#eef4ff]">
                    <img src="{{ asset('images/graficoico.png') }}" alt="atendente" class="w-14">
                    Dashboard
                </a>

                <a href="{{ route('adminPanel') }}"
                   class="flex items-center gap-2 bg-[#1a262d] text-white px-6 py-7 rounded transition duration-300 hover:bg-[#22313a] hover:text-[#eef4ff]">
                    <img src="{{ asset('images/painel.png') }}" alt="atendente" class="w-14">
                    Painel de Gestão
                </a>

                <a href="{{ route('panel.index') }}"
                   class="flex items-center gap-2 bg-[#1a262d] text-white px-6 py-7 rounded transition duration-300 hover:bg-[#22313a] hover:text-[#eef4ff]">
                    <img src="{{ asset('images/painel_tickets.png') }}" alt="atendente" class="w-14">
                    Controle do Visor
                </a>
            @endif

        </div>
    </div>
</div>
@endsection