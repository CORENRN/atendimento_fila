@extends('layouts.app')

@section('content')
<div class="flex h-screen">
    <div class="relative flex flex-col text-6xl font-black w-[50%] h-full bg-white items-center justify-center">
        <img src="{{ asset('images/atendimento.jpg') }}" 
            alt="tickets"
            class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-blue-800 opacity-50"></div>
    </div>

    <div class="bg-white w-[50%] h-full flex flex-col items-center justify-center">

            <div class="p-10 rounded text-center">
                <div class="flex flex-col gap-3 mb-3">
                    <h1 class="text-5xl font-semibold">Ola! Seja Bem-Vindo(a)!</h1>
                    <div class="h-2 w-44 bg-blue-400 rounded-full"></div>
                </div>
                <p class="text-lg">Por favor, selecione uma das opções abaixo:</p>
            </div>

            <div class="space-y-4 mt-0 w-[70%]">
                @if(auth()->user() && auth()->user()->categoria === 'user')
                    <a href="{{ route('queue', 'triagem') }}" class="flex items-center gap-2 bg-[#527cd1] transition duration-300 text-white px-6 py-7 rounded hover:bg-[#8aabec] hover:text-[#eef4ff]"><img src="{{ asset('images/triagemico.png') }}" alt="atendente" class="w-14">Fila de Triagem</a>
                    <a href="{{ route('queue', 'atendimento') }}" class="flex items-center gap-2 bg-[#527cd1] hover:bg-[#8aabec] hover:text-[#eef4ff] transition duration-300 text-white px-6 py-7 rounded"><img src="{{ asset('images/atendenteico.png') }}" alt="atendente" class="w-14">Fila de Atendimento</a>
                    
                @endif
                @if(auth()->user() && auth()->user()->categoria === 'superAdmin' || auth()->user()->categoria === 'admin')
                    <a href="{{ route('queue', 'triagem') }}" class="flex items-center gap-2 bg-[#527cd1] transition duration-300 text-white px-6 py-7 rounded hover:bg-[#8aabec] hover:text-[#eef4ff]"><img src="{{ asset('images/triagemico.png') }}" alt="atendente" class="w-14">Fila de Triagem</a>
                    <a href="{{ route('queue', 'atendimento') }}" class="flex items-center gap-2 bg-[#527cd1] hover:bg-[#8aabec] hover:text-[#eef4ff] transition duration-300 text-white px-6 py-7 rounded"><img src="{{ asset('images/atendenteico.png') }}" alt="atendente" class="w-14">Fila de Atendimento</a>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 transition duration-300 bg-[#527cd1] hover:bg-[#8aabec] hover:text-[#eef4ff] text-white px-6 py-7 rounded"><img src="{{ asset('images/graficoico.png') }}" alt="atendente" class="w-14">Dashboard</a>
                    <a href="{{ route('adminPanel') }}" class="flex items-center gap-2 transition duration-300 bg-[#527cd1] hover:bg-[#8aabec] hover:text-[#eef4ff] text-white px-6 py-7 rounded"><img src="{{ asset('images/painel.png') }}" alt="atendente" class="w-14">Painel do Administrador</a>
                @endif
            </div>
    </div>
</div>



@endsection

