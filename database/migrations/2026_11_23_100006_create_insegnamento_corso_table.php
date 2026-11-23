<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insegnamento_corso', function (Blueprint $table) {
            $table->integer('ins_corso_id', true);
            $table->integer('corso_id');
            $table->integer('insegnamento_id');
            $table->integer('durata');
            $table->integer('cfu');
            $table->integer('anno_di_corso');

            $table->unique(['corso_id', 'insegnamento_id']);
            $table->foreign('corso_id')->references('corso_id')->on('corso')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('insegnamento_id')->references('insegnamento_id')->on('insegnamento')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insegnamento_corso');
    }
};
