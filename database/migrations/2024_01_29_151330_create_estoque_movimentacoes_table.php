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
        Schema::create('estoque_movimentacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produtos_id');
            $table->string('tipo', 20);
            $table->decimal('quantidade', 10, 2);
            $table->string('motivo', 255)->nullable();
            $table->foreignId('fornecedores_id')->nullable();
            $table->string('nota_fiscal', 45)->nullable();
            $table->timestamps();

            // Chave primária composta
            // $table->primary(['id', 'produtos_id']);

            // Chave estrangeira
            $table->foreign('produtos_id')->references('id')->on('produtos')->onDelete('cascade');
            $table->foreign('fornecedores_id')->references('id')->on('fornecedores')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estoque_movimentacoes');
    }
};
