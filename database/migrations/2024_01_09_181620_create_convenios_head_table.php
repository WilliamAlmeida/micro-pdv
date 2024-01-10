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
        Schema::create('convenios_head', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('caixa_id');
            $table->unsignedBigInteger('vendas_head_id');
            $table->unsignedBigInteger('clientes_id');
            $table->timestamps();

            // $table->foreign('vendas_head_id')->references('id')->on('vendas_head')
            //     ->onUpdate('NO ACTION')->onDelete('NO ACTION');

            // $table->foreign('caixa_id')->references('id')->on('caixa')
            //     ->onUpdate('NO ACTION')->onDelete('NO ACTION');

            // $table->foreign('clientes_id')->references('id')->on('clientes')
            //     ->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('convenios_head');
    }
};
