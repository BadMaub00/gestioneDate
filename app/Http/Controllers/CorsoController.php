<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Corso;
use App\Models\Insegnamento;
use App\Models\InsegnamentiCorso;

class CorsoController extends Controller
{
    public function index() {
        $corsi = Corso::orderBy('nome', 'asc')->get();
        return view('admin.corsi.index', compact('corsi'));
    }

    public function create() {
        return view('admin.corsi.create');
    }

    public function store(Request $request) {
        Corso::create($request->validate([
            'nome'       => 'required|string|max:150',
            'tipo_corso' => 'required|in:Triennale,Magistrale,Magistrale a ciclo unico',
        ]));
        return redirect()->route('admin.corsi.index')->with('success', 'Corso di laurea creato con successo!');
    }

    public function edit($corso_id) {
        $corso = Corso::findOrFail($corso_id);
        return view('admin.corsi.edit', compact('corso'));
    }

    public function update(Request $request, $corso_id) {
        $corso = Corso::findOrFail($corso_id);
        $corso->update($request->validate([
            'nome'       => 'required|string|max:150',
            'tipo_corso' => 'required|in:Triennale,Magistrale,Magistrale a ciclo unico',
        ]));
        return redirect()->route('admin.corsi.index')->with('success', 'Corso di laurea aggiornato con successo!');
    }
    public function destroy($corso_id) {
        $corso = Corso::findOrFail($corso_id);
        $corso->delete();
        
        return redirect()->route('admin.corsi.index')->with('success', 'Corso di laurea eliminato con successo!');
    }

    public function viewPianoDiStudi($corso_id) {
        $corso = Corso::with(['insegnamenti' => fn($q) => $q->orderBy('anno_di_corso', 'asc')])->findOrFail($corso_id);
        $insegnamentiPerAnno = $corso->insegnamenti->groupBy('anno_di_corso');
        $durataAnni = $this->getDurataAnni($corso->tipo_corso);
        return view('admin.corsi.piano_di_studi', compact('corso', 'insegnamentiPerAnno', 'durataAnni'));
    }

    public function createInsegnamentoCorso($corso_id) {
        $corso = Corso::findOrFail($corso_id);
        $insegnamenti = Insegnamento::all();
        return view('admin.corsi.createInsCorso', compact('corso', 'insegnamenti'));
    }

    public function storeInsegnamentoCorso(Request $request, $corso_id) {
        $corso = Corso::findOrFail($corso_id);
        $durataAnni = $this->getDurataAnni($corso->tipo_corso);

        $data = $request->validate([
            'insegnamento_id' => 'required|exists:insegnamento,insegnamento_id',
            'anno_di_corso'   => "required|integer|min:1|max:{$durataAnni}",
            'cfu'             => 'required|integer|min:1|max:30',
            'durata'          => 'required|integer|min:1',
        ]);

        if (InsegnamentiCorso::where('corso_id', $corso_id)->where('insegnamento_id', $data['insegnamento_id'])->exists()) {
            return back()->withErrors(['insegnamento_id' => 'Questo insegnamento fa già parte del piano di studi!'])->withInput();
        }

        $data['corso_id'] = $corso_id;
        InsegnamentiCorso::create($data);
        return redirect()->route('admin.corsi.piano_di_studi', $corso_id)->with('success', 'Insegnamento aggiunto!');
    }

    public function editInsegnamentoCorso($id) {
        $insegnamentoCorso = InsegnamentiCorso::with(['insegnamento', 'corso'])->findOrFail($id);
        $durataAnni = $this->getDurataAnni($insegnamentoCorso->corso->tipo_corso);
        $insegnamenti = Insegnamento::orderBy('nome', 'asc')->get();
        return view('admin.corsi.editInsCorso', ['insegnamentoCorso' => $insegnamentoCorso, 'corso' => $insegnamentoCorso->corso, 'durataAnni' => $durataAnni, 'insegnamenti' => $insegnamenti]);
    }

    public function updateInsegnamentoCorso(Request $request, $id) {
        $insegnamentoCorso = InsegnamentiCorso::findOrFail($id);
        $durataAnni = $this->getDurataAnni(Corso::findOrFail($insegnamentoCorso->corso_id)->tipo_corso);

        $insegnamentoCorso->update($request->validate([
            'anno_di_corso' => "required|integer|min:1|max:{$durataAnni}",
            'cfu'           => 'required|integer|min:1|max:30',
            'durata'        => 'required|integer|min:1',
        ]));
        return redirect()->route('admin.corsi.piano_di_studi', $insegnamentoCorso->corso_id)->with('success', 'Piano di studi aggiornato!');
    }

    private function getDurataAnni(string $tipoCorso): int {
        return match ($tipoCorso) { 'Magistrale' => 2, 'Magistrale a ciclo unico' => 5, default => 3 };
    }
}