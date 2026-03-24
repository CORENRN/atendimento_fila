<x-guest-layout>
    {{-- Alerta de erro geral (Lógica do LDAP com estilo Azul Claro) --}}
    @if ($errors->any())
        {{-- Alterado: bg-sky-50 (fundo azul bem claro), border-sky-400 (borda azul claro) --}}
        <div class="mb-4 p-4 rounded-lg bg-sky-50 border-l-4 border-sky-400 shadow-sm animate-pulse">
            <div class="flex">
                <div class="flex-shrink-0">
                    {{-- Alterado: text-sky-500 (Ícone X em azul) --}}
                    <svg class="h-5 w-5 text-sky-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    {{-- Alterado: text-sky-900 (Título) e text-sky-800 (Mensagem) --}}
                    <p class="text-sm font-bold text-sky-900">Erro de Autenticação</p>
                    <p class="text-xs text-sky-800">{{ $errors->first('username') }}</p>
                </div>
            </div>
        </div>
    @endif

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Nome de Usuário --}}
        <div>
            <x-input-label for="username" :value="__('Usuário da Rede')" class="text-white" />
            <x-text-input id="username" class="block mt-1 w-full" 
                            type="text" 
                            name="username" 
                            :value="old('username')" 
                            required autofocus />
            {{-- Texto de erro do campo em branco para contraste --}}
            <x-input-error :messages="$errors->get('username')" class="mt-2 text-white" />
        </div>

        {{-- Senha --}}
        <div class="mt-4">
            <x-input-label for="password" :value="__('Senha')" class="text-white" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-white" />
        </div>

        {{-- Lembrar Acesso --}}
        <div class="flex justify-between mt-4 items-center">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-[#3368d1] text-[#3368d1] shadow-sm" name="remember">
                <span class="ms-2 text-md text-white">{{ __('Lembrar acesso') }}</span>
            </label>
        </div>

        {{-- Botão Entrar --}}
        <div class="flex items-center justify-center mt-10 w-full">
            <x-primary-button class="w-full justify-center py-3">
                {{ __('Entrar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>