<?php

namespace Database\Seeders;

use App\Models\Pais;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaisesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pais::firstOrCreate([
            'nome' => 'Brasil',
            'sigla' => 'BR',
            'codigo' => '55',
        ]);
    }
}
