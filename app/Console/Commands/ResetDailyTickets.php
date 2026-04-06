<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetDailyTickets extends Command
{
    // O comando que você usaria no terminal
    protected $signature = 'tickets:reset';

    protected $description = 'Reinicia a contagem de tickets diariamente';

    public function handle()
    {
        // Se você usa uma tabela de "sequência" ou quer dar truncate:
        // CUIDADO: truncate apaga TODOS os dados da tabela.
        DB::table('tickets')->truncate(); 
        
        // Se você quer apenas resetar o AUTO_INCREMENT sem apagar (se for MySQL):
        // DB::statement("ALTER TABLE tickets AUTO_INCREMENT = 1;");

        $this->info('Contagem de tickets reiniciada com sucesso!');
    }
}