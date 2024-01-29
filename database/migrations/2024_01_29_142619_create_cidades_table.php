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
        Schema::create('cidades', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 120)->nullable();
            $table->string('codigo', 100)->nullable();
            $table->unsignedBigInteger('estado_id')->nullable();
            $table->timestamps();

            // Chave estrangeira
            $table->foreign('estado_id')->references('id')->on('estados')->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cidades');
    }
};
