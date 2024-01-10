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
        Schema::create('convenios_itens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('convenios_head_id');
            $table->unsignedBigInteger('produtos_id');
            $table->string('descricao');
            $table->decimal('quantidade', 12, 2)->default('1.00');
            $table->decimal('preco', 12, 2)->default('0.00');
            $table->decimal('desconto', 12, 2)->default('0.00');
            $table->decimal('valor_total', 12, 2)->default('0.00');
            $table->integer('status')->nullable()->default('0');
            $table->unsignedBigInteger('convenios_recebimentos_id');
            $table->timestamps();

            // $table->foreign('convenios_head_id')->references('id')->on('convenios_head')
            //     ->onUpdate('NO ACTION')->onDelete('NO ACTION');

            // $table->foreign('produtos_id')->references('id')->on('produtos')
            //     ->onUpdate('NO ACTION')->onDelete('NO ACTION');

            // $table->foreign('convenios_recebimentos_id')->references('id')->on('convenios_recebimentos')
            //     ->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('convenios_itens');
    }
};
