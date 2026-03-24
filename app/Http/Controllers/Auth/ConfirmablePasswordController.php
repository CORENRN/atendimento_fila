<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
/* -------------------------------------------------------------------------- */
/*                            Confirmação de senha                            */
/* -------------------------------------------------------------------------- */
class ConfirmablePasswordController extends Controller
{
    /**
     * Exibir a tela de confirmação de senha.
     */
    public function show(): View
    {
        return view('auth.confirm-password');
    }

    /**
     * Processar a confirmação de senha recebida.
     */
    public function store(Request $request): RedirectResponse
    {
        if (! Auth::guard('web')->validate([
            'email' => $request->user()->email,
            'password' => $request->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
