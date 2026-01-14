<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <style>
            h2, h1{
                font-family: "Lora", serif;
            }
            p{
               font-family: "Roboto Slab", serif;
            }
        </style>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Lora:ital,wght@0,400..700;1,400..700&family=Roboto+Slab:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">


        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans bg-blackPrimary text-gray-900 antialiased">
        <div class="min-h-screen flex items-center  sm:pt-0">
            <div class="w-[50%] bg- h-screen flex flex-col items-center justify-center">
                <img src="{{ asset('images/login.png') }}" alt="atendente" class="w-[70%] -mt-20">
                <h2 class="text-5xl font-black text-lightW text-center">Bem Vindo(a) de volta!</h2>
                <p class="text-lg text-center  mt-5 text-lightW/80 max-w-[50%]">O "Atendimento COREN" é um sistema que veio para facilitar os processos profissionais de atendimento!</p>
            </div>
            <div class="w-[50%] h-screen flex flex-col items-center justify-center">
                <h1 class="flex items-center text-4xl font-black text-primary">ATENDIMENT<div>
                <svg width="35px" class="mt-0" height="35px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <g>
                        <path fill="none" d="M0 0h24v24H0z"/>
                        <path fill="#56cbec" d="M21 8a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-1.062A8.001 8.001 0 0 1 12 23v-2a6 6 0 0 0 6-6V9A6 6 0 1 0 6 9v7H3a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h1.062a8.001 8.001 0 0 1 15.876 0H21zM7.76 15.785l1.06-1.696A5.972 5.972 0 0 0 12 15a5.972 5.972 0 0 0 3.18-.911l1.06 1.696A7.963 7.963 0 0 1 12 17a7.963 7.963 0 0 1-4.24-1.215z"/>
                    </g>
                </svg>
                </div><span class="text-lightW ml-3">COREN</span></h1>
                <div class="px-6 py-4 overflow-hidden sm:rounded-lg w-[60%]">
                    {{ $slot }}
                </div>
            </div>

        </div>
    </body>
</html>
