<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class DocenteInsegnamento extends Model
{
    use HasFactory;

    public const RUOLO_PRIMARIO = 'Primario';
    public const RUOLO_SECONDARIO = 'Secondario';
    public const RUOLI = [self::RUOLO_PRIMARIO, self::RUOLO_SECONDARIO];

    protected $table = 'docente_insegnamento';
    protected $primaryKey = 'doc_ins_id';

    // Disabilita la gestione automatica dei timestamp
    public $timestamps = false;

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'ins_corso_id',
        'utente_id',
        'ruolo',
    ];

    public function docente()
    {
        return $this->belongsTo(Utente::class, 'utente_id', 'utente_id');
    }

    public function insegnamentiCorso()
    {
        return $this->belongsTo(InsegnamentiCorso::class, 'ins_corso_id', 'ins_corso_id');
    }

    // ---------------------------------------------------------------- scope

    public function scopeDelDocente($query, $utenteId)
    {
        return $query->where('utente_id', $utenteId);
    }

    public function scopePrimari($query)
    {
        return $query->where('ruolo', self::RUOLO_PRIMARIO);
    }

    /** Filtri opzionali dell'elenco associazioni: corso_id, ins_corso_id, docente_id. */
    public function scopeFiltra($query, array $filtri)
    {
        if (filled($filtri['corso_id'] ?? null)) {
            $query->whereHas('insegnamentiCorso', fn ($q) => $q->where('corso_id', $filtri['corso_id']));
        }
        if (filled($filtri['ins_corso_id'] ?? null)) {
            $query->where('ins_corso_id', $filtri['ins_corso_id']);
        }
        if (filled($filtri['docente_id'] ?? null)) {
            $query->where('utente_id', $filtri['docente_id']);
        }

        return $query;
    }

    // ---------------------------------------------------------------- letture

    /** Associazioni filtrate, ordinate per corso/cognome/nome e raggruppate per nome del corso. */
    public static function elencoPerCorso(array $filtri = []): Collection
    {
        return static::with(['docente', 'insegnamentiCorso.insegnamento', 'insegnamentiCorso.corso'])
            ->filtra($filtri)
            ->get()
            ->sortBy([
                fn ($a, $b) => strcmp($a->insegnamentiCorso->corso->nome ?? '', $b->insegnamentiCorso->corso->nome ?? ''),
                fn ($a, $b) => strcmp($a->docente->cognome ?? '', $b->docente->cognome ?? ''),
                fn ($a, $b) => strcmp($a->docente->nome ?? '', $b->docente->nome ?? ''),
            ])
            ->groupBy(fn ($assoc) => $assoc->insegnamentiCorso->corso->nome ?? 'N/D')
            ->sortKeys();
    }

    /** Tutti i docenti di un insegnamento del corso (con nome e cognome) in una sola query. */
    public static function docentiConDati(int $insCorsoId): Collection
    {
        return static::where('docente_insegnamento.ins_corso_id', $insCorsoId)
            ->join('utente', 'docente_insegnamento.utente_id', '=', 'utente.utente_id')
            ->select('utente.utente_id', 'utente.nome', 'utente.cognome', 'docente_insegnamento.ruolo')
            ->get();
    }

    // ---------------------------------------------------------------- regole

    /**
     * Regole per associare un docente a un insegnamento del corso.
     * Restituisce [campo => messaggio] oppure null se l'associazione è consentita.
     */
    public static function erroreAssociazione(int $insCorsoId, Utente $utente, string $ruolo): ?array
    {
        if (!$utente->isDocente()) {
            return ['utente_id' => 'Solo gli utenti con ruolo "Docente" possono essere associati ad un insegnamento!'];
        }

        $perInsegnamento = static::where('ins_corso_id', $insCorsoId);

        if ((clone $perInsegnamento)->where('utente_id', $utente->getKey())->exists()) {
            return ['utente_id' => 'Questo docente è già associato a questo insegnamento!'];
        }

        if ($ruolo === self::RUOLO_PRIMARIO && (clone $perInsegnamento)->primari()->exists()) {
            return ['ruolo' => 'Questo insegnamento ha già un docente con ruolo Primario!'];
        }

        return null;
    }
}
