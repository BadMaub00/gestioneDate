<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appello;
use App\Models\Sessione;
use Carbon\Carbon;

class appelloController extends Controller
{
    /**
     * Salvataggio nuovo appello (Docente / Admin)
     */
    public function store(Request $request)
    {
        $sessione = Sessione::findOrFail($request->sessione_id);

        // 1. Blocco sessioni già passate
        if (Carbon::parse($sessione->data_fine)->lt(Carbon::today())) {
            return back()->with('error', 'Impossibile creare un appello per una sessione già conclusa.');
        }

        // 2. Controllo limite massimo appelli per lo stesso insegnamentoCorso nella sessione
        $appelliEsistenti = Appello::where('sessione_id', $request->sessione_id)
            ->where('ins_corso_id', $request->ins_corso_id)
            ->count();

        if ($appelliEsistenti >= $sessione->numero_appelli) {
            return back()->withErrors([
                'limite_appelli' => "La sessione '{$sessione->nome}' consente al massimo {$sessione->numero_appelli} appelli per questo insegnamento. Limite raggiunto."
            ])->withInput();
        }

        // 3. Validazione regole di business su date e orari
        $validated = $request->validate([
            'sessione_id'    => 'required|exists:sessione_esami,sessione_id',
            'ins_corso_id'   => 'required|exists:insegnamento_corso,ins_corso_id',
            'aula_id'        => 'required|exists:aula,aula_id',
            'ora_inizio'     => 'required|date_format:H:i|after_or_equal:08:00',
            'ora_fine'       => 'required|date_format:H:i|after:ora_inizio|before_or_equal:19:00',
            'data'           => [
                'required',
                'date',
                'after_or_equal:' . $sessione->data_inizio,
                'before_or_equal:' . $sessione->data_fine,
                function ($attribute, $value, $fail) {
                    $data = Carbon::parse($value);
                    
                    // Vincolo giorni feriali (Lunedì - Venerdì)
                    if ($data->isWeekend()) {
                        $fail('Gli appelli d\'esame possono essere programmati solo dal lunedì al venerdì.');
                    }
                },
            ],
        ], [
            'data.after_or_equal'      => "La data dell'appello deve rientrare nella sessione (Inizio: " . Carbon::parse($sessione->data_inizio)->format('d/m/Y') . ").",
            'data.before_or_equal'     => "La data dell'appello deve rientrare nella sessione (Fine: " . Carbon::parse($sessione->data_fine)->format('d/m/Y') . ").",
            'ora_inizio.after_or_equal'=> "L'orario di inizio non può essere antecedente le 08:00.",
            'ora_fine.before_or_equal' => "L'orario di fine deve essere entro le 19:00.",
            'ora_fine.after'           => "L'orario di fine deve essere successivo all'orario di inizio.",
        ]);

        Appello::create($validated);

        return back()->with('success', 'Appello creato con successo!');
    }

    /**
     * Aggiornamento appello esistente (Docente / Admin)
     */
    public function update(Request $request, $id)
    {
        $appello = Appello::with('sessione')->findOrFail($id);
        $sessione = $appello->sessione;

        // 1. Blocco modifica se la sessione è passata
        if (Carbon::parse($sessione->data_fine)->lt(Carbon::today())) {
            return back()->with('error', 'Impossibile modificare un appello appartenente a una sessione passata.');
        }

        // 2. Validazione regole di business su date e orari
        $validated = $request->validate([
            'aula_id'    => 'required|exists:aula,aula_id',
            'ora_inizio' => 'required|date_format:H:i|after_or_equal:08:00',
            'ora_fine'   => 'required|date_format:H:i|after:ora_inizio|before_or_equal:19:00',
            'data'       => [
                'required',
                'date',
                'after_or_equal:' . $sessione->data_inizio,
                'before_or_equal:' . $sessione->data_fine,
                function ($attribute, $value, $fail) {
                    $data = Carbon::parse($value);
                    if ($data->isWeekend()) {
                        $fail('Gli appelli d\'esame possono essere programmati solo dal lunedì al venerdì.');
                    }
                },
            ],
        ], [
            'data.after_or_equal'      => "La data dell'appello deve rientrare nella sessione (Inizio: " . Carbon::parse($sessione->data_inizio)->format('d/m/Y') . ").",
            'data.before_or_equal'     => "La data dell'appello deve rientrare nella sessione (Fine: " . Carbon::parse($sessione->data_fine)->format('d/m/Y') . ").",
            'ora_inizio.after_or_equal'=> "L'orario di inizio non può essere antecedente le 08:00.",
            'ora_fine.before_or_equal' => "L'orario di fine deve essere entro le 19:00.",
            'ora_fine.after'           => "L'orario di fine deve essere successivo all'orario di inizio.",
        ]);

        $appello->update($validated);

        return back()->with('success', 'Appello aggiornato con successo!');
    }

    /**
     * Eliminazione appello (Docente / Admin)
     */
    public function destroy($id)
    {
        $appello = Appello::with('sessione')->findOrFail($id);

        if (Carbon::parse($appello->sessione->data_fine)->lt(Carbon::today())) {
            return back()->with('error', 'Impossibile eliminare un appello appartenente a una sessione passata.');
        }

        $appello->delete();

        return back()->with('success', 'Appello eliminato con successo.');
    }
}