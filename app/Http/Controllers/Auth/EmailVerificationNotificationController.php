<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
/* -------------------------------------------------------------------------- */
/*                Enviar notificação para verificação de e-mail               */
/* -------------------------------------------------------------------------- */
class EmailVerificationNotificationController extends Controller
{
    /**
     * Enviar uma nova notificação de verificação de e-mail.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
