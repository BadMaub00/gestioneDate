<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appello;
use App\Models\Aula;
use Carbon\Carbon;

class appelloController extends Controller
{
    public function create()
    {
        $aule = Aula::with('edificio')->orderBy('nome', 'asc')->get();
        return view('appelli.create', compact('aule'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ins_corso_id' => 'required|exists:insegnamento_corso,ins_corso_id',
            'sessione_id'  => 'required|exists:sessione_esami,sessione_id',
            'aula_id'      => 'required|exists:aula,aula_id',
            'data'         => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    if (Carbon::parse($value)->isWeekend()) {
                        $fail('Non è possibile inserire un appello durante il fine settimana (sabato o domenica).');
                    }
                },
            ],
            'ora_inizio'   => 'required|date_format:H:i',
            'ora_fine'     => 'required|date_format:H:i|after:ora_inizio',
        ]);

        // Controllo sovrapposizione dell'aula
        $aulaOccupata = Appello::where('aula_id', $validated['aula_id'])
            ->where('data', $validated['data'])
            ->where('ora_inizio', '<', $validated['ora_fine'])
            ->where('ora_fine', '>', $validated['ora_inizio'])
            ->exists();

        if ($aulaOccupata) {
            return back()
                ->withErrors(['aula_id' => 'L\'aula selezionata è già occupata per questa data e fascia oraria.'])
                ->withInput();
        }

        Appello::create($validated);

        return redirect()->back()->with('success', 'Appello creato con successo.');
    }

    public function edit($id)
    {
        $appello = Appello::findOrFail($id);
        $aule = Aula::with('edificio')->orderBy('nome', 'asc')->get();
        return view('appelli.edit', compact('appello', 'aule'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'aula_id' => 'required|exists:aula,aula_id',
            'data'    => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    if (Carbon::parse($value)->isWeekend()) {
                        $fail('Non è possibile posizionare un appello di sabato o domenica.');
                    }
                },
            ],
            'ora_inizio' => 'required|date_format:H:i',
            'ora_fine'   => 'required|date_format:H:i|after:ora_inizio',
        ]);

        // Controllo sovrapposizione dell'aula escludendo l'appello corrente
        $aulaOccupata = Appello::where('aula_id', $validated['aula_id'])
            ->where('appello_id', '!=', $id)
            ->where('data', $validated['data'])
            ->where('ora_inizio', '<', $validated['ora_fine'])
            ->where('ora_fine', '>', $validated['ora_inizio'])
            ->exists();

        if ($aulaOccupata) {
            return back()
                ->withErrors(['aula_id' => 'L\'aula selezionata è già occupata per questa data e fascia oraria.'])
                ->withInput();
        }

        $appello = Appello::findOrFail($id);
        $appello->update($validated);

        return redirect()->back()->with('success', 'Appello aggiornato con successo.');
    }

    public function destroy($id)
    {
        $appello = Appello::findOrFail($id);
        $appello->delete();

        return redirect()->back()->with('success', 'Appello eliminato con successo.');
    }
}