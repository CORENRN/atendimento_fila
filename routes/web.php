<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\GuicheController;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;



Route::get('/painel', [PanelController::class, 'index'])->name('panel.index');
Route::get('/painel/data', [PanelController::class, 'data'])->name('panel.data');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/', [GuicheController::class, 'showSelectGuiche'])->name('guiche.select.view');
    Route::post('/selecionar-guiche', [GuicheController::class, 'selectGuiche'])->name('guiche.select');

    Route::get('home', [TicketController::class, 'home'])->name('home');

    Route::prefix('queue')->group(function () {
        Route::get('{stage}', [TicketController::class, 'queue'])->name('queue');
        Route::post('{stage}/call', [TicketController::class, 'callNext'])->name('queue.call');
        Route::post('{id}/finish', [TicketController::class, 'finish'])->name('queue.finish');
        Route::post('{id}/cancel', [TicketController::class, 'cancel'])->name('queue.cancel');
        Route::post('{id}/advance', [TicketController::class, 'advance'])->name('queue.advance');
        Route::post('recall/{id}', [TicketController::class, 'recall'])->name('queue.recall');

    });

    // Retirada de senha
    Route::get('/ticket/take', function () {
        return view('ticket_take');
    })->name('ticket.take');

    Route::post('/ticket/take', [TicketController::class, 'takeTicket'])->name('ticket.take.post');
    Route::get('/ticket/{id}', [TicketController::class, 'showTicket'])->name('ticket.show');


    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


});

require __DIR__.'/auth.php';



