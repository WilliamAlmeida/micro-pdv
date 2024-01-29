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
        Schema::create('trib_cfop', function (Blueprint $table) {
            $table->id();
            $table->string('cfop', 10)->nullable();
            $table->text('descricao')->nullable();
            $table->longText('aplicacao')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trib_cfop');
    }
};
