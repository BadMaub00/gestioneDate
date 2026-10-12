<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appello extends Model
{
    protected $table = 'appello';
    protected $primaryKey = 'id';
    protected $fillable = ['ins_corso_id', 'sessione_id', 'data', 'ora_inizio', 'ora_fine'];

    public function sessione()
    {
        return $this->belongsTo(Sessione::class, 'sessione_id', 'sessione_id');
    }

    public function insegnamentoCorso()
    {
        return $this->belongsTo(InsegnamentiCorso::class, 'ins_corso_id', 'ins_corso_id');
    }

    public function getAppelloId(){
        return $this->id;
    }
}
