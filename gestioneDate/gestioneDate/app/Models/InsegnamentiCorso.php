<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsegnamentiCorso extends Model
{
    protected $table = 'insegnamento_corso';
    protected $primaryKey = 'ins_corso_id';
    public $timestamps = false;

    protected $fillable = [
        'corso_id',
        'insegnamento_id',
        'durata',
        'cfu',
        'anno_di_corso',
    ];

    /**
     * Relazione con l'insegnamento.
     */
    public function insegnamento()
    {
        return $this->belongsTo(Insegnamento::class, 'insegnamento_id', 'insegnamento_id');
    }

    /**
     * Relazione con il corso di studio.
     */
    public function corso()
    {
        return $this->belongsTo(Corso::class, 'corso_id', 'corso_id');
    }

    /**
     * Relazione con gli appelli d'esame legati a questo insegnamento del corso.
     */
    public function appelli()
    {
        return $this->hasMany(Appello::class, 'ins_corso_id', 'ins_corso_id');
    }
}