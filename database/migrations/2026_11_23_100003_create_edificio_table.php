<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('edificio', function (Blueprint $table) {
            $table->integer('edificio_id', true);
            $table->string('nome', 100);
            $table->string('via', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('edificio');
    }
};
