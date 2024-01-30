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
        Schema::create('produtos_has_categorias', function (Blueprint $table) {
            $table->unsignedBigInteger('produtos_id');
            $table->unsignedBigInteger('categorias_id');
            $table->timestamps();

            // Chave primária composta
            $table->primary(['produtos_id', 'categorias_id']);

            // Chaves estrangeiras
            $table->foreign('produtos_id')->references('id')->on('produtos')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('categorias_id')->references('id')->on('categorias')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtos_has_categorias');
    }
};
