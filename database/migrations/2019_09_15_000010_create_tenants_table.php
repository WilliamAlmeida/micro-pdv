<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();

            $table->integer('id_tipo_empresa')->default(0);
            $table->string('nome_fantasia', 255);
            $table->string('slug', 255)->nullable();
            $table->string('razao_social', 255)->nullable();
            $table->integer('idpais')->default(1);
            $table->integer('idestado')->default(0);
            $table->integer('idcidade')->default(0);
            $table->string('cnpj', 18)->nullable();
            $table->string('cpf', 14)->nullable();
            $table->string('end_logradouro', 255)->nullable();
            $table->string('end_numero', 10)->default('S/N');
            $table->string('end_complemento', 255)->nullable();
            $table->string('end_bairro', 255)->nullable();
            $table->string('end_cidade', 255)->nullable();
            $table->string('end_cep', 10)->nullable();
            $table->integer('file_ticket')->nullable();
            $table->integer('file_logo')->nullable();
            $table->integer('file_background')->nullable();
            $table->string('whatsapp', 11)->nullable();
            $table->integer('whatsapp_status')->default(1);
            $table->integer('tema')->default(0);
            $table->text('keywords')->nullable();
            $table->string('description', 160)->nullable();
            $table->decimal('taxa_entrega', 12, 2)->default(0.00);
            $table->decimal('valor_min_entrega', 12, 2)->default(0.00);
            $table->decimal('isento_taxa_entrega', 12, 2)->default(0.00);
            $table->integer('tempo_entrega_min')->default(30);
            $table->integer('tempo_entrega_max')->default(60);
            $table->tinyInteger('negar_entrega')->default(0);
            $table->integer('ultimo_pedido')->default(0);
            $table->decimal('couvert', 12, 2)->default(0.00);
            $table->integer('garcom')->default(10);
            $table->decimal('rate', 5, 2)->default(5.00);
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('status_manual')->default(1);
            $table->tinyInteger('status_mesa')->default(0);
            $table->tinyInteger('impressao')->default(0);
            $table->tinyInteger('impressao_mesa')->default(0);
            $table->string('manifest_v', 15)->nullable();
            $table->tinyInteger('s_mesa')->default(0);
            $table->string('inscricao_municipal', 45)->nullable();
            $table->string('inscricao_estadual', 45)->nullable();
            $table->integer('regime_tributario')->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->json('data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
}
