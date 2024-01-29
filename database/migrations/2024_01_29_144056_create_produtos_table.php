<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id');
            $table->string('titulo', 255);
            $table->string('slug', 150);
            $table->string('codigo_barras_1', 30)->nullable();
            $table->string('codigo_barras_2', 30)->nullable();
            $table->string('codigo_barras_3', 30)->nullable();
            $table->decimal('preco_varejo', 12, 2)->nullable();
            $table->decimal('preco_atacado', 12, 2)->nullable();
            $table->decimal('valor_garcom', 12, 2)->default(0.00);
            $table->decimal('preco_promocao', 12, 2)->nullable();
            $table->date('promocao_inicio')->nullable();
            $table->date('promocao_fim')->nullable();
            $table->string('trib_icms', 20)->nullable();
            $table->string('trib_csosn', 20)->nullable();
            $table->string('trib_cst', 20)->nullable();
            $table->string('trib_origem_produto', 20)->nullable();
            $table->string('trib_cfop_de', 20)->nullable();
            $table->string('trib_cfop_fe', 20)->nullable();
            $table->string('trib_ncm', 20)->nullable();
            $table->string('trib_cest', 20)->nullable();
            $table->decimal('estoque_atual', 10, 2)->default(-1.00);
            $table->string('unidade_medida', 5)->nullable();
            $table->string('codigo_externo', 255)->nullable();
            $table->tinyInteger('destaque')->nullable();
            $table->tinyInteger('somente_mesa')->nullable();
            $table->integer('sales')->default(0);
            $table->integer('views')->default(0);
            $table->integer('ordem')->default(999);
            $table->text('descricao')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Chave primária composta
            // $table->primary(['id', 'tenant_id']);

            // Chave estrangeira
            $table->foreign('tenant_id')->references('id')->on('tenants');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};
