<?php

namespace Database\Seeders;

use App\Models\Tributacoes\Cest;
use App\Models\Tributacoes\Ncm;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cests = Storage::disk('asset')->get('trib_cest.json');
        $cests = collect(json_decode($cests));

        $ncms = Ncm::pluck('id', 'ncm')->all();

        if(!empty($ncms)) {
            foreach ($cests->chunk(100) as $cest) {
                $cest = $cest->map(function($item) use ($ncms) {
                    if(isset($ncms[$item[2]])) {
                        return [
                            'cest' => $item[0],
                            'descricao' => $item[1],
                            'ncm_id' => $ncms[$item[2]],
                        ];
                    }
                })->filter(fn($item) => !empty($item));

                Cest::insert($cest->all());
            }
        }
    }
}
