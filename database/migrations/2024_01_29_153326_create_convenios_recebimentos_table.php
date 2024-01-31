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
        Schema::create('convenios_recebimentos', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('convenios_head_id');
            $table->unsignedBigInteger('caixa_id');
            $table->decimal('desconto', 12, 2)->default('0.00')->nullable();
            $table->decimal('troco', 12, 2)->default('0.00')->nullable();
            $table->decimal('valor_total', 12, 2)->default('0.00')->nullable();
            $table->timestamps();

            // $table->foreign('convenios_head_id')->references('id')->on('convenios_head')
            //     ->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('convenios_recebimentos');
    }
};
