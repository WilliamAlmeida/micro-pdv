<?php

namespace Database\Seeders;

use App\Models\Tributacoes\Ncm;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class NcmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ncms = Storage::disk('asset')->get('trib_ncm_tipi.json');
        $ncms = collect(json_decode($ncms));

        foreach ($ncms->chunk(100) as $ncm) {
            $ncm = $ncm->map(function($item) {
                return [
                    'ncm' => $item[0],
                    'descricao' => $item[1],
                    'aliq_ipi' => $item[2]
                ];
            })->filter(fn($item) => !empty($item));

            Ncm::insert($ncm->all());
        }
    }
}
