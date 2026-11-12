<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Utente extends Authenticatable
{
    public const RUOLO_ADMIN = 'Amministratore';
    public const RUOLO_DOCENTE = 'Docente';

    protected $table = 'utente';
    protected $primaryKey = 'utente_id';
    protected $fillable = ['username','password','nome','cognome','data_nascita','ruolo'];
    protected $hidden = ['password'];

    public $timestamps = false;

    public $incrementing = true;
    protected $keyType = 'int';
    

    public function isAdmin(): bool
    {
        return $this->ruolo === self::RUOLO_ADMIN;
    }

    public function isDocente(): bool
    {
        return $this->ruolo === self::RUOLO_DOCENTE;
    }

    /**
     * URL della dashboard corretta in base al ruolo (Amministratore o Docente).
     * Usato dai componenti Blade condivisi (navbar, pulsante "Torna alla Dashboard").
     */
    public function dashboardRoute(): string
    {
        return route($this->isAdmin() ? 'admin.dashboard' : 'docente.dashboard');
    }

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