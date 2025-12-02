@extends('layouts.app')

@section('content')
<div class="w-full flex flex-col items-center justify-center bg-blackPrimary">
    <div class="w-full h-screen flex flex-col items-center justify-center">
        <h2 class="text-5xl font-black text-lightW mb-4 mt-36">Quase tudo pronto!</h2>
            <p class="text-lightW">Escolha em qual guichê você irá trabalhar hoje dentre as opções disponiveis abaixo!</p>

            @if(session('error'))
                <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

        <form action="{{ route('guiche.select') }}" method="POST" class=" w-[50%] mt-5">
        @csrf

            <div class="mb-4 relative">
                <select name="guiche_id" id="guiche_id" required class=" mt-1 block w-full border border-[#213555]/30 text-[#213555] py-4 pl-16  rounded-md shadow-sm p-2">
                    <option value="" class="">Selecione um guichê</option>
                    @foreach($guiches as $guiche)
                    <option value="{{ $guiche->id }}">{{ $guiche->name }}</option>
                    @endforeach
                </select>
                <div class="absolute top-2 left-4 transform ">
                    <img src="{{ asset('images/atendenteico.png') }}" class="w-10 h-10" alt="Icone">
                </div>
            </div>
            <div class="bg-[#202e36] hover:p-2  duration-300 rounded">
                <button type="submit" class="justify-center gap-2 w-full duration-300 bg-[#1a262d] transition text-white px-6 py-7 rounded hover:text-[#eef4ff]">
                    Escolher Guichê
                </button>
            </div> 
                <p class="text-md text-lightW text-center mt-5">guichê já escolhido? <a href="/home" class="text-lightW hover:text-primary transition duration-300">ir para "home"</a></p>
        </form>
    </div>
    
</div>

@endsection
