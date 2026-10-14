<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocenteInsegnamento extends Model
{
    protected $table = 'docente_insegnamento';
    protected $primaryKey = 'doc_ins_id';
    protected $fillable = ['ins_corso_id','utente_id','ruolo'];

    public function docente()
    {
        return $this->belongsTo(Utente::class, 'utente_id', 'utente_id');
    }
    public function insegnamentiCorso()
    {
        return $this->belongsTo(InsegnamentiCorso::class, 'ins_corso_id', 'ins_corso_id');
    }

}
