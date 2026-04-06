<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Fila de Atendimento') }}</title>
        
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ time() }}">

        <style>
            h2, h1 { font-family: "Lora", serif; }
            p { font-family: "Roboto Slab", serif; }
        </style>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Lora:ital,wght@0,400..700;1,400..700&family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased overflow-hidden bg-gray-100 dark:bg-gray-900">
        <div class="min-h-screen">
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow-lg relative z-10">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            @if (session('success'))
                {{-- 
                    1. pointer-events-none: Faz o container ser "invisível" para o mouse.
                    2. z-[100]: Mantém na frente visualmente.
                    3. w-full flex justify-center: Mantém o alinhamento central.
                --}}
                <div class="w-full flex justify-center fixed z-[100] mt-[-65px] pointer-events-none">
                    {{-- 
                        1. pointer-events-auto: Faz com que APENAS a caixinha preta 
                        reaja ao mouse (caso você queira colocar um botão de fechar).
                    --}}
                    <div class="bg-blackPrimary border border-white/10 px-6 py-3 rounded shadow-md animate-notification flex items-center gap-2 pointer-events-auto">
                        <strong class="font-bold text-primary">Sucesso!</strong>
                        <span class="text-lightW">{{ session('success') }}</span>
                        {{-- Botão opcional para fechar manualmente --}}
                        <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white/50 hover:text-white">&times;</button>
                    </div>
                </div>
            @endif

            <main class="flex flex-col h-screen overflow-hidden">
                 @yield('content')
            </main>
        </div>
    </body>
</html>