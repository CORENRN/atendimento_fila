@extends('layouts.app')

@section('content')
<div class="w-[60%] bg-white p-14 rounded">
    <h2 class="text-5xl font-black text-[#213555] mb-4 mt-36">Quase tudo pronto!</h2>
    <p class="text-[#213555]/80 text-lg w-[40%]">Escolha em qual guichê você irá trabalhar hoje dentre as opções disponiveis abaixo e vamos ao trabalho!</p>

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

        <button type="submit" class="w-full bg-[#527cd1] transition uppercase tracking-wide duration-300 mt-5 text-white text-sm font-black px-4 py-5 rounded hover:bg-[#8aabec] hover:text-[#213555]/80">
            Escolher Guichê
        </button>
        <p class="text-md text-[#213555]/80 text-center mt-5">guichê já escolhido? <a href="/home" class="text-[#527cd1] hover:text-[#22468f] transition duration-300">ir para "home"</a></p>
    </form>

    
</div>
<div class="w-[40%] h-screen relative overflow-hidden">
    <video 
        src="{{ asset('videos/video.mp4') }}" 
        autoplay 
        muted 
        loop 
        playsinline 
        class="absolute w-full h-full object-cover object-[80%_center]">
    </video>
    
    <!-- Camada azul por cima do vídeo -->
    <div class="absolute w-full h-full bg-blue-900/50 top-0 left-0"></div>
</div>
@endsection
