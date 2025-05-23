<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // <-- importar Hash

class AttendantsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('attendants')->insert([
            [
                'id' => 1,
                'name' => 'Atendente 1',
                'password' => Hash::make('senha123'), // senha padrão
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Atendente 2',
                'password' => Hash::make('senha123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
