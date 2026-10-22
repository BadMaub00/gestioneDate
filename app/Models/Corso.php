<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Corso extends Model
{
    use HasFactory;

    protected $table = 'corso';
    protected $primaryKey = 'corso_id';

    // Disabilita la gestione automatica delle colonne created_at e updated_at
    public $timestamps = false;

    protected $fillable = [
        'nome', 
        'tipo_corso'
    ];

    public function insegnamenti()
    {
        return $this->hasMany(InsegnamentiCorso::class, 'corso_id', 'corso_id');
    }

    public function sessioni()
    {
        return $this->hasMany(Sessione::class, 'corso_id', 'corso_id');
    }
}
