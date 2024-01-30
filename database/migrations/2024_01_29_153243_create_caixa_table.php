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
        Schema::create('caixa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id');
            $table->foreignId('user_id')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->decimal('valor_inicial', 12, 2)->default(0.00);
            $table->decimal('sangria_total', 12, 2)->default(0.00);
            $table->decimal('entrada_total', 12, 2)->default(0.00);
            $table->timestamps();

            // $table->unique(['id', 'user_id']);

            // Chave estrangeira
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caixa');
    }
};
