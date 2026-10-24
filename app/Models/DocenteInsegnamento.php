<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocenteInsegnamento extends Model
{
    use HasFactory;

    protected $table = 'docente_insegnamento';
    protected $primaryKey = 'doc_ins_id';

    // Disabilita la gestione automatica dei timestamp
    public $timestamps = false;

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'ins_corso_id',
        'utente_id',
        'ruolo',
    ];

    public function docente()
    {
        return $this->belongsTo(Utente::class, 'utente_id', 'utente_id');
    }

    public function insegnamentiCorso()
    {
        return $this->belongsTo(InsegnamentiCorso::class, 'ins_corso_id', 'ins_corso_id');
    }
}