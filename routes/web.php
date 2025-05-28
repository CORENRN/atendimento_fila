<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\GuicheController;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;


Route::get('/', function () {
    return view('welcome');
});


Route::get('/painel', [PanelController::class, 'index'])->name('panel.index');
Route::get('/painel/data', [PanelController::class, 'data'])->name('panel.data');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/selecionar-guiche', [GuicheController::class, 'showSelectGuiche'])->name('guiche.select.view');
    Route::post('/selecionar-guiche', [GuicheController::class, 'selectGuiche'])->name('guiche.select');

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


});

require __DIR__.'/auth.php';



