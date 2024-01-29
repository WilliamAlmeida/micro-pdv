<?php

namespace Database\Seeders;

use App\Models\Pais;
use App\Models\Estado;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EstadosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estados = Storage::disk('asset')->get('estados.json');
        $estados = collect(json_decode($estados));

        $paises = Pais::pluck('id', 'sigla')->all();

        if(!empty($paises)) {
            foreach ($estados->chunk(100) as $estado) {
                $estado = $estado->map(function($item) use ($paises) {
                    if(isset($paises[$item[3]])) {
                        return [
                            'nome' => $item[0],
                            'codigo' => $item[1],
                            'uf' => $item[2],
                            'pais_id' => $paises[$item[3]],
                        ];
                    }
                })->filter(fn($item) => !empty($item));

                Estado::insert($estado->all());
            }
        }
    }
}
