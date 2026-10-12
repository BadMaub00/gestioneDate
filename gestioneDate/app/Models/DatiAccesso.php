<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class DatiAccesso extends Authenticatable
{
    protected $table = 'dati_accesso';
    protected $primaryKey = 'utente_id';
    protected $keyType = 'string';
    protected $fillable = ['utente_id','username','password','ruolo'];
    protected $hidden = ['password'];

    public function amministratore()
    {
        return $this->belongsTo(Amministratore::class, 'utente_id', 'admin_id');
    }

    public function docente()
    {
        return $this->belongsTo(Docente::class, 'utente_id', 'docente_id');
    }

    public function getUtenteId(){
        return $this->utente_id;
    }

}
