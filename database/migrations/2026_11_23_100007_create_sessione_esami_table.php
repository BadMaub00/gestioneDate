<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sessione_esami', function (Blueprint $table) {
            $table->integer('sessione_id', true);
            $table->integer('corso_id')->nullable();
            $table->date('data_inizio');
            $table->date('data_fine');
            $table->string('nome', 150);
            $table->integer('anno');
            $table->integer('numero_appelli');
            $table->date('scadenza_inserimento')->nullable();

            $table->foreign('corso_id')->references('corso_id')->on('corso')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessione_esami');
    }
};
