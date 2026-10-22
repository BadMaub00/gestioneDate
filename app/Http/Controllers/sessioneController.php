<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sessione;
use App\Models\Appello;
use Carbon\Carbon;

class sessioneController extends Controller
{
    public function index()
    {
        $sessioni = Sessione::orderBy('data_inizio', 'desc')->get();
        return view('admin.sessioni.index', compact('sessioni'));
    }

    public function create()
    {
        return view('admin.sessioni.create');
    }

    public function store(Request $request)
    {
        $this->derivaAnno($request);

        $minDataInizio = Carbon::today()->addMonth()->format('Y-m-d');

        $validated = $request->validate(
            $this->regole($request) + [
                'data_inizio' => ['required', 'date', 'after_or_equal:' . $minDataInizio],
            ],
            $this->messaggi() + [
                'data_inizio.after_or_equal' => 'La data di inizio della sessione deve essere programmata con almeno un mese di anticipo rispetto a oggi.',
            ]
        );

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

        $this->derivaAnno($request);

        $validated = $request->validate(
            $this->regole($request, (int) $id) + ['data_inizio' => 'required|date'],
            $this->messaggi()
        );

        // Non lasciare appelli già inseriti fuori dal nuovo periodo
        $fuoriPeriodo = Appello::where('sessione_id', $sessione->sessione_id)
            ->where(function ($q) use ($validated) {
                $q->where('data', '<', $validated['data_inizio'])
                  ->orWhere('data', '>', $validated['data_fine']);
            })
            ->count();

        if ($fuoriPeriodo > 0) {
            return back()->withErrors([
                'data_inizio' => "Ci sono {$fuoriPeriodo} appelli già inseriti fuori dal nuovo periodo: spostali o eliminali prima di modificare le date."
            ])->withInput();
        }

        // Non scendere sotto il numero di appelli già presenti per un insegnamento
        $massimoPresente = (int) Appello::where('sessione_id', $sessione->sessione_id)
            ->selectRaw('COUNT(*) AS n')
            ->groupBy('ins_corso_id')
            ->orderByDesc('n')
            ->value('n');

        if ($validated['numero_appelli'] < $massimoPresente) {
            return back()->withErrors([
                'numero_appelli' => "Esiste già un insegnamento con {$massimoPresente} appelli in questa sessione: il numero previsto non può essere inferiore."
            ])->withInput();
        }

        $sessione->update($validated);

        return redirect()->route('admin.sessioni.index')->with('success', 'Sessione d\'esame aggiornata con successo!');
    }

    public function destroy($id)
    {
        $sessione = Sessione::findOrFail($id);

        if (Carbon::parse($sessione->data_fine)->lt(Carbon::today())) {
            return back()->with('error', 'Impossibile eliminare una sessione d\'esame già passata.');
        }

        $sessione->delete();

        return redirect()->route('admin.sessioni.index')->with('success', 'Sessione d\'esame eliminata con successo!');
    }

    // --- HELPER ---

    /** L'anno è derivato dalla data di inizio (il form non ha un campo "anno"). */
    private function derivaAnno(Request $request): void
    {
        if ($request->filled('data_inizio')) {
            $request->merge(['anno' => Carbon::parse($request->data_inizio)->year]);
        }
    }

    /** Regole comuni a creazione e modifica (data_inizio viene aggiunta dal chiamante). */
    private function regole(Request $request, ?int $escludiId = null): array
    {
        return [
            'nome'           => 'required|string|max:150',
            'anno'           => 'required|integer',
            'numero_appelli' => 'required|integer|min:1|max:4',
            'data_fine'      => ['required', 'date', function ($attribute, $value, $fail) use ($request, $escludiId) {
                if (!$request->filled('data_inizio')) {
                    return;
                }

                $inizio = Carbon::parse($request->data_inizio);
                $fine = Carbon::parse($value);

                if ($fine->lt($inizio->copy()->addWeeks(2))) {
                    $fail('La data di fine deve essere di almeno due settimane successiva alla data di inizio.');
                    return;
                }

                // Le sessioni non possono sovrapporsi
                $sovrapposta = Sessione::where('data_inizio', '<=', $fine->toDateString())
                    ->where('data_fine', '>=', $inizio->toDateString())
                    ->when($escludiId, fn ($q) => $q->where('sessione_id', '!=', $escludiId))
                    ->first();

                if ($sovrapposta) {
                    $fail("Il periodo si sovrappone alla sessione esistente '{$sovrapposta->nome}'.");
                }
            }],
        ];
    }

    private function messaggi(): array
    {
        return [
            'numero_appelli.min' => 'Il numero minimo di appelli previsti è 1.',
            'numero_appelli.max' => 'Il numero massimo di appelli previsti per una sessione è 4.',
        ];
    }
}
