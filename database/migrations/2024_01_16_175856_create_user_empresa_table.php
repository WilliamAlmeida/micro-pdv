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
        Schema::create('user_empresa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('empresa_id', 191);
            $table->timestamps();

            $table->index('user_id');
            $table->index('empresa_id');
            $table->unique(['user_id', 'empresa_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_empresa');
    }
};
