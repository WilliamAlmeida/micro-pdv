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
        Schema::create('vendas_pagamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caixa_id');
            $table->foreignId('vendas_head_id');
            $table->string('forma_pagamento', 45);
            $table->decimal('valor', 12, 2)->default(0.00);
            $table->timestamps();

            // $table->unique(['id', 'caixa_id', 'vendas_head_id']);

            // Chave estrangeira
            $table->foreign('caixa_id')->references('id')->on('caixa')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('vendas_head_id')->references('id')->on('vendas_head')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendas_pagamentos');
    }
};
