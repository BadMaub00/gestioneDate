<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insegnamento extends Model
{
    use HasFactory;

    protected $table = 'insegnamento';
    protected $primaryKey = 'insegnamento_id';

    // Disabilita la gestione automatica delle colonne created_at e updated_at
    public $timestamps = false;

    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $fillable = [
        'nome',
    ];

    /** Presenze di questo insegnamento nei piani di studio dei corsi. */
    public function insegnamentiCorso()
    {
        return $this->hasMany(InsegnamentiCorso::class, 'insegnamento_id', 'insegnamento_id');
    }
}