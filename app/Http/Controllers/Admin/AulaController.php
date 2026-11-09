<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aula;
use App\Models\Edificio;
use Illuminate\Http\Request;

class AulaController extends Controller
{
    private const REGOLE = [
        'nome'     => 'required|string|max:100',
        'capienza' => 'required|integer|min:1',
    ];

    public function index($edificio_id)
    {
        $edificio = Edificio::findOrFail($edificio_id);
        $aule = Aula::where('edificio_id', $edificio_id)->orderBy('nome', 'asc')->get();

        return view('admin.sedi.aule', compact('edificio', 'aule'));
    }

    public function create($edificio_id)
    {
        $edificio = Edificio::findOrFail($edificio_id);

        return view('admin.sedi.createAula', compact('edificio'));
    }

    public function store(Request $request, $edificio_id)
    {
        $edificio = Edificio::findOrFail($edificio_id);

        Aula::create($request->validate(self::REGOLE) + ['edificio_id' => $edificio->edificio_id]);

        return redirect()->route('admin.sedi.aule', $edificio_id)->with('success', 'Aula creata con successo!');
    }

    public function edit($id)
    {
        $aula = Aula::with('edificio')->findOrFail($id);

        return view('admin.sedi.editAula', compact('aula'));
    }

    public function update(Request $request, $id)
    {
        $aula = Aula::findOrFail($id);
        $aula->update($request->validate(self::REGOLE));

        return redirect()->route('admin.sedi.aule', $aula->edificio_id)->with('success', 'Aula aggiornata con successo!');
    }

    public function destroy($id)
    {
        $aula = Aula::findOrFail($id);

        // La FK appello.aula_id non è in cascade: con appelli collegati il DELETE darebbe errore 500.
        if ($aula->appelli()->exists()) {
            return back()->with('error', "Impossibile eliminare l'aula '{$aula->nome}': ha appelli programmati.");
        }

        $aula->delete();

        return redirect()->route('admin.sedi.aule', $aula->edificio_id)->with('success', 'Aula eliminata con successo!');
    }
}
