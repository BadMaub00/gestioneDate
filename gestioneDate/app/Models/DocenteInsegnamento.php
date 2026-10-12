<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocenteInsegnamento extends Model
{
    protected $table = 'docente_insegnamento';
    protected $primaryKey = 'id';
    protected $fillable = ['ins_corso_id','docente_id','ruolo'];

    public function docenti()
    {
        return $this->belongsTo(Docente::class, 'docente_id', 'docente_id');
    }
    public function insegnamentiCorso()
    {
        return $this->belongsTo(InsegnamentiCorso::class, 'ins_corso_id', 'ins_corso_id');
    }

    public function getDocInsId(){
        return $this->id;
    }
}
