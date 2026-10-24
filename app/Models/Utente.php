<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Utente extends Authenticatable
{
    protected $table = 'utente';
    protected $primaryKey = 'utente_id';
    protected $fillable = ['username','password','nome','cognome','data_nascita','ruolo'];
    protected $hidden = ['password'];

    public $timestamps = false;

    public $incrementing = true;
    protected $keyType = 'int';
    

    public function incarichi()
    {
        return $this->hasMany(DocenteInsegnamento::class, 'utente_id', 'utente_id');
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}