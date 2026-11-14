<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Corso;
use App\Models\Insegnamento;
use App\Models\InsegnamentiCorso;
use Illuminate\Http\Request;

/** Piano di studi di un corso: insegnamenti (con anno, CFU e durata) che ne fanno parte. */
class PianoDiStudiController extends Controller
{
    public function show($corso_id)
    {
        $corso = Corso::with(['insegnamenti' => fn ($q) => $q->orderBy('anno_di_corso', 'asc')])->findOrFail($corso_id);
        $insegnamentiPerAnno = $corso->insegnamenti->groupBy('anno_di_corso');
        $durataAnni = $corso->durataAnni();

        return view('admin.corsi.piano_di_studi', compact('corso', 'insegnamentiPerAnno', 'durataAnni'));
    }

    public function create($corso_id)
    {
        $corso = Corso::findOrFail($corso_id);
        $insegnamenti = Insegnamento::all();

        return view('admin.corsi.createInsCorso', compact('corso', 'insegnamenti'));
    }

    public function store(Request $request, $corso_id)
    {
        $corso = Corso::findOrFail($corso_id);
        $durataAnni = $corso->durataAnni();

        $data = $request->validate([
            'insegnamento_id' => 'required|exists:insegnamento,insegnamento_id',
            'anno_di_corso'   => "required|integer|min:1|max:{$durataAnni}",
            'cfu'             => 'required|integer|min:1|max:30',
            'durata'          => 'required|integer|min:1',
        ]);

        if (InsegnamentiCorso::giaNelPiano((int) $corso_id, (int) $data['insegnamento_id'])) {
            return back()->withErrors(['insegnamento_id' => 'Questo insegnamento fa già parte del piano di studi!'])->withInput();
        }

        $data['corso_id'] = $corso_id;
        InsegnamentiCorso::create($data);

        return redirect()->route('admin.corsi.piano_di_studi', $corso_id)->with('success', 'Insegnamento aggiunto!');
    }

    public function edit($id)
    {
        $insegnamentoCorso = InsegnamentiCorso::with(['insegnamento', 'corso'])->findOrFail($id);
        $durataAnni = $insegnamentoCorso->corso->durataAnni();
        $insegnamenti = Insegnamento::orderBy('nome', 'asc')->get();

        return view('admin.corsi.editInsCorso', [
            'insegnamentoCorso' => $insegnamentoCorso,
            'corso'             => $insegnamentoCorso->corso,
            'durataAnni'        => $durataAnni,
            'insegnamenti'      => $insegnamenti,
        ]);
    }

    public function update(Request $request, $id)
    {
        $insegnamentoCorso = InsegnamentiCorso::with('corso')->findOrFail($id);
        $durataAnni = $insegnamentoCorso->corso->durataAnni();

        $insegnamentoCorso->update($request->validate([
            'anno_di_corso' => "required|integer|min:1|max:{$durataAnni}",
            'cfu'           => 'required|integer|min:1|max:30',
            'durata'        => 'required|integer|min:1',
        ]));

        return redirect()->route('admin.corsi.piano_di_studi', $insegnamentoCorso->corso_id)->with('success', 'Piano di studi aggiornato!');
    }

    public function destroy($id)
    {
        $insegnamentoCorso = InsegnamentiCorso::with('insegnamento')->findOrFail($id);
        $corso_id = $insegnamentoCorso->corso_id;
        $nome = $insegnamentoCorso->insegnamento->nome ?? 'Insegnamento';

        // Blocco se esistono appelli in sessioni già concluse (si perderebbe lo storico)
        if ($insegnamentoCorso->haAppelliInSessioniConcluse()) {
            return back()->with('error', "Impossibile rimuovere '{$nome}' dal piano di studi: esistono appelli in sessioni già concluse.");
        }

        // Le FK sono ON DELETE CASCADE: vengono eliminati anche gli appelli futuri
        // e le associazioni docente collegate a questo insegnamento del corso.
        $insegnamentoCorso->delete();

        return redirect()->route('admin.corsi.piano_di_studi', ['corso_id' => $corso_id])
            ->with('success', "'{$nome}' rimosso dal piano di studi.");
    }
}
