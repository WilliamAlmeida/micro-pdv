<?php

namespace Database\Seeders;

use App\Models\Caixa;
use App\Models\CaixaSangriaEntrada;
use App\Models\ConveniosHead;
use App\Models\ConveniosItens;
use App\Models\ConveniosRecebimentos;
use App\Models\ConveniosPagamentos;
use App\Models\VendasHead;
use App\Models\VendasItens;
use App\Models\VendasPagamentos;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ResetDadosCaixa extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Caixa::truncate();
        CaixaSangriaEntrada::truncate();
        VendasHead::truncate();
        VendasItens::truncate();
        VendasPagamentos::truncate();
        ConveniosHead::truncate();
        ConveniosItens::truncate();
        ConveniosRecebimentos::truncate();
        ConveniosPagamentos::truncate();
    }
}
