<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Files;
use App\Models\Tenant;
use App\Models\Horarios;
use App\Models\Tenant\Clientes;
use App\Models\Tenant\Produtos;
use Illuminate\Database\Seeder;
use App\Models\Tenant\Convenios;
use App\Models\Tenant\Categorias;
use App\Models\Tenant\Impressoes;
use App\Models\Tenant\Fornecedores;
use App\Models\Tenant\EstoqueMovimentacoes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ResetEmpresas extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Horarios::truncate();
        Fornecedores::truncate();
        Clientes::truncate();
        Categorias::truncate();
        Impressoes::truncate();
        EstoqueMovimentacoes::truncate();
        Produtos::truncate();
        Convenios::truncate();
        Files::truncate();

        foreach (Tenant::get() as $tenant) {
            $tenant->users()->sync([]);
        }

        Tenant::query()->delete();

        User::query()->update(['empresas_id' => null]);
    }
}
