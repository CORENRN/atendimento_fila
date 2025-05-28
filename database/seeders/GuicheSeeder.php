<?php

namespace Database\Seeders;

use App\Models\Guiche;
use Illuminate\Database\Seeder;

class GuicheSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 6; $i++) {
            Guiche::create([
                'name' => 'Atendimento ' . $i,
            ]);
        }

        for ($i = 1; $i <= 2; $i++) {
            Guiche::create([
                'name' => 'Triagem ' . $i,
            ]);
        }
    }
}
