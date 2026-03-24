<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
/* -------------------------------------------------------------------------- */
/*                    Exibe a tela de verificação de e-mail                   */
/* -------------------------------------------------------------------------- */
class EmailVerificationPromptController extends Controller
{
    /**
     * Exibir a tela de solicitação de verificação de e-mail.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('dashboard', absolute: false))
                    : view('auth.verify-email');
    }
}
