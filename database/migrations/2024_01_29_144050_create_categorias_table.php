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
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id');
            $table->string('titulo', 100);
            $table->string('slug', 150);
            $table->integer('ordem')->default(1);
            $table->integer('file_imagem')->nullable();
            $table->string('codigo_barras_1', 30)->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Chave primária composta
            // $table->primary(['id', 'empresas_id']);

            // Chave estrangeira
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias');
    }
};
