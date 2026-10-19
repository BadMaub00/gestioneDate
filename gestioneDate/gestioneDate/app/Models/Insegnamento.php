<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insegnamento extends Model
{
    use HasFactory;

    protected $table = 'insegnamento';
    protected $primaryKey = 'insegnamento_id';

    // Disabilita la gestione automatica delle colonne created_at e updated_at
    public $timestamps = false;

    protected $fillable = [
        'nome',
    ];
}