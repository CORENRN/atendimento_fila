@extends('layouts.app')

@section('content')
<div class="flex h-screen w-full">
    

    <div class="bg-[#141e22] w-full h-full flex flex-col items-center justify-center">

            <div class="p-10 rounded text-center">
                <div class="flex justify-center items-center flex-col gap-3 mb-3">
                    <h1 class="text-5xl font-semibold text-[#eceef0]">Olá! Seja Bem-Vindo(a)!</h1>
                    <div class="h-2 w-5/6 bg-[#56cbec] rounded-full"></div>
                </div>
                <p class="text-lg text-[#eceef0]">Por favor, selecione uma das opções abaixo:</p>
            </div>

            <div class=" space-y-4 duration-300 m-1 p-1 mt-0 w-[70%] rounded">
                @if(auth()->user() && auth()->user()->categoria === 'user')
                <div class="bg-[#202e36] hover:p-2 duration-300 rounded">
                    <a href="{{ route('queue', 'triagem') }}" class="flex items-center gap-2  duration-300 bg-[#1a262d] transition text-white px-6 py-7 rounded hover:text-[#eef4ff]"><img src="{{ asset('images/triagemico.png') }}" alt="atendente" class="w-14">Triagem</a>
                </div>
                <div class="bg-[#202e36] hover:p-2 duration-300 rounded">
                    <a href="{{ route('queue', 'atendimento') }}" class="flex items-center gap-2 bg-[#1a262d] hover:text-[#eef4ff] transition duration-300 text-white px-6 py-7 rounded"><img src="{{ asset('images/atendenteico.png') }}" alt="atendente" class="w-14">Fila de Atendimento</a>
                </div>
                @endif
                @if(auth()->user() && auth()->user()->categoria === 'superAdmin' || auth()->user()->categoria === 'admin')
                    <div class="bg-[#202e36] hover:p-2 duration-300 rounded">
                        <a href="{{ route('queue', 'triagem') }}" class="flex items-center gap-2 bg-[#1a262d] hover:text-[#eef4ff] transition duration-300 text-white px-6 py-7 rounded"><img src="{{ asset('images/triagemico.png') }}" alt="atendente" class="w-14">Triagem</a>
                    </div>

                    <div class="bg-[#202e36] hover:p-2 duration-300 rounded">
                        <a href="{{ route('queue', 'atendimento') }}" class="flex items-center gap-2 bg-[#1a262d] hover:text-[#eef4ff] transition duration-300 text-white px-6 py-7 rounded"><img src="{{ asset('images/atendenteico.png') }}" alt="atendente" class="w-14">Fila de Atendimento</a>
                    </div>

                    <div class="bg-[#202e36] hover:p-2 duration-300 rounded">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 bg-[#1a262d] hover:text-[#eef4ff] transition duration-300 text-white px-6 py-7 rounded"><img src="{{ asset('images/graficoico.png') }}" alt="atendente" class="w-14">Dashboard</a>
                    </div>

                    <div class="bg-[#202e36] hover:p-2 duration-300 rounded">
                        <a href="{{ route('adminPanel') }}" class="flex items-center gap-2 bg-[#1a262d] hover:text-[#eef4ff] transition duration-300 text-white px-6 py-7 rounded"><img src="{{ asset('images/painel.png') }}" alt="atendente" class="w-14">Painel do Administrador</a>
                    </div>
                @endif
            </div>
    </div>
</div>



@endsection

