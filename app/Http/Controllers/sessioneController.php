<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Sessione;
use App\Models\Appello;
use App\Models\Corso;
use Carbon\Carbon;

class sessioneController extends Controller
{
    public function index()
    {
        $sessioni = Sessione::with('corso')->orderBy('data_inizio', 'desc')->get();
        $corsi = Corso::orderBy('nome', 'asc')->get();

        return view('admin.sessioni.index', compact('sessioni', 'corsi'));
    }

    public function create()
    {
        $corsi = Corso::orderBy('nome', 'asc')->orderBy('tipo_corso', 'asc')->get();

        return view('admin.sessioni.create', compact('corsi'));
    }

    /**
     * Una sessione "per più corsi" è salvata come una riga per ciascun corso
     * (sessione_esami.corso_id): qui le creiamo tutte insieme.
     */
    public function store(Request $request)
    {
        $this->derivaAnno($request);

        $minDataInizio = Carbon::today()->addMonth()->format('Y-m-d');

        $validated = $request->validate(
            $this->regole() + [
                'data_inizio' => ['required', 'date', 'after_or_equal:' . $minDataInizio],
            ],
            $this->messaggi() + [
                'data_inizio.after_or_equal' => 'La data di inizio della sessione deve essere programmata con almeno un mese di anticipo rispetto a oggi.',
            ]
        );

        $corsiIds = array_map('intval', $validated['corsi']);

        if ($errori = $this->conflittiDistanza($corsiIds, $validated['data_inizio'], $validated['data_fine'], [])) {
            return back()->withErrors($errori)->withInput();
        }

        DB::transaction(function () use ($corsiIds, $validated) {
            foreach ($corsiIds as $corsoId) {
                Sessione::create($this->attributi($validated) + ['corso_id' => $corsoId]);
            }
        });

        $n = count($corsiIds);

        return redirect()->route('admin.sessioni.index')
            ->with('success', $n === 1
                ? 'Sessione d\'esame creata con successo!'
                : "Sessione d'esame creata con successo per {$n} corsi!");
    }

    public function edit($id)
    {
        $sessione = Sessione::findOrFail($id);

        if (Carbon::parse($sessione->data_fine)->lt(Carbon::today())) {
            return back()->with('error', 'Impossibile modificare una sessione d\'esame già passata.');
        }

        $corsi = Corso::orderBy('nome', 'asc')->orderBy('tipo_corso', 'asc')->get();
        // Corsi che hanno già questa stessa sessione
        $corsiSelezionati = $sessione->gruppo()->pluck('corso_id')->filter()->map(fn ($c) => (int) $c)->all();

        return view('admin.sessioni.edit', compact('sessione', 'corsi', 'corsiSelezionati'));
    }

    /**
     * Modifica la sessione per tutti i corsi del suo gruppo:
     *  - corsi già presenti e ancora selezionati  -> aggiornati;
     *  - corsi nuovi selezionati                  -> sessione creata;
     *  - corsi deselezionati                      -> sessione rimossa (solo se senza appelli).
     */
    public function update(Request $request, $id)
    {
        $sessione = Sessione::findOrFail($id);

        if (Carbon::parse($sessione->data_fine)->lt(Carbon::today())) {
            return back()->with('error', 'Impossibile modificare una sessione d\'esame già passata.');
        }

        $this->derivaAnno($request);

        $validated = $request->validate(
            $this->regole() + ['data_inizio' => 'required|date'],
            $this->messaggi()
        );

        $selezionati = array_map('intval', $validated['corsi']);
        $gruppo = $sessione->gruppo()->filter(fn ($s) => $s->corso_id !== null)->keyBy('corso_id');

        $daAggiornare = $gruppo->filter(fn ($s, $corsoId) => in_array((int) $corsoId, $selezionati, true));
        $daRimuovere  = $gruppo->reject(fn ($s, $corsoId) => in_array((int) $corsoId, $selezionati, true));
        $daCreare     = array_values(array_diff($selezionati, $gruppo->keys()->map(fn ($c) => (int) $c)->all()));

        $errori = [];

        // Distanza minima di un mese dalle altre sessioni di ciascun corso (il gruppo stesso è escluso)
        $errori += $this->conflittiDistanza($selezionati, $validated['data_inizio'], $validated['data_fine'], $gruppo->pluck('sessione_id')->all());

        // Sessioni da rimuovere: non devono avere appelli
        foreach ($daRimuovere as $corsoId => $s) {
            if ($s->appelli()->exists()) {
                $errori["corsi"] = "Il corso '" . $this->nomeCorso($corsoId) . "' ha già appelli in questa sessione: eliminali prima di rimuoverlo.";
                break;
            }
        }

        // Sessioni da aggiornare: appelli dentro il nuovo periodo e numero_appelli sufficiente
        foreach ($daAggiornare as $corsoId => $s) {
            $fuoriPeriodo = Appello::where('sessione_id', $s->sessione_id)
                ->where(function ($q) use ($validated) {
                    $q->where('data', '<', $validated['data_inizio'])
                      ->orWhere('data', '>', $validated['data_fine']);
                })
                ->count();

            if ($fuoriPeriodo > 0) {
                $errori['data_inizio'] = "Per il corso '" . $this->nomeCorso($corsoId) . "' ci sono {$fuoriPeriodo} appelli già inseriti fuori dal nuovo periodo: spostali o eliminali prima di modificare le date.";
                break;
            }
        }

        foreach ($daAggiornare as $corsoId => $s) {
            $massimoPresente = (int) Appello::where('sessione_id', $s->sessione_id)
                ->selectRaw('COUNT(*) AS n')
                ->groupBy('ins_corso_id')
                ->orderByDesc('n')
                ->value('n');

            if ($validated['numero_appelli'] < $massimoPresente) {
                $errori['numero_appelli'] = "Per il corso '" . $this->nomeCorso($corsoId) . "' esiste già un insegnamento con {$massimoPresente} appelli in questa sessione: il numero previsto non può essere inferiore.";
                break;
            }
        }

        if ($errori) {
            return back()->withErrors($errori)->withInput();
        }

        DB::transaction(function () use ($daAggiornare, $daRimuovere, $daCreare, $validated) {
            foreach ($daAggiornare as $s) {
                $s->update($this->attributi($validated));
            }
            foreach ($daRimuovere as $s) {
                $s->delete();
            }
            foreach ($daCreare as $corsoId) {
                Sessione::create($this->attributi($validated) + ['corso_id' => $corsoId]);
            }
        });

        return redirect()->route('admin.sessioni.index')->with('success', 'Sessione d\'esame aggiornata con successo!');
    }

