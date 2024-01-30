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
        Schema::create('vendas_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caixa_id');
            $table->foreignId('vendas_head_id');
            $table->foreignId('produtos_id')->nullable();
            $table->string('descricao', 255);
            $table->decimal('quantidade', 12, 2)->default(1.00);
            $table->decimal('preco', 12, 2)->default(0.00);
            $table->decimal('desconto', 12, 2)->default(0.00);
            $table->decimal('valor_total', 12, 2)->default(0.00);
            $table->timestamps();

            // $table->unique(['id', 'caixa_id', 'produtos_id', 'vendas_head_id']);

            // Chave estrangeira
            $table->foreign('caixa_id')->references('id')->on('caixa')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('vendas_head_id')->references('id')->on('vendas_head')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('produtos_id')->references('id')->on('produtos')->cascadeOnUpdate()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendas_itens');
    }
};
