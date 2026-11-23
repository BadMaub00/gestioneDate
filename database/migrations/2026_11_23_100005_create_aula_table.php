<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aula', function (Blueprint $table) {
            $table->integer('aula_id', true);
            $table->string('nome', 100);
            $table->integer('edificio_id');
            $table->integer('capienza');

            $table->foreign('edificio_id')->references('edificio_id')->on('edificio')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aula');
    }
};
