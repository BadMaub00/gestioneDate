<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Insegnamento;
use Illuminate\Http\Request;

/** Anagrafica degli insegnamenti (non il piano di studi: quello è in CorsoController). */
class InsegnamentoController extends Controller
{
    public function index()
    {
        $insegnamenti = Insegnamento::orderBy('nome', 'asc')->get();

        return view('admin.insegnamenti.index', compact('insegnamenti'));
    }

    public function create()
    {
        return view('admin.insegnamenti.create');
    }

    public function store(Request $request)
    {
        Insegnamento::create($request->validate(['nome' => 'required|string|max:150|unique:insegnamento,nome']));

        return redirect()->route('admin.insegnamenti.index')->with('success', 'Insegnamento creato con successo!');
    }

    public function edit($insegnamento_id)
    {
        $insegnamento = Insegnamento::findOrFail($insegnamento_id);

        return view('admin.insegnamenti.edit', compact('insegnamento'));
    }

    public function update(Request $request, $insegnamento_id)
    {
        $insegnamento = Insegnamento::findOrFail($insegnamento_id);

        $insegnamento->update($request->validate([
            'nome' => 'required|string|max:150|unique:insegnamento,nome,' . $insegnamento_id . ',insegnamento_id',
        ]));

        return redirect()->route('admin.insegnamenti.index')->with('success', 'Insegnamento aggiornato con successo!');
    }

    public function destroy($insegnamento_id)
    {
        Insegnamento::findOrFail($insegnamento_id)->delete();

        return redirect()->back()->with('success', 'Insegnamento eliminato con successo.');
    }
}
