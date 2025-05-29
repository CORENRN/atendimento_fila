@extends('layouts.app')

@section('content')


    <div class="relative flex flex-col text-6xl font-black w-[50%] h-full items-center justify-center">
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

            <div class="space-y-4 mt-4 w-[70%]">
                <a href="{{ route('queue', 'triagem') }}" class="block bg-blue-500 transition duration-300 text-white px-6 py-10 rounded hover:bg-blue-600">Fila de Triagem</a>
                <a href="{{ route('queue', 'atendimento') }}" class="block bg-green-500 transition duration-300 text-white px-6 py-10 rounded hover:bg-green-600">Fila de Atendimento</a>
                <a href="{{ route('ticket.take') }}" class="block bg-yellow-500 transition duration-300 text-white px-6 py-10 rounded hover:bg-yellow-600">Retirar Senha</a>
                <a href="{{ route('dashboard') }}" class="block bg-purple-500 transition duration-300 text-white px-6 py-10 rounded hover:bg-purple-600">Dashboard</a>
            </div>
    </div>
@endsection

