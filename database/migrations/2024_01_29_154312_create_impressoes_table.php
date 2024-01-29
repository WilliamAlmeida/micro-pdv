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
        Schema::create('impressoes', function (Blueprint $table) {
            $table->id();
            $table->string('rel_table', 45)->nullable();
            $table->foreignId('rel_id')->nullable();
            $table->tinyInteger('tipo')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->text('html');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('impressoes');
    }
};
