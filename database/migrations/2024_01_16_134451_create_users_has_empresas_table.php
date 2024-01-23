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
        Schema::create('users_has_empresas', function (Blueprint $table) {
            $table->foreignId('users_id');
            $table->foreignId('empresas_id');
            $table->timestamps();

            $table->primary(['users_id', 'empresas_id'], 'user_empresa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_has_empresas');
    }
};
