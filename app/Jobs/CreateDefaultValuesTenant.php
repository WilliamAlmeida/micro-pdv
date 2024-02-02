<?php

namespace App\Jobs;

use Throwable;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\warning;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Stancl\Tenancy\Contracts\TenantWithDatabase;

class CreateDefaultValuesTenant implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var TenantWithDatabase|Model */
    protected $tenant;

    public function __construct(TenantWithDatabase $tenant)
    {
        $this->tenant = $tenant;
    }

    public function handle()
    {
        // Terminate execution of this job & other jobs in the pipeline
        if ($this->tenant->getInternal('create_database') === false) {
            return false;
        }

        info("Create Default Values Tenant {$this->tenant->nome_fantasia} init.");

        DB::beginTransaction();

        try {
            $convenio = $this->tenant->convenios()->create([
                'nome_fantasia'      => 'Avulso',
                'slug'               => 'avulso',
                'razao_social'       => 'AVULSO',
                'idpais'             => $this->tenant->idpais,
                'idestado'           => $this->tenant->idestado,
                'idcidade'           => $this->tenant->idcidade,
                'cnpj'               => null,
                'inscricao_estadual' => null,
                'cpf'                => '000.000.000-00',
                'end_logradouro'     => $this->tenant->end_logradouro,
                'end_numero'         => $this->tenant->end_numero,
                'end_complemento'    => $this->tenant->end_complemento,
                'end_bairro'         => $this->tenant->end_bairro,
                'end_uf'             => $this->tenant->end_uf,
                'end_cidade'         => $this->tenant->end_cidade,
                'end_cep'            => $this->tenant->end_cep,
            ]);

            $this->tenant->clientes()->create([
                'id_convenio'        => $convenio->id,
                'nome_fantasia'      => 'Geral',
                'slug'               => 'geral',
                'razao_social'       => 'GERAL',
                'idpais'             => $this->tenant->idpais,
                'idestado'           => $this->tenant->idestado,
                'idcidade'           => $this->tenant->idcidade,
                'cnpj'               => null,
                'inscricao_estadual' => null,
                'cpf'                => '000.000.000-00',
                'end_logradouro'     => $this->tenant->end_logradouro,
                'end_numero'         => $this->tenant->end_numero,
                'end_complemento'    => $this->tenant->end_complemento,
                'end_bairro'         => $this->tenant->end_bairro,
                'end_uf'             => $this->tenant->end_uf,
                'end_cidade'         => $this->tenant->end_cidade,
                'end_cep'            => $this->tenant->end_cep,
            ]);

            $categoria = $this->tenant->categorias()->create([
                'titulo' => 'Geral',
                'slug' => 'geral'
            ]);

            $produto = $this->tenant->produtos()->create([
                'titulo' => 'Produto Teste',
                'slug' => 'produto-teste',
                'descricao' => 'Este é um produto para testes criado no momento da geração da empresa, podendo ser alterado ou deletado se necessário',
                'codigo_barras_1' => '0000000000000',
                'preco_varejo' => 1.00,
                'estoque_atual' => 0,
            ]);

            $produto->categorias()->attach($categoria);

            info("Create Default Values Tenant {$this->tenant->nome_fantasia} finish.");

            DB::commit();

        } catch (\Throwable $th) {
            throw $th;

            DB::rollback();
        }
    }

    public function failed(Throwable $exception): void
    {
        info("Create Default Values Tenant {$this->tenant->nome_fantasia} error.");
        warning("Create Default Values Tenant {$this->tenant->nome_fantasia} error.");
        info($exception);
    }
}