    /** Elimina la sessione del solo corso della riga selezionata. */
    public function destroy($id)
    {
        $sessione = Sessione::findOrFail($id);

        if (Carbon::parse($sessione->data_fine)->lt(Carbon::today())) {
            return back()->with('error', 'Impossibile eliminare una sessione d\'esame già passata.');
        }

        $sessione->delete();

        return redirect()->route('admin.sessioni.index')->with('success', 'Sessione d\'esame eliminata con successo!');
    }

    // --- HELPER ---

    /** L'anno è derivato dalla data di inizio (il form non ha un campo "anno"). */
    private function derivaAnno(Request $request): void
    {
        if ($request->filled('data_inizio')) {
            $request->merge(['anno' => Carbon::parse($request->data_inizio)->year]);
        }
    }

    private function attributi(array $validated): array
    {
        return [
            'nome'           => $validated['nome'],
            'anno'           => $validated['anno'],
            'numero_appelli' => $validated['numero_appelli'],
            'data_inizio'    => $validated['data_inizio'],
            'data_fine'      => $validated['data_fine'],
        ];
    }

    private function nomeCorso($corsoId): string
    {
        $c = Corso::find($corsoId);

        return $c ? "{$c->nome} ({$c->tipo_corso})" : "#{$corsoId}";
    }

    /**
     * Due sessioni dello stesso corso devono distare almeno un mese
     * (tra la fine dell'una e l'inizio dell'altra); le sovrapposizioni sono quindi escluse.
     * Restituisce un array di errori ['corsi' => messaggio] oppure [].
     *
     * @param int[] $corsiIds
     * @param int[] $escludiSessioneIds sessioni da ignorare (quelle che si stanno modificando)
     */
    private function conflittiDistanza(array $corsiIds, string $inizio, string $fine, array $escludiSessioneIds): array
    {
        $inizio = Carbon::parse($inizio);
        $fine = Carbon::parse($fine);
        $messaggi = [];

        foreach ($corsiIds as $corsoId) {
            $vicina = Sessione::where('corso_id', $corsoId)
                ->when($escludiSessioneIds, fn ($q) => $q->whereNotIn('sessione_id', $escludiSessioneIds))
                ->get()
                ->first(function ($altra) use ($inizio, $fine) {
                    $altraInizio = Carbon::parse($altra->data_inizio);
                    $altraFine = Carbon::parse($altra->data_fine);

                    // "meno di un mese" di distanza (sovrapposizione compresa)
                    return $inizio->lt($altraFine->copy()->addMonth())
                        && $altraInizio->lt($fine->copy()->addMonth());
                });

            if ($vicina) {
                $messaggi[] = "Il corso '" . $this->nomeCorso($corsoId) . "' ha già la sessione '{$vicina->nome}' (dal "
                    . Carbon::parse($vicina->data_inizio)->format('d/m/Y') . " al "
                    . Carbon::parse($vicina->data_fine)->format('d/m/Y')
                    . "): tra due sessioni dello stesso corso deve passare almeno un mese.";
            }
        }

        return $messaggi ? ['corsi' => implode(' ', $messaggi)] : [];
    }

    /** Regole comuni a creazione e modifica (data_inizio viene aggiunta dal chiamante). */
    private function regole(): array
    {
        return [
            'nome'           => 'required|string|max:150',
            'anno'           => 'required|integer',
            'numero_appelli' => 'required|integer|min:1|max:4',
            'corsi'          => 'required|array|min:1',
            'corsi.*'        => 'integer|distinct|exists:corso,corso_id',
            'data_fine'      => ['required', 'date', 'after_or_equal:data_inizio', function ($attribute, $value, $fail) {
                $inizio = request('data_inizio');

                if ($inizio && Carbon::parse($value)->lt(Carbon::parse($inizio)->addWeeks(2))) {
                    $fail('La data di fine deve essere di almeno due settimane successiva alla data di inizio.');
                }
            }],
        ];
    }

    private function messaggi(): array
    {
        return [
            'numero_appelli.min' => 'Il numero minimo di appelli previsti è 1.',
            'numero_appelli.max' => 'Il numero massimo di appelli previsti per una sessione è 4.',
            'corsi.required'     => 'Seleziona almeno un corso di studi.',
            'corsi.min'          => 'Seleziona almeno un corso di studi.',
            'corsi.*.exists'     => 'Uno dei corsi selezionati non esiste.',
        ];
    }
}
