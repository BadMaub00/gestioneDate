<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insegnamento', function (Blueprint $table) {
            $table->integer('insegnamento_id', true);
            $table->string('nome', 150);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insegnamento');
    }
};
