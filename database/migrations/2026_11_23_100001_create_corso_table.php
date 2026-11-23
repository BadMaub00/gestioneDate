<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corso', function (Blueprint $table) {
            $table->integer('corso_id', true);
            $table->string('nome', 150);
            $table->enum('tipo_corso', ['Triennale', 'Magistrale', 'Magistrale a ciclo unico']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corso');
    }
};
