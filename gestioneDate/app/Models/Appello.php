<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appello extends Model
{
    use HasFactory;

    protected $table = 'appello';
    protected $primaryKey = 'appello_id';
    public $timestamps = false;

    protected $fillable = [
        'ins_corso_id', 
        'sessione_id', 
        'data', 
        'ora_inizio', 
        'ora_fine',
        'aula_id',
    ];

    public function sessione()
    {
        return $this->belongsTo(Sessione::class, 'sessione_id', 'sessione_id');
    }

    public function insegnamentoCorso()
    {
        return $this->belongsTo(InsegnamentiCorso::class, 'ins_corso_id', 'ins_corso_id');
    }

    public function aula()
    {
        return $this->belongsTo(Aula::class, 'aula_id', 'aula_id');
    }
}