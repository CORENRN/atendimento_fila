@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-semibold mb-4">Escolha seu Guichê</h2>

    @if(session('error'))
        <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('guiche.select') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label for="guiche_id" class="block text-sm font-medium text-gray-700">Guichê</label>
            <select name="guiche_id" id="guiche_id" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                <option value="">Selecione um guichê</option>
                @foreach($guiches as $guiche)
                    <option value="{{ $guiche->id }}">{{ $guiche->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="w-full bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700">
            Confirmar
        </button>
    </form>
</div>
@endsection
