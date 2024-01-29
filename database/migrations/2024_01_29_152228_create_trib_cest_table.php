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
        Schema::create('trib_cest', function (Blueprint $table) {
            $table->id();
            $table->string('cest', 10)->nullable();
            $table->longText('descricao')->nullable();
            $table->foreignId('ncm_id')->nullable();
            $table->timestamps();

            $table->foreign('ncm_id')->references('id')->on('trib_ncm_tipi')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trib_cest');
    }
};
