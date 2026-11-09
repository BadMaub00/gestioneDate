<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appello;
use App\Models\Aula;
use App\Models\Edificio;
use App\Services\AppelloRules;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Modifica/eliminazione appelli da parte dell'amministratore (non ha il limite
 * della scadenza di inserimento, ma non può toccare sessioni già concluse).
 * La creazione di un appello resta riservata al docente Primario.
 */
class AppelloAdminController extends Controller
{
    public function edit($id)
    {
        $appello = Appello::with(['sessione', 'insegnamentoCorso.insegnamento', 'insegnamentoCorso.corso', 'aula.edificio'])->findOrFail($id);

        if ($errore = AppelloRules::bloccoAdmin($appello->sessione, 'modificare')) {
            return back()->with('error', $errore);
        }

        $edifici = Edificio::with('aule')->orderBy('nome', 'asc')->get();
        $aule = Aula::orderBy('nome', 'asc')->get();

        return view('admin.appelli.edit', compact('appello', 'edifici', 'aule'));
    }

    public function update(Request $request, $id)
    {
        $appello = Appello::with(['insegnamentoCorso', 'sessione'])->findOrFail($id);
        $sessione = $appello->sessione;

        if ($errore = AppelloRules::bloccoAdmin($sessione, 'modificare')) {
            return back()->with('error', $errore);
        }

        $validated = $request->validate(
            AppelloRules::regole($sessione) + [
                'edificio_id' => 'required|exists:edificio,edificio_id',
                // l'aula deve appartenere all'edificio scelto
                'aula_id'     => ['required', Rule::exists('aula', 'aula_id')->where('edificio_id', $request->input('edificio_id'))],
                'note'        => 'nullable|string|max:500',
            ],
            AppelloRules::messaggi($sessione) + [
                'aula_id.exists' => "L'aula selezionata non appartiene all'edificio scelto.",
            ]
        );

        if ($errore = AppelloRules::conflitto((int) $appello->ins_corso_id, $validated, (int) $id)) {
            return back()->withErrors($errore)->withInput();
        }

        // Le note si aggiornano solo se il campo è stato inviato (il form lo invia sempre; vuoto = cancella).
        $appello->update(\Illuminate\Support\Arr::only($validated, ['data', 'ora_inizio', 'ora_fine', 'aula_id', 'note']));

        return $this->tornaAlCalendario($appello)->with('success', 'Appello modificato con successo.');
    }

    public function destroy($id)
    {
        $appello = Appello::with(['insegnamentoCorso', 'sessione'])->findOrFail($id);

        if ($errore = AppelloRules::bloccoAdmin($appello->sessione, 'eliminare')) {
            return back()->with('error', $errore);
        }

        $appello->delete();

        return $this->tornaAlCalendario($appello)->with('success', 'Appello eliminato con successo.');
    }

    private function tornaAlCalendario(Appello $appello)
    {
        return redirect()->route('admin.calendario', [
            'sessione_id' => $appello->sessione_id,
            'corso_id'    => $appello->insegnamentoCorso->corso_id ?? null,
        ]);
    }
}
