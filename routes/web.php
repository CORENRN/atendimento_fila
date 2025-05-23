<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;


Route::get('/', [FrontendController::class, 'home'])->name('home');

Route::prefix('queue')->group(function () {
    Route::get('{stage}', [FrontendController::class, 'queue'])->name('queue');
    Route::post('{stage}/call', [FrontendController::class, 'callNext'])->name('queue.call');
    Route::post('{id}/finish', [FrontendController::class, 'finish'])->name('queue.finish');
    Route::post('{id}/cancel', [FrontendController::class, 'cancel'])->name('queue.cancel');
    Route::post('{id}/advance', [FrontendController::class, 'advance'])->name('queue.advance');
});

// Retirada de senha
Route::get('/ticket/take', function () {
    return view('ticket_take');
})->name('ticket.take');

Route::post('/ticket/take', [FrontendController::class, 'takeTicket'])->name('ticket.take.post');
Route::get('/ticket/{id}', [FrontendController::class, 'showTicket'])->name('ticket.show');


Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');











//KEYCLOAK

// Route::middleware('auth.keycloak')->group(function () {
//     Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
// });

Route::get('/login', function () {
    $query = http_build_query([
        'client_id' => env('KEYCLOAK_CLIENT_ID'),
        'redirect_uri' => env('KEYCLOAK_REDIRECT_URI'),
        'response_type' => 'code',
        'scope' => 'openid profile email',
    ]);

    return redirect(env('KEYCLOAK_BASE_URL') . "/realms/" . env('KEYCLOAK_REALM') . "/protocol/openid-connect/auth?" . $query);
})->name('login');

Route::get('/login/callback', function (Request $request) {
    $code = $request->get('code');

    if (!$code) {
        return redirect('/login')->with('error', 'Código de autorização não encontrado');
    }

    // Trocar o código pelo token
    $response = Http::asForm()->post(env('KEYCLOAK_BASE_URL') . "/realms/" . env('KEYCLOAK_REALM') . "/protocol/openid-connect/token", [
        'grant_type' => 'authorization_code',
        'client_id' => env('KEYCLOAK_CLIENT_ID'),
        'client_secret' => env('KEYCLOAK_CLIENT_SECRET'),
        'code' => $code,
        'redirect_uri' => env('KEYCLOAK_REDIRECT_URI'),
    ]);

    if (!$response->successful()) {
        return redirect('/login')->with('error', 'Falha ao obter token do Keycloak');
    }

    $tokens = $response->json();

    // Salvar token na sessão (ou usar outro método)
    session([
        'keycloak_access_token' => $tokens['access_token'],
        'keycloak_refresh_token' => $tokens['refresh_token'],
    ]);

    // Opcional: buscar informações do usuário com o access_token
    $userResponse = Http::withToken($tokens['access_token'])
        ->get(env('KEYCLOAK_BASE_URL') . "/realms/" . env('KEYCLOAK_REALM') . "/protocol/openid-connect/userinfo");

    if ($userResponse->successful()) {
        session(['keycloak_user' => $userResponse->json()]);
    }

    return redirect('/dashboard');
})->name('login.callback');