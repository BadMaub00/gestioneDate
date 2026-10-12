<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    protected $table = 'docente';
    protected $primaryKey = 'docente_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['nome','cognome','data_nascita'];

    public function datiAccesso(){
        return $this->hasOne(DatiAccesso::class, 'utente_id', 'docente_id');
    }

}
