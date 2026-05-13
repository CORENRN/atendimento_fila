<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::call(function () {
    DB::table('tickets')->update(['ticket_number' => 0]);
    DB::table('tickets')->update(['status' => 'finalizado']);
    DB::table('ticket_counters')->updateOrInsert(
        ['last_number' => 0],
    );
})->dailyAt('23:59');