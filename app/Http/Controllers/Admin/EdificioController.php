<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Edificio;
use Illuminate\Http\Request;

class EdificioController extends Controller
{
    public function index()
    {
        $edifici = Edificio::orderBy('nome', 'asc')->get();

        return view('admin.sedi.edifici', compact('edifici'));
    }

    public function create()
    {
        return view('admin.sedi.createEdificio');
    }

    public function store(Request $request)
    {
        Edificio::create($request->validate([
            'nome' => 'required|string|max:150|unique:edificio,nome',
            'via'  => 'required|string|max:255',
        ]));

        return redirect()->route('admin.sedi.edifici')->with('success', 'Edificio creato con successo!');
    }

    public function edit($id)
    {
        $edificio = Edificio::findOrFail($id);

        return view('admin.sedi.editEdificio', compact('edificio'));
    }

    public function update(Request $request, $id)
    {
        $edificio = Edificio::findOrFail($id);

        $edificio->update($request->validate([
            'nome' => 'required|string|max:150|unique:edificio,nome,' . $id . ',edificio_id',
            'via'  => 'required|string|max:255',
        ]));

        return redirect()->route('admin.sedi.edifici')->with('success', 'Edificio aggiornato con successo!');
    }

    public function destroy($id)
    {
        $edificio = Edificio::findOrFail($id);

        // La FK appello.aula_id non è in cascade: con appelli collegati il DELETE darebbe errore 500.
        if ($edificio->aule()->whereHas('appelli')->exists()) {
            return back()->with('error', "Impossibile eliminare '{$edificio->nome}': alcune sue aule hanno appelli programmati.");
        }

        $edificio->delete();

        return redirect()->route('admin.sedi.edifici')->with('success', 'Edificio eliminato con successo!');
    }
}
