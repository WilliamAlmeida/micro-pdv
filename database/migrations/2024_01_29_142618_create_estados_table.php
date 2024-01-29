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
        Schema::create('estados', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 75)->nullable();
            $table->string('codigo', 100)->nullable();
            $table->string('uf', 5)->nullable();
            $table->unsignedBigInteger('pais_id')->nullable();
            $table->timestamps();

            // Chave estrangeira
            $table->foreign('pais_id')->references('id')->on('paises')->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estados');
    }
};
