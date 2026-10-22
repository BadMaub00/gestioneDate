<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sessione extends Model
{
    use HasFactory;

    protected $table = 'sessione_esami';
    protected $primaryKey = 'sessione_id';

    // Disabilita la gestione automatica delle colonne created_at e updated_at
    public $timestamps = false;

    protected $fillable = [
        'corso_id',
        'data_inizio', 
        'data_fine', 
        'nome', 
        'anno', 
        'numero_appelli'
    ];

    public function corso()
    {
        return $this->belongsTo(Corso::class, 'corso_id', 'corso_id');
    }

    /**
     * Le "sorelle" di questa sessione: le righe create insieme per altri corsi
     * (stesso nome, anno, periodo e numero di appelli). Include la sessione stessa.
     */
    public function gruppo()
    {
        return static::where('nome', $this->nome)
            ->where('anno', $this->anno)
            ->where('data_inizio', $this->data_inizio)
            ->where('data_fine', $this->data_fine)
            ->where('numero_appelli', $this->numero_appelli)
            ->get();
    }

    public function appelli()
    {
        return $this->hasMany(Appello::class, 'sessione_id', 'sessione_id');
    }
}