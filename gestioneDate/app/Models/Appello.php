<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Appello extends Model
{
    use HasFactory;

    protected $table = 'appello';
    protected $primaryKey = 'appello_id';

    // Disabilita la gestione automatica delle colonne created_at e updated_at
    public $timestamps = false;

    protected $fillable = [
        'ins_corso_id', 
        'sessione_id', 
        'data', 
        'ora_inizio', 
        'ora_fine'
    ];

    public function sessione()
    {
        return $this->belongsTo(Sessione::class, 'sessione_id', 'sessione_id');
    }

    public function insegnamentoCorso()
    {
        return $this->belongsTo(InsegnamentiCorso::class, 'ins_corso_id', 'ins_corso_id');
    }
}