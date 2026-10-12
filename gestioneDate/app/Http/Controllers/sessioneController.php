<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sessione;

class sessioneController extends Controller
{
    private function validationData(){
        return [
            'nome' => 'required|string|max:255',
            'anno' => 'required|integer|min:2024|max:2099',
            'data_inizio' => 'required|date',
            'data_fine' => 'required|date|after_or_equal:data_inizio',
        ];
    }
    public function index(){
        // Recupera tutte le sessioni ordinate per data
        $sessioni = Sessione::orderBy('data_inizio', 'desc')->get();
        return view('admin.sessioni.index', compact('sessioni'));
    }

    public function create(){
        return view('admin.sessioni.create');
    }

    public function store(Request $request)
    {
        // 1. Validazione precisa e completa dei dati
        $validatedData = $request->validate($this->validationData());

        // 2. Creazione sicura utilizzando solo i dati validati
        Sessione::create($validatedData);

        // 3. Reindirizzamento alla lista con messaggio Flash
        return redirect()->route('sessioni.index')->with('success', 'Sessione creata con successo.');
    }

    public function edit($id)
    {
        $sessione = Sessione::findOrFail($id);
        return view('admin.sessioni.edit', compact('sessione'));
    }

    public function update(Request $request, $id){
        $sessione = Sessione::findOrFail($id);
        // 1. Validazione dei dati
        $validatedData = $request->validate($this->validationData());
        // 2. Aggiornamento della sessione
        $sessione->update($validatedData);
        // 3. Reindirizzamento con messaggio di successo
        return redirect()->route('sessioni.index')->with('success', 'Sessione aggiornata con successo.');
    }

    public function destroy($id)
    {
        $sessione = Sessione::findOrFail($id);
        $sessione->delete();

        return redirect()->route('sessioni.index')
            ->with('success', 'Sessione eliminata con successo.');
    }
}
