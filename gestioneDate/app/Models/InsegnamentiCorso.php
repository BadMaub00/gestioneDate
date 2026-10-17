<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsegnamentiCorso extends Model
{
    use HasFactory;

    protected $table = 'insegnamento_corso';
    protected $primaryKey = 'ins_corso_id';

    // Disabilita la gestione automatica delle colonne created_at e updated_at
    public $timestamps = false;

    protected $fillable = [
        'corso_id',
        'insegnamento_id',
        'anno_di_corso',
        'cfu',
        'durata',
    ];

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
        return $this->hasMany(Appello::class, 'ins_corso_id', 'ins_corso_id');
    }
}