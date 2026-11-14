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
        $associazioniGrouped = DocenteInsegnamento::elencoPerCorso($request->only(['corso_id', 'ins_corso_id', 'docente_id']));

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
            'ruolo'        => 'required|in:' . implode(',', DocenteInsegnamento::RUOLI),
        ]);

        $errore = DocenteInsegnamento::erroreAssociazione(
            (int) $validated['ins_corso_id'],
            Utente::findOrFail($validated['utente_id']),
            $validated['ruolo']
        );

        if ($errore) {
            return back()->withErrors($errore)->withInput();
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