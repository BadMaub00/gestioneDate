<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsegnamentiCorso extends Model
{
    protected $table = 'insegnamento_corso';
    
    protected $primaryKey = 'ins_corso_id';

    protected $fillable = ['corso_id', 'insegnamento_id', 'durata', 'cfu', 'anno_di_corso'];

    public function corso()
    {
        return $this->belongsTo(Corso::class, 'corso_id', 'corso_id');
    }

    public function insegnamento()
    {
        return $this->belongsTo(Insegnamento::class, 'insegnamento_id', 'insegnamento_id');
    }

    public function appelli()
    {
        // Collega questo insegnamento del corso ai suoi appelli
        return $this->hasMany(Appello::class, 'ins_corso_id', 'ins_corso_id');
    }
}