<?php

namespace Database\Seeders;

use App\Models\Tenant\Convenios;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConveniosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Convenios::factory()->create();
    }
}
