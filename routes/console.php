<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::call(function () {
  
    DB::table('tickets')
        ->where('status', '!=', 'finalizado')
        ->update([
            'ticket_number' => 0,
            'status' => 'finalizado',
            'updated_at' => now()
        ]);


    DB::table('ticket_counters')->update(['last_number' => 0]);

    DB::table('user_guiche')->truncate(); 

})->dailyAt('23:59')->timezone('America/Fortaleza');