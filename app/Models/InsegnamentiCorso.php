<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class InsegnamentiCorso extends Model
{
    protected $table = 'insegnamento_corso';
    protected $primaryKey = 'ins_corso_id';
    public $timestamps = false;

    public $incrementing = true;
    protected $keyType = 'int';
    

    protected $fillable = [
        'corso_id',
        'insegnamento_id',
        'durata',
        'cfu',
        'anno_di_corso',
    ];

    /**
     * Relazione con l'insegnamento.
     */
    public function insegnamento()
    {
        return $this->belongsTo(Insegnamento::class, 'insegnamento_id', 'insegnamento_id');
    }

    /**
     * Relazione con il corso di studio.
     */
    public function corso()
    {
        return $this->belongsTo(Corso::class, 'corso_id', 'corso_id');
    }

    /**
     * Relazione con gli appelli d'esame legati a questo insegnamento del corso.
     */
    public function appelli()
    {
        return $this->hasMany(Appello::class, 'ins_corso_id', 'ins_corso_id');
    }

    /** Associazioni dei docenti (Primario/Secondario) a questo insegnamento del corso. */
    public function docenti()
    {
        return $this->hasMany(DocenteInsegnamento::class, 'ins_corso_id', 'ins_corso_id');
    }

    /** True se l'insegnamento fa già parte del piano di studi del corso. */
    public static function giaNelPiano(int $corsoId, int $insegnamentoId): bool
    {
        return static::where('corso_id', $corsoId)->where('insegnamento_id', $insegnamentoId)->exists();
    }

    /** True se esistono appelli in sessioni già concluse (rimuoverlo farebbe perdere lo storico). */
    public function haAppelliInSessioniConcluse(): bool
    {
        return $this->appelli()
            ->whereHas('sessione', fn ($q) => $q->where('data_fine', '<', Carbon::today()))
            ->exists();
    }

    /** Id degli insegnamenti dello stesso anno di corso dello stesso corso (compreso questo). */
    public function idsStessoAnno(): Collection
    {
        return static::where('corso_id', $this->corso_id)
            ->where('anno_di_corso', $this->anno_di_corso)
            ->pluck('ins_corso_id');
    }

    /**
     * Sessioni del corso con, in ->appelli_insegnamento, gli appelli di QUESTO insegnamento
     * (in ordine cronologico) e le relative aule.
     */
    public function sessioniConAppelli(): Collection
    {
        $sessioni = Sessione::where('corso_id', $this->corso_id)->orderBy('data_inizio', 'asc')->get();

        // Una sola query per tutti gli appelli, poi distribuiti per sessione
        $appelliPerSessione = Appello::where('ins_corso_id', $this->ins_corso_id)
            ->whereIn('sessione_id', $sessioni->pluck('sessione_id'))
            ->with('aula.edificio')
            ->orderBy('data', 'asc')
            ->orderBy('ora_inizio', 'asc')
            ->get()
            ->groupBy('sessione_id');

        foreach ($sessioni as $sessione) {
            $sessione->appelli_insegnamento = $appelliPerSessione->get($sessione->sessione_id, collect());
        }

        return $sessioni;
    }
}