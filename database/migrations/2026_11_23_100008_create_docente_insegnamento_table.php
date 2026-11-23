<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('docente_insegnamento', function (Blueprint $table) {
            $table->integer('doc_ins_id', true);
            $table->integer('ins_corso_id');
            $table->integer('utente_id');
            $table->enum('ruolo', ['Primario', 'Secondario']);

            $table->foreign('ins_corso_id')->references('ins_corso_id')->on('insegnamento_corso')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('utente_id')->references('utente_id')->on('utente')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('docente_insegnamento');
    }
};
