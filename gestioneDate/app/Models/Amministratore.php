<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Amministratore extends Model
{
    protected $table = 'amministratore';
    protected $primaryKey = 'admin_id';
    //public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['nome,cognome','data_nascita'];

    public function datiAccesso()
    {
        return $this->hasOne(DatiAccesso::class, 'utente_id', 'admin_id');
    }

}
