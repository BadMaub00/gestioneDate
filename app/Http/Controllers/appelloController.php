<?php

namespace App\Http\Controllers;

use App\Models\Appello;
use App\Models\Aula;
use App\Models\DocenteInsegnamento;
use App\Models\Edificio;
use App\Models\InsegnamentiCorso;
use App\Models\Sessione;
use App\Services\AppelloRules;
use Illuminate\Http\Request;

/**
 * CRUD degli appelli lato DOCENTE (rotte protette da DocenteMiddleware).
 * L'amministratore non passa da qui: usa Admin\AppelloAdminController.
 */
class appelloController extends Controller
{
    /** Solo il docente Primario dell'insegnamento può operare sui suoi appelli. */
    private function autorizza(Request $request, int $insCorsoId): void
    {
        $autorizzato = DocenteInsegnamento::where('utente_id', $request->user()->getKey())
            ->where('ins_corso_id', $insCorsoId)
            ->where('ruolo', 'Primario')
            ->exists();

        abort_unless($autorizzato, 403, 'Non sei associato a questo insegnamento.');
    }

    public function create(Request $request)
    {
        $request->validate([
            'ins_corso_id' => 'required|exists:insegnamento_corso,ins_corso_id',
            'sessione_id'  => 'required|exists:sessione_esami,sessione_id',
        ]);

        $this->autorizza($request, (int) $request->ins_corso_id);

        $sessione = Sessione::findOrFail($request->sessione_id);
        $insegnamentoCorso = InsegnamentiCorso::with(['insegnamento', 'corso'])->findOrFail($request->ins_corso_id);

        if ((int) $sessione->corso_id !== (int) $insegnamentoCorso->corso_id) {
            return back()->with('error', 'Questa sessione non appartiene al corso dell\'insegnamento.');
        }

        if ($errore = AppelloRules::bloccoDocente($sessione, 'creare')) {
            return back()->with('error', $errore);
        }

        $edifici = Edificio::with('aule')->orderBy('nome', 'asc')->get();

        return view('docente.appelli.create', compact('sessione', 'insegnamentoCorso', 'edifici'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sessione_id'  => 'required|exists:sessione_esami,sessione_id',
            'ins_corso_id' => 'required|exists:insegnamento_corso,ins_corso_id',
        ]);

        $this->autorizza($request, (int) $request->ins_corso_id);

        $sessione = Sessione::findOrFail($request->sessione_id);
        $insegnamentoCorso = InsegnamentiCorso::findOrFail($request->ins_corso_id);

        if ((int) $sessione->corso_id !== (int) $insegnamentoCorso->corso_id) {
            return back()->withErrors(['sessione_id' => 'La sessione selezionata non appartiene al corso di questo insegnamento.'])->withInput();
        }

        if ($errore = AppelloRules::bloccoDocente($sessione, 'creare')) {
            return back()->with('error', $errore);
        }

        $appelliEsistenti = Appello::where('sessione_id', $sessione->sessione_id)
            ->where('ins_corso_id', $request->ins_corso_id)
            ->count();

        if ($appelliEsistenti >= $sessione->numero_appelli) {
            return back()->withErrors([
                'limite_appelli' => "La sessione '{$sessione->nome}' consente al massimo {$sessione->numero_appelli} appelli per questo insegnamento. Limite raggiunto.",
            ])->withInput();
        }

        // sessione_id e ins_corso_id sono già stati validati sopra: qui si aggiungono solo i campi dell'appello,
        // ma devono comparire in $validated per il create().
        $validated = $request->validate(
            AppelloRules::regole($sessione) + [
                'sessione_id'  => 'required',
                'ins_corso_id' => 'required',
                'aula_id'      => 'required|exists:aula,aula_id',
                'note'         => 'nullable|string|max:500',
            ],
            AppelloRules::messaggi($sessione)
        );

        if ($errore = AppelloRules::conflitto((int) $validated['ins_corso_id'], $validated)) {
            return back()->withErrors($errore)->withInput();
        }

        Appello::create($validated);

        return redirect()->route('docente.appelliInsegnamento', $request->ins_corso_id)
            ->with('success', 'Appello creato con successo!');
    }

    public function edit(Request $request, $id)
    {
        $appello = Appello::with(['sessione', 'insegnamentoCorso.insegnamento', 'insegnamentoCorso.corso', 'aula.edificio'])->findOrFail($id);

        $this->autorizza($request, (int) $appello->ins_corso_id);
        $sessione = $appello->sessione;

        if ($errore = AppelloRules::bloccoDocente($sessione, 'modificare')) {
            return back()->with('error', $errore);
        }

        $edifici = Edificio::with('aule')->orderBy('nome', 'asc')->get();
        $aule = Aula::orderBy('nome', 'asc')->get();

        return view('docente.appelli.edit', compact('appello', 'sessione', 'edifici', 'aule'));
    }

    public function update(Request $request, $id)
    {
        $appello = Appello::with('sessione')->findOrFail($id);

        $this->autorizza($request, (int) $appello->ins_corso_id);
        $sessione = $appello->sessione;

        if ($errore = AppelloRules::bloccoDocente($sessione, 'modificare')) {
            return back()->with('error', $errore);
        }

        $validated = $request->validate(
            AppelloRules::regole($sessione) + [
                'aula_id' => 'required|exists:aula,aula_id',
                'note'    => 'nullable|string|max:500',
            ],
            AppelloRules::messaggi($sessione)
        );

        if ($errore = AppelloRules::conflitto((int) $appello->ins_corso_id, $validated, (int) $appello->appello_id)) {
            return back()->withErrors($errore)->withInput();
        }

        $appello->update($validated);

        return redirect()->route('docente.appelliInsegnamento', $appello->ins_corso_id)
            ->with('success', 'Appello aggiornato con successo!');
    }

    public function destroy(Request $request, $id)
    {
        $appello = Appello::with('sessione')->findOrFail($id);

        $this->autorizza($request, (int) $appello->ins_corso_id);

        if ($errore = AppelloRules::bloccoDocente($appello->sessione, 'eliminare')) {
            return back()->with('error', $errore);
        }

        $appello->delete();

        return back()->with('success', 'Appello eliminato con successo.');
    }
}
