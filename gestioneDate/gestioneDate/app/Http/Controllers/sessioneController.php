<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sessione;
use Carbon\Carbon;

class sessioneController extends Controller
{
    public function index()
    {
        $sessioni = Sessione::orderBy('data_inizio', 'asc')->get();
        return view('admin.sessioni.index', compact('sessioni'));
    }

    public function create()
    {
        return view('admin.sessioni.create');
    }

    public function store(Request $request)
    {
        // Data minima consentita per l'inizio: almeno 1 mese da oggi
        $minDataInizio = Carbon::today()->addMonth()->format('Y-m-d');

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'anno' => 'required|integer|min:2020|max:2099',
            'numero_appelli' => 'required|integer|min:1|max:4', // Min 1, Max 4
            'data_inizio' => ['required', 'date', 'after_or_equal:' . $minDataInizio],
            'data_fine' => ['required', 'date', function ($attribute, $value, $fail) use ($request) {
                if ($request->filled('data_inizio')) {
                    $inizio = Carbon::parse($request->data_inizio);
                    $fine = Carbon::parse($value);

                    // Verifica che la data di fine sia almeno 2 settimane (14 giorni) dopo la data d'inizio
                    if ($fine->lt($inizio->copy()->addWeeks(2))) {
                        $fail('La data di fine deve essere di almeno due settimane successiva alla data di inizio.');
                    }
                }
            }],
        ], [
            'numero_appelli.min' => 'Il numero minimo di appelli previsti è 1.',
            'numero_appelli.max' => 'Il numero massimo di appelli previsti per una sessione è 4.',
            'data_inizio.after_or_equal' => 'La data di inizio della sessione deve essere programmata con almeno un mese di anticipo rispetto a oggi.',
        ]);

        Sessione::create($validated);

        return redirect()->route('admin.sessioni.index')->with('success', 'Sessione d\'esame creata con successo!');
    }

    public function edit($id)
    {
        $sessione = Sessione::findOrFail($id);

        if (Carbon::parse($sessione->data_fine)->lt(Carbon::today())) {
            return back()->with('error', 'Impossibile modificare una sessione d\'esame già passata.');
        }

        return view('admin.sessioni.edit', compact('sessione'));
    }

    public function update(Request $request, $id)
    {
        $sessione = Sessione::findOrFail($id);

        if (Carbon::parse($sessione->data_fine)->lt(Carbon::today())) {
            return back()->with('error', 'Impossibile modificare una sessione d\'esame già passata.');
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'anno' => 'required|integer|min:2020|max:2099',
            'numero_appelli' => 'required|integer|min:1|max:4', // Min 1, Max 4
            'data_inizio' => 'required|date',
            'data_fine' => ['required', 'date', function ($attribute, $value, $fail) use ($request) {
                if ($request->filled('data_inizio')) {
                    $inizio = Carbon::parse($request->data_inizio);
                    $fine = Carbon::parse($value);

                    if ($fine->lt($inizio->copy()->addWeeks(2))) {
                        $fail('La data di fine deve essere di almeno due settimane successiva alla data di inizio.');
                    }
                }
            }],
        ], [
            'numero_appelli.min' => 'Il numero minimo di appelli previsti è 1.',
            'numero_appelli.max' => 'Il numero massimo di appelli previsti per una sessione è 4.',
        ]);

        $sessione->update($validated);

        return redirect()->route('admin.sessioni.index')->with('success', 'Sessione d\'esame aggiornata con successo!');
    }

    public function destroy($id)
    {
        $sessione = Sessione::findOrFail($id);
        $sessione->delete();

        return redirect()->route('admin.sessioni.index')->with('success', 'Sessione eliminata con successo!');
    }
}