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
        Schema::create('caixa_sangria_entrada', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caixa_id');
            $table->string('tipo', 1);
            $table->string('motivo', 255)->nullable();
            $table->decimal('valor', 12, 2)->default(0.00);
            $table->timestamps();

            // $table->unique(['id', 'caixa_id']);

            // Chave estrangeira
            $table->foreign('caixa_id')->references('id')->on('caixa')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caixa_sangria_entrada');
    }
};
