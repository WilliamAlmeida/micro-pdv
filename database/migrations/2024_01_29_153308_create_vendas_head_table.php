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
        Schema::create('vendas_head', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caixa_id');
            $table->tinyInteger('status')->default(0);
            $table->decimal('desconto', 12, 2)->default(0.00);
            $table->decimal('troco', 12, 2)->default(0.00);
            $table->decimal('valor_total', 12, 2)->default(0.00);
            $table->timestamps();

            // $table->unique(['id', 'caixa_id']);

            // Chave estrangeira
            $table->foreign('caixa_id')->references('id')->on('caixa')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendas_head');
    }
};
