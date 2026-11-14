<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Corso extends Model
{
    use HasFactory;

    protected $table = 'corso';
    protected $primaryKey = 'corso_id';

    // Disabilita la gestione automatica delle colonne created_at e updated_at
    public $timestamps = false;

    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $fillable = [
        'nome', 
        'tipo_corso'
    ];

    /** Durata legale in anni per tipo di corso (unica definizione per tutta l'app). */
    public static function durataPerTipo(string $tipo): int
    {
        return match ($tipo) {
            'Magistrale' => 2,
            'Magistrale a ciclo unico' => 5,
            default => 3,
        };
    }

    public function durataAnni(): int
    {
        return self::durataPerTipo($this->tipo_corso);
    }

    /** Uso: $corso->durata_anni (comodo nelle viste). */
    public function getDurataAnniAttribute(): int
    {
        return $this->durataAnni();
    }

    /** Nome del corso con il tipo, per messaggi e liste: "Informatica (Triennale)". */
    public function etichetta(): string
    {
        return "{$this->nome} ({$this->tipo_corso})";
    }

    public function insegnamenti()
    {
        return $this->hasMany(InsegnamentiCorso::class, 'corso_id', 'corso_id');
    }

    public function sessioni()
    {
        return $this->hasMany(Sessione::class, 'corso_id', 'corso_id');
    }
}
