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
        Schema::create('convenios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('nome_fantasia', 255);
            $table->string('slug', 255)->nullable();
            $table->string('razao_social', 255)->nullable();
            $table->integer('idpais')->default(1);
            $table->integer('idestado')->default(0);
            $table->integer('idcidade')->default(0);
            $table->string('cnpj', 18)->nullable();
            $table->string('inscricao_estadual', 20)->nullable()->comment('000.000.000.000');
            $table->string('cpf', 14)->nullable();
            $table->string('end_logradouro', 255)->nullable();
            $table->string('end_numero', 10)->default('S/N');
            $table->string('end_complemento', 255)->nullable();
            $table->string('end_bairro', 255)->nullable();
            $table->string('end_uf', 2)->nullable();
            $table->string('end_cidade', 255)->nullable();
            $table->string('end_cep', 10)->nullable();
            $table->string('whatsapp', 11)->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Chave primária composta
            // $table->primary(['id', 'tenant_id']);

            // Chave estrangeira
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('convenios');
    }
};
