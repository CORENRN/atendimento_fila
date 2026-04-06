<?php

use App\Http\Controllers\AllowNotification;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\GuicheController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\UserController;

// --- ROTAS PÚBLICAS (Sem login) ---
Route::get('/ticket/take', [TicketController::class, 'takeTicketView'])->name('ticket.take');
Route::post('/ticket/take', [TicketController::class, 'takeTicket'])->name('ticket.take.post');
Route::get('/ticket/{id}', [TicketController::class, 'showTicket'])->name('ticket.show');
Route::get('/painel', [PanelController::class, 'index'])->name('panel.index');
Route::get('/painel/data', [PanelController::class, 'data'])->name('panel.data');
Route::get('/ticket/{id}/print', [PrintController::class, 'printTicket'])->name('ticket.print');
Route::get('/allow', [AllowNotification::class, 'index']);
// --- ROTAS PROTEGIDAS (Precisa de Login LDAP) ---
Route::middleware('auth')->group(function () {
    
    // Rota inicial após login (Home)
    Route::get('/', [TicketController::class, 'home'])->name('home');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/users/{id}', [ProfileController::class, 'destroy'])->name('users.destroy');

    // Guichê
    Route::get('/selecionar-guiche', [GuicheController::class, 'showSelectGuiche'])->name('guiche.select.view');
    Route::post('/selecionar-guiche', [GuicheController::class, 'selectGuiche'])->name('guiche.select');

    // Fila (Atendimento)
    Route::prefix('queue')->group(function () {
       
        Route::get('tickets/{stage}', [TicketController::class, 'getTicketsJson'])
            ->name('queue.tickets.json');
        Route::post('recall/{id}', [TicketController::class, 'recall'])
            ->name('queue.recall');

        Route::post('{stage}/call-multiple', [TicketController::class, 'callMultiple'])
                ->name('queue.callMultiple');
                
        Route::get('{stage}', [TicketController::class, 'queue'])
            ->name('queue');
        Route::post('{stage}/call', [TicketController::class, 'callNext'])
            ->name('queue.call');
        Route::post('{stage}/call-priority', [TicketController::class, 'callNextPriority'])
            ->name('queue.priority');

        Route::post('{id}/finish', [TicketController::class, 'finish'])
            ->name('queue.finish');
        Route::post('{id}/cancel', [TicketController::class, 'cancel'])
            ->name('queue.cancel');
        Route::post('{id}/advance', [TicketController::class, 'advance'])
            ->name('queue.advance');
    });

    // Impressoras
    Route::get('/impressoras', [PrintController::class, 'index'])->name('printers.index');
    Route::post('/impressoras', [PrintController::class, 'store'])->name('printers.store');

    // Usuários (Gestão de categorias)
    Route::get('/users/assign-category', [UserController::class, 'showAssignCategory'])->name('users.assignCategory');
    Route::put('/users/{id}/update-category', [UserController::class, 'updateCategory'])->name('users.updateCategory');

    // --- ROTAS ADMINISTRATIVAS (Restritas por Categoria) ---
    Route::middleware('adminOrSuper')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/adminPanel', [PanelController::class, 'adminPanel'])->name('adminPanel');
    });

    Route::post('/panel/update-video', [PanelController::class, 'updateVideo'])
        ->middleware('superAdmin')
        ->name('panel.updateVideo');
});

require __DIR__.'/auth.php';