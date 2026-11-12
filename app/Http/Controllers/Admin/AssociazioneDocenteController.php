<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Corso;
use App\Models\DocenteInsegnamento;
use App\Models\InsegnamentiCorso;
use App\Models\Utente;
use Illuminate\Http\Request;

/** Associazioni docente <-> insegnamento del corso (solo amministratore). */
class AssociazioneDocenteController extends Controller
{
    public function index(Request $request)
    {
        $query = DocenteInsegnamento::with(['docente', 'insegnamentiCorso.insegnamento', 'insegnamentiCorso.corso']);

        if ($request->filled('corso_id')) {
            $query->whereHas('insegnamentiCorso', fn ($q) => $q->where('corso_id', $request->corso_id));
        }
        if ($request->filled('ins_corso_id')) {
            $query->where('ins_corso_id', $request->ins_corso_id);
        }
        if ($request->filled('docente_id')) {
            $query->where('utente_id', $request->docente_id);
        }

        // Ordinamento per corso, cognome, nome; raggruppamento per nome del corso
        $associazioniGrouped = $query->get()->sortBy([
            fn ($a, $b) => strcmp($a->insegnamentiCorso->corso->nome ?? '', $b->insegnamentiCorso->corso->nome ?? ''),
            fn ($a, $b) => strcmp($a->docente->cognome ?? '', $b->docente->cognome ?? ''),
            fn ($a, $b) => strcmp($a->docente->nome ?? '', $b->docente->nome ?? ''),
        ])
        ->groupBy(fn ($assoc) => $assoc->insegnamentiCorso->corso->nome ?? 'N/D')
        ->sortKeys();

        // Dati per i menu a tendina dei filtri
        $corsi = Corso::orderBy('nome', 'asc')->get();
        $insegnamentiCorso = InsegnamentiCorso::with('insegnamento')->get();
        $docenti = $this->docenti();

        return view('admin.personale.associazioni_index', compact('associazioniGrouped', 'corsi', 'insegnamentiCorso', 'docenti'));
    }

    public function create()
    {
        $corsi = Corso::orderBy('nome', 'asc')->get();
        $docenti = $this->docenti();
        $insegnamentiCorso = InsegnamentiCorso::with(['insegnamento', 'corso'])->orderBy('corso_id', 'asc')->get();

        return view('admin.personale.associa', compact('corsi', 'docenti', 'insegnamentiCorso'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ins_corso_id' => 'required|exists:insegnamento_corso,ins_corso_id',
            'utente_id'    => 'required|exists:utente,utente_id',
            'ruolo'        => 'required|in:Primario,Secondario',
        ]);

        // 1. Solo gli utenti con ruolo Docente possono essere associati
        if (!Utente::findOrFail($validated['utente_id'])->isDocente()) {
            return back()->withErrors(['utente_id' => 'Solo gli utenti con ruolo "Docente" possono essere associati ad un insegnamento!'])->withInput();
        }

        $perInsegnamento = DocenteInsegnamento::where('ins_corso_id', $validated['ins_corso_id']);

        // 2. Associazione duplicata
        if ((clone $perInsegnamento)->where('utente_id', $validated['utente_id'])->exists()) {
            return back()->withErrors(['utente_id' => 'Questo docente è già associato a questo insegnamento!'])->withInput();
        }

        // 3. Un solo Primario per insegnamento
        if ($validated['ruolo'] === 'Primario' && (clone $perInsegnamento)->where('ruolo', 'Primario')->exists()) {
            return back()->withErrors(['ruolo' => 'Questo insegnamento ha già un docente con ruolo Primario!'])->withInput();
        }

        DocenteInsegnamento::create($validated);

        return redirect()->route('admin.personale.associazioni_index')->with('success', 'Docente associato con successo!');
    }

    public function destroy($id)
    {
        DocenteInsegnamento::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Associazione rimossa con successo.');
    }

    private function docenti()
    {
        return Utente::where('ruolo', Utente::RUOLO_DOCENTE)->orderBy('cognome', 'asc')->orderBy('nome', 'asc')->get();
    }
}