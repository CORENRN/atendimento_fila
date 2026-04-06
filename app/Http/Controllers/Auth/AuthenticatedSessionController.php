<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create() { return view('auth.login'); }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->ensureIsNotRateLimited();

        if ($this->authenticateLdap($request->username, $request->password)) {
            RateLimiter::clear($request->throttleKey());

            // 1. Tenta buscar o usuário pelo username primeiro
            $user = User::where('username', $request->username)->first();

            if (!$user) {
                // 2. Se NÃO existe, CRIA um novo com a categoria padrão
                $user = User::create([
                    'username'          => $request->username,
                    'name'              => $request->username,
                    'email'             => $request->username . '@corenrn.gov.br',
                    'password'          => bcrypt(Str::random(16)),
                    'categoria'         => User::CATEGORIA_USER, // Valor padrão inicial
                    'email_verified_at' => now(),
                ]);
            } else {
                // 3. Se JÁ existe, APENAS ATUALIZA o que for necessário
                // Note que NÃO incluímos 'categoria' aqui, assim o valor do banco é preservado
                $user->update([
                    'name'              => $request->username,
                    'email'             => $request->username . '@corenrn.gov.br',
                    'email_verified_at' => now(),
                ]);
            }

            Auth::login($user, $request->boolean('remember'));
            
            $request->session()->regenerate();
            $request->session()->save(); 

            return redirect()->intended('/'); 
        }

        RateLimiter::hit($request->throttleKey());

        throw ValidationException::withMessages([
            'username' => 'Acesso negado. Verifique suas credenciais de rede.',
        ]);
    }

    private function authenticateLdap($username, $password)
    {
        // Usa config() com fallback para o .env, garantindo que funcione em produção
        $host = config('services.ldap.host', '192.168.10.200');
        $port = config('services.ldap.port', 389);
        
        $conn = @ldap_connect($host, $port);
        if (!$conn) return false;

        ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);

        $formats = [$username . "@corenrn.gov.br", $username . "@corenrn.local", "CORENRN\\" . $username];

        foreach ($formats as $ldapUser) {
            if (@ldap_bind($conn, $ldapUser, $password)) {
                return true;
            }
        }
        return false;
    }

    public function destroy(Request $request)
    {
      
        $user = Auth::user();

        if ($user) {
           $user->guiches()->detach();
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Logged out']);
        }

        return redirect('/login');
    }
}