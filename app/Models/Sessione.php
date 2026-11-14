<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Sessione extends Model
{
    use HasFactory;

    protected $table = 'sessione_esami';
    protected $primaryKey = 'sessione_id';

    // Disabilita la gestione automatica delle colonne created_at e updated_at
    public $timestamps = false;

    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $fillable = [
        'corso_id',
        'data_inizio', 
        'data_fine', 
        'nome', 
        'anno', 
        'numero_appelli',
        'scadenza_inserimento',
    ];

    /** Scadenza di inserimento appelli quando non è indicata: un mese prima dell'inizio della sessione. */
    public static function scadenzaPredefinita($dataInizio): Carbon
    {
        return Carbon::parse($dataInizio)->subMonth()->startOfDay();
    }

    /**
     * Ultimo giorno (incluso) in cui i docenti possono inserire/modificare/eliminare appelli.
     * Se la sessione non ha una scadenza esplicita, vale un mese prima dell'inizio.
     * Uso: $sessione->scadenza_effettiva
     */
    public function getScadenzaEffettivaAttribute(): Carbon
    {
        return $this->scadenza_inserimento
            ? Carbon::parse($this->scadenza_inserimento)->startOfDay()
            : static::scadenzaPredefinita($this->data_inizio);
    }

    /** True se la sessione è già terminata (data_fine nel passato). */
    public function isConclusa(): bool
    {
        return Carbon::parse($this->data_fine)->lt(Carbon::today());
    }

    /** True se la scadenza per l'inserimento degli appelli è già trascorsa. */
    public function terminiInserimentoScaduti(): bool
    {
        return Carbon::today()->gt($this->scadenza_effettiva);
    }

    public function corso()
    {
        return $this->belongsTo(Corso::class, 'corso_id', 'corso_id');
    }

    /**
     * Le "sorelle" di questa sessione: le righe create insieme per altri corsi
     * (stesso nome, anno, periodo e numero di appelli). Include la sessione stessa.
     */
    public function gruppo()
    {
        return static::where('nome', $this->nome)
            ->where('anno', $this->anno)
            ->where('data_inizio', $this->data_inizio)
            ->where('data_fine', $this->data_fine)
            ->where('numero_appelli', $this->numero_appelli)
            ->get();
    }

    public function appelli()
    {
        return $this->hasMany(Appello::class, 'sessione_id', 'sessione_id');
    }

    // ---------------------------------------------------------------- regole sulle date

    /** True se la sessione è quella del corso indicato. */
    public function appartieneACorso(int $corsoId): bool
    {
        return (int) $this->corso_id === $corsoId;
    }

    /**
     * Prima sessione dello stesso corso che dista meno di un mese dal periodo indicato
     * (sovrapposizione compresa), oppure null. $escludiIds: sessioni da ignorare
     * (quelle che si stanno modificando).
     *
     * @param int[] $escludiIds
     */
    public static function vicinaA(int $corsoId, string $inizio, string $fine, array $escludiIds = []): ?self
    {
        $inizio = Carbon::parse($inizio);
        $fine = Carbon::parse($fine);

        return static::where('corso_id', $corsoId)
            ->when($escludiIds, fn ($q) => $q->whereNotIn('sessione_id', $escludiIds))
            ->get()
            ->first(function ($altra) use ($inizio, $fine) {
                $altraInizio = Carbon::parse($altra->data_inizio);
                $altraFine = Carbon::parse($altra->data_fine);

                return $inizio->lt($altraFine->copy()->addMonth())
                    && $altraInizio->lt($fine->copy()->addMonth());
            });
    }

    // ---------------------------------------------------------------- appelli della sessione

    /** Quanti appelli ha già un insegnamento del corso in questa sessione. */
    public function contaAppelli(int $insCorsoId): int
    {
        return $this->appelli()->where('ins_corso_id', $insCorsoId)->count();
    }

    /** Quanti appelli sono fuori dal periodo indicato (da spostare o eliminare prima di cambiare le date). */
    public function appelliFuoriPeriodo(string $inizio, string $fine): int
    {
        return $this->appelli()
            ->where(function ($q) use ($inizio, $fine) {
                $q->where('data', '<', $inizio)->orWhere('data', '>', $fine);
            })
            ->count();
    }

    /** Il maggior numero di appelli che un singolo insegnamento ha in questa sessione. */
    public function maxAppelliPerInsegnamento(): int
    {
        return (int) $this->appelli()
            ->selectRaw('COUNT(*) AS n')
            ->groupBy('ins_corso_id')
            ->orderByDesc('n')
            ->value('n');
    }
}