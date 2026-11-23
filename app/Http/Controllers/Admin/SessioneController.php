<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Sessione;
use App\Models\Corso;
use App\Services\SessioneRules;
use Carbon\Carbon;

class SessioneController extends Controller
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

        $validated = $request->validate(
            SessioneRules::regoleCreazione(),
            SessioneRules::messaggiCreazione()
        );

        $corsiIds = array_map('intval', $validated['corsi']);

        if ($errori = SessioneRules::conflittiDistanza($corsiIds, $validated['data_inizio'], $validated['data_fine'])) {
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

        if ($sessione->isConclusa()) {
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

        if ($sessione->isConclusa()) {
            return back()->with('error', 'Impossibile modificare una sessione d\'esame già passata.');
        }

        $this->derivaAnno($request);

        $validated = $request->validate(
            SessioneRules::regoleModifica(),
            SessioneRules::messaggiModifica()
        );

        $selezionati = array_map('intval', $validated['corsi']);
        $gruppo = $sessione->gruppo()->filter(fn ($s) => $s->corso_id !== null)->keyBy('corso_id');

        $daAggiornare = $gruppo->filter(fn ($s, $corsoId) => in_array((int) $corsoId, $selezionati, true));
        $daRimuovere  = $gruppo->reject(fn ($s, $corsoId) => in_array((int) $corsoId, $selezionati, true));
        $daCreare     = array_values(array_diff($selezionati, $gruppo->keys()->map(fn ($c) => (int) $c)->all()));

        // Distanza minima di un mese dalle altre sessioni di ciascun corso (il gruppo stesso è escluso)
        $errori = SessioneRules::conflittiDistanza($selezionati, $validated['data_inizio'], $validated['data_fine'], $gruppo->pluck('sessione_id')->all());

        // Vincoli sugli appelli già inseriti (corsi rimossi, nuove date, numero di appelli)
        $errori = array_merge($errori, SessioneRules::vincoliModifica($daAggiornare, $daRimuovere, $validated));

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

        if ($sessione->isConclusa()) {
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
            // Facoltativa: se vuota, un mese prima dell'inizio della sessione
            'scadenza_inserimento' => !empty($validated['scadenza_inserimento'])
                ? $validated['scadenza_inserimento']
                : Sessione::scadenzaPredefinita($validated['data_inizio'])->format('Y-m-d'),
        ];
    }
}