<?php

namespace Database\Seeders;

use App\Models\Tenant\Caixa;
use App\Models\Tenant\CaixaSangriaEntrada;
use App\Models\Tenant\ConveniosHead;
use App\Models\Tenant\ConveniosItens;
use App\Models\Tenant\ConveniosRecebimentos;
use App\Models\Tenant\ConveniosPagamentos;
use App\Models\Tenant\VendasHead;
use App\Models\Tenant\VendasItens;
use App\Models\Tenant\VendasPagamentos;
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
