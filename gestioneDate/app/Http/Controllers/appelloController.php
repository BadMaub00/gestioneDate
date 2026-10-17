<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appello;
use Carbon\Carbon;

class appelloController extends Controller
{
    public function create()
    {
        return view('appelli.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ins_corso_id' => 'required|exists:insegnamento_corso,ins_corso_id',
            'sessione_id'  => 'required|exists:sessione_esami,sessione_id',
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

        Appello::create([
            'ins_corso_id' => $request->ins_corso_id,
            'sessione_id'  => $request->sessione_id,
            'data'         => $request->data,
            'ora_inizio'   => $request->ora_inizio,
            'ora_fine'     => $request->ora_fine,
        ]);

        return redirect()->back()->with('success', 'Appello creato con successo.');
    }

    public function edit($id)
    {
        $appello = Appello::findOrFail($id);
        return view('appelli.edit', compact('appello'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'data' => [
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

        $appello = Appello::findOrFail($id);
        $appello->update([
            'data'       => $request->data,
            'ora_inizio' => $request->ora_inizio,
            'ora_fine'   => $request->ora_fine,
        ]);

        return redirect()->back()->with('success', 'Appello aggiornato con successo.');
    }

    public function destroy($id)
    {
        $appello = Appello::findOrFail($id);
        $appello->delete();

        return redirect()->back()->with('success', 'Appello eliminato con successo.');
    }
}