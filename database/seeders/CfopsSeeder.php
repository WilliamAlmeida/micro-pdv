<?php

namespace Database\Seeders;

use App\Models\Tributacoes\Cfop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CfopsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cfops = Storage::disk('asset')->get('trib_cfop.json');
        $cfops = collect(json_decode($cfops));

        foreach ($cfops->chunk(100) as $cfop) {
            $cfop = $cfop->map(function($item) {
                return [
                    'cfop' => $item[0],
                    'descricao' => $item[1],
                    'aplicacao' => $item[2]
                ];
            })->filter(fn($item) => !empty($item));

            Cfop::insert($cfop->all());
        }
    }
}
