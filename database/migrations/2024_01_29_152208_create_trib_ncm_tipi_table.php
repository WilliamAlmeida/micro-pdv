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
        Schema::create('trib_ncm_tipi', function (Blueprint $table) {
            $table->id();
            $table->string('ncm', 10)->nullable();
            $table->longText('descricao')->nullable();
            $table->decimal('aliq_ipi', 4, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trib_ncm_tipi');
    }
};
