<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sessione;

class sessioneController extends Controller
{
    private function validationData(){
        return [
            'nome'           => 'required|string|max:255',
            'anno'           => 'required|integer|min:2024|max:2099',
            'data_inizio'    => 'required|date',
            'data_fine'      => 'required|date|after_or_equal:data_inizio',
            'numero_appelli' => 'required|integer|min:1|max:4',
        ];
    }

    public function index(){
        $sessioni = Sessione::orderBy('data_inizio', 'desc')->get();
        return view('admin.sessioni.index', compact('sessioni'));
    }

    public function create(){
        return view('admin.sessioni.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate($this->validationData());

        Sessione::create($validatedData);

        return redirect()->route('sessioni.index')->with('success', 'Sessione creata con successo.');
    }

    public function edit($id)
    {
        $sessione = Sessione::findOrFail($id);
        return view('admin.sessioni.edit', compact('sessione'));
    }

    public function update(Request $request, $id){
        $sessione = Sessione::findOrFail($id);
        
        $validatedData = $request->validate($this->validationData());

        $sessione->update($validatedData);

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