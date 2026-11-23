<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appello', function (Blueprint $table) {
            $table->integer('appello_id', true);
            $table->integer('ins_corso_id');
            $table->integer('sessione_id');
            $table->date('data');
            $table->time('ora_inizio');
            $table->time('ora_fine');
            $table->integer('aula_id');
            $table->text('note')->nullable();

            $table->foreign('ins_corso_id')->references('ins_corso_id')->on('insegnamento_corso')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('sessione_id')->references('sessione_id')->on('sessione_esami')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('aula_id')->references('aula_id')->on('aula')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appello');
    }
};
