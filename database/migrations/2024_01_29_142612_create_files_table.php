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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->longText('url');
            $table->string('name', 255)->default('');
            $table->longText('full_name');
            $table->string('extension', 45)->default('');
            $table->longText('file_mimetype')->nullable();
            $table->string('rel_table', 255)->nullable();
            $table->foreignId('rel_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
