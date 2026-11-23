<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('utente', function (Blueprint $table) {
            $table->integer('utente_id', true);
            $table->string('username', 100);
            $table->string('password', 100);
            $table->string('nome', 100);
            $table->string('cognome', 100);
            $table->date('data_nascita');
            $table->enum('ruolo', ['Docente', 'Amministratore']);

            $table->unique(['username']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('utente');
    }
};
