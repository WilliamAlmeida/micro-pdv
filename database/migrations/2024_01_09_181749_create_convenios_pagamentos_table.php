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
        Schema::create('convenios_pagamentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('convenios_recebimentos_id');
            $table->string('forma_pagamento', 45);
            $table->decimal('valor', 12, 2)->default('0.00');
            $table->timestamps();

            // $table->foreign('convenios_recebimentos_id')->references('id')->on('convenios_recebimentos')
            //     ->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('convenios_pagamentos');
    }
};
