<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    use HasFactory;

    protected $table = 'aula';
    protected $primaryKey = 'aula_id';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'edificio_id',
        'capienza',
    ];

    /**
     * Relazione con l'edificio di appartenenza.
     */
    public function edificio()
    {
        return $this->belongsTo(Edificio::class, 'edificio_id', 'edificio_id');
    }

    /**
     * Relazione con gli appelli programmati nell'aula.
     */
    public function appelli()
    {
        return $this->hasMany(Appello::class, 'aula_id', 'aula_id');
    }
}