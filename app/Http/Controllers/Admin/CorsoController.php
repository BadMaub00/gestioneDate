<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Corso;
use Illuminate\Http\Request;

/** Anagrafica dei corsi di laurea. Il piano di studi è in PianoDiStudiController. */
class CorsoController extends Controller
{
    private const REGOLE = [
        'nome'       => 'required|string|max:150',
        'tipo_corso' => 'required|in:Triennale,Magistrale,Magistrale a ciclo unico',
    ];

    public function index()
    {
        $corsi = Corso::orderBy('nome', 'asc')->get();

        return view('admin.corsi.index', compact('corsi'));
    }

    public function create()
    {
        return view('admin.corsi.create');
    }

    public function store(Request $request)
    {
        Corso::create($request->validate(self::REGOLE));

        return redirect()->route('admin.corsi.index')->with('success', 'Corso di laurea creato con successo!');
    }

    public function edit($corso_id)
    {
        $corso = Corso::findOrFail($corso_id);

        return view('admin.corsi.edit', compact('corso'));
    }

    public function update(Request $request, $corso_id)
    {
        $corso = Corso::findOrFail($corso_id);
        $corso->update($request->validate(self::REGOLE));

        return redirect()->route('admin.corsi.index')->with('success', 'Corso di laurea aggiornato con successo!');
    }

    public function destroy($corso_id)
    {
        Corso::findOrFail($corso_id)->delete();

        return redirect()->route('admin.corsi.index')->with('success', 'Corso di laurea eliminato con successo!');
    }
}
