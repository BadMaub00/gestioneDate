<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sessione;
use App\Models\Appello;
use App\Models\Corso;

class adminController extends Controller
{
    public function dashboard(){
        $sessioni = Sessione::orderBy('data_inizio', 'desc')->get();
        return view('admin.dashboard', compact('sessioni'));
    }

    public function indexCorsi() {
        $corsi = Corso::orderBy('nome', 'asc')->get();
        return view('admin.corsi.index', compact('corsi'));
    }

    public function viewPianoDiStudi($corso_id) {
    // 1. Recupera il corso con gli insegnamenti ordinati
    $corso = Corso::with(['insegnamenti' => function($query) {
        $query->orderBy('anno_di_corso', 'asc');
    }])->findOrFail($corso_id);

    // 2. Raggruppa gli insegnamenti usando la colonna pivot corretta (anno_di_corso)
    $insegnamentiPerAnno = $corso->insegnamenti->groupBy('anno_di_corso');

    // 3. Calcola la durata del corso in base al tipo_corso
    $durataAnni = match ($corso->tipo_corso) {
        'Magistrale' => 2,
        'Magistrale a ciclo unico' => 5,
        default => 3, // Triennale
    };

    // 4. Passa tutte le variabili necessarie alla vista
    return view('admin.corsi.piano_di_studi', compact('corso', 'insegnamentiPerAnno', 'durataAnni'));
}

    public function viewcalendario_generale($sessione_id = null){
        // 1. Recupera tutte le sessioni d'esame per popolare il menu/selettore nella vista
        $sessioni = Sessione::orderBy('data_inizio', 'desc')->get();

        // 2. Se non è stata specificata una sessione nell'URL, seleziona la prima/più recente di default
        if (!$sessione_id && $sessioni->isNotEmpty()) {
            $sessione_id = $sessioni->first()->getKey(); 
        }

        // 3. Recupera tutti gli appelli d'esame per la sessione selezionata
        // Eager loading (with) per caricare i dettagli dell'insegnamento, corso e sessione
        $appelli = Appello::where('sessione_id', $sessione_id)->with(['insegnamentoCorso.insegnamento', 'insegnamentoCorso.corso'])
            ->orderBy('data', 'asc')
            ->orderBy('ora_inizio', 'asc')
            ->get();

        // 4. Passa tutti i dati necessari alla vista Blade dell'admin
        return view('admin.calendario', compact('appelli', 'sessioni', 'sessione_id'));
    }
}
