<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appello extends Model
{
    use HasFactory;

    protected $table = 'appello';
    protected $primaryKey = 'appello_id';
    public $timestamps = false;

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'ins_corso_id', 
        'sessione_id', 
        'data', 
        'ora_inizio', 
        'ora_fine',
        'aula_id',
        'note',
    ];

    public function sessione()
    {
        return $this->belongsTo(Sessione::class, 'sessione_id', 'sessione_id');
    }

    public function insegnamentoCorso()
    {
        return $this->belongsTo(InsegnamentiCorso::class, 'ins_corso_id', 'ins_corso_id');
    }

    public function aula()
    {
        return $this->belongsTo(Aula::class, 'aula_id', 'aula_id');
    }

    /** Appelli che occupano (anche in parte) la fascia oraria indicata nello stesso giorno. */
    public function scopeSovrapposti($query, string $data, string $oraInizio, string $oraFine, ?int $escludiAppelloId = null)
    {
        return $query->where('data', $data)
            ->where('ora_inizio', '<', $oraFine)
            ->where('ora_fine', '>', $oraInizio)
            ->when($escludiAppelloId, fn ($q) => $q->where('appello_id', '!=', $escludiAppelloId));
    }

    /** Appelli di insegnamenti di un corso (e, se indicato, di un anno di corso). */
    public function scopeDelCorso($query, $corsoId, $annoCorso = null)
    {
        return $query->whereHas('insegnamentoCorso', function ($q) use ($corsoId, $annoCorso) {
            $q->where('corso_id', $corsoId);
            if ($annoCorso) {
                $q->where('anno_di_corso', $annoCorso);
            }
        });
    }
}
