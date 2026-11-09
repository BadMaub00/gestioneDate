<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Utente;
use Illuminate\Http\Request;

class PersonaleController extends Controller
{
    public function index()
    {
        $personale = Utente::orderBy('cognome', 'asc')->orderBy('nome', 'asc')->get();

        return view('admin.personale.index', compact('personale'));
    }

    public function create()
    {
        return view('admin.personale.create');
    }

    public function store(Request $request)
    {
        // L'hash della password è fatto dal cast 'hashed' del modello Utente.
        Utente::create($request->validate([
            'username'     => 'required|string|max:100|unique:utente,username',
            'password'     => 'required|string|min:6',
            'nome'         => 'required|string|max:100',
            'cognome'      => 'required|string|max:100',
            'data_nascita' => 'required|date',
            'ruolo'        => 'required|in:' . Utente::RUOLO_DOCENTE . ',' . Utente::RUOLO_ADMIN,
        ]));

        return redirect()->route('admin.personale.index')->with('success', 'Membro del personale creato con successo!');
    }

    public function edit($id)
    {
        $utente = Utente::findOrFail($id);

        return view('admin.personale.edit', compact('utente'));
    }

    public function update(Request $request, $id)
    {
        $utente = Utente::findOrFail($id);

        $validated = $request->validate([
            'username'     => 'required|string|max:100|unique:utente,username,' . $id . ',utente_id',
            'nome'         => 'required|string|max:100',
            'cognome'      => 'required|string|max:100',
            'data_nascita' => 'required|date',
            'ruolo'        => 'required|in:' . Utente::RUOLO_DOCENTE . ',' . Utente::RUOLO_ADMIN,
            'password'     => 'nullable|string|min:6',
        ]);

        // Un admin non può togliersi da solo i privilegi (si rischia di restare senza amministratori).
        if ($utente->is($request->user()) && $validated['ruolo'] !== $utente->ruolo) {
            return back()->withErrors(['ruolo' => 'Non puoi modificare il tuo stesso ruolo.'])->withInput();
        }

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $utente->update($validated);

        return redirect()->route('admin.personale.index')->with('success', 'Membro del personale aggiornato con successo!');
    }

    public function destroy(Request $request, $id)
    {
        $utente = Utente::findOrFail($id);

        if ($utente->is($request->user())) {
            return back()->with('error', 'Non puoi eliminare il tuo stesso account.');
        }

        $utente->delete();

        return redirect()->route('admin.personale.index')->with('success', 'Membro del personale eliminato con successo!');
    }
}
