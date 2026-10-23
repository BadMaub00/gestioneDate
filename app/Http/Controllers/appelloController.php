<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appello;
use App\Models\Sessione;
use App\Models\Aula;
use App\Models\Edificio;
use App\Models\InsegnamentiCorso;
use App\Models\DocenteInsegnamento;
use App\Services\AppelloRules;
use Carbon\Carbon;

class appelloController extends Controller
{
    /**
     * Il docente può operare solo sugli insegnamenti a cui è associato
     * (l'amministratore non ha questo vincolo).
     */
    private function autorizza(int $insCorsoId): void
    {
        /** @var Utente $utente */
        $utente = Auth::user();

        if ($utente->ruolo === 'Amministratore') {
            return;
        }

        $associato = DocenteInsegnamento::where('utente_id', $utente->getKey())
            ->where('ins_corso_id', $insCorsoId)
            ->where('ruolo', 'Primario')
            ->exists();

        abort_unless($associato, 403, 'Non sei associato a questo insegnamento.');
    }

    public function create(Request $request)
    {
        $request->validate([
            'ins_corso_id' => 'required|exists:insegnamento_corso,ins_corso_id',
            'sessione_id'  => 'required|exists:sessione_esami,sessione_id',
        ]);

        $this->autorizza((int) $request->ins_corso_id);

        $sessione = Sessione::findOrFail($request->sessione_id);
        $insegnamentoCorso = InsegnamentiCorso::with(['insegnamento', 'corso'])->findOrFail($request->ins_corso_id);

        if ((int) $sessione->corso_id !== (int) $insegnamentoCorso->corso_id) {
            return back()->with('error', 'Questa sessione non appartiene al corso dell\'insegnamento.');
        }

        if (Carbon::parse($sessione->data_fine)->lt(Carbon::today())) {
            return back()->with('error', 'Impossibile creare un appello per una sessione già conclusa.');
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

        $this->autorizza((int) $request->ins_corso_id);

        $sessione = Sessione::findOrFail($request->sessione_id);
        $insegnamentoCorso = InsegnamentiCorso::findOrFail($request->ins_corso_id);

        if ((int) $sessione->corso_id !== (int) $insegnamentoCorso->corso_id) {
            return back()->withErrors(['sessione_id' => 'La sessione selezionata non appartiene al corso di questo insegnamento.'])->withInput();
        }

        if (Carbon::parse($sessione->data_fine)->lt(Carbon::today())) {
            return back()->with('error', 'Impossibile creare un appello per una sessione già conclusa.');
        }

        $appelliEsistenti = Appello::where('sessione_id', $sessione->sessione_id)
            ->where('ins_corso_id', $request->ins_corso_id)
            ->count();

        if ($appelliEsistenti >= $sessione->numero_appelli) {
            return back()->withErrors([
                'limite_appelli' => "La sessione '{$sessione->nome}' consente al massimo {$sessione->numero_appelli} appelli per questo insegnamento. Limite raggiunto."
            ])->withInput();
        }

        $validated = $request->validate(
            AppelloRules::regole($sessione) + [
                'sessione_id'  => 'required|exists:sessione_esami,sessione_id',
                'ins_corso_id' => 'required|exists:insegnamento_corso,ins_corso_id',
                'aula_id'      => 'required|exists:aula,aula_id',
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

    public function edit($id)
    {
        $appello = Appello::with([
            'sessione',
            'insegnamentoCorso.insegnamento',
            'insegnamentoCorso.corso',
            'aula.edificio'
        ])->findOrFail($id);

        $this->autorizza((int) $appello->ins_corso_id);

        if (Carbon::parse($appello->sessione->data_fine)->lt(Carbon::today())) {
            return back()->with('error', 'Impossibile modificare un appello appartenente a una sessione passata.');
        }

        $sessione = $appello->sessione;
        $edifici = Edificio::with('aule')->orderBy('nome', 'asc')->get();
        $aule = Aula::orderBy('nome', 'asc')->get();

        return view('docente.appelli.edit', compact('appello', 'sessione', 'edifici', 'aule'));
    }

    public function update(Request $request, $id)
    {
        $appello = Appello::with('sessione')->findOrFail($id);

        $this->autorizza((int) $appello->ins_corso_id);

        $sessione = $appello->sessione;

        if (Carbon::parse($sessione->data_fine)->lt(Carbon::today())) {
            return back()->with('error', 'Impossibile modificare un appello appartenente a una sessione passata.');
        }

        $validated = $request->validate(
            AppelloRules::regole($sessione) + [
                'aula_id' => 'required|exists:aula,aula_id',
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

    public function destroy($id)
    {
        $appello = Appello::with('sessione')->findOrFail($id);

        $this->autorizza((int) $appello->ins_corso_id);

        if (Carbon::parse($appello->sessione->data_fine)->lt(Carbon::today())) {
            return back()->with('error', 'Impossibile eliminare un appello appartenente a una sessione passata.');
        }

        $appello->delete();

        return back()->with('success', 'Appello eliminato con successo.');
    }
}
