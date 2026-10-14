<?php

namespace App\Http\Controllers;

use App\Models\Appello;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\InsegnamentiCorso;
use App\Models\DocenteInsegnamento;
use App\Models\Utente;


class docenteController extends Controller
{
    public function dashboard(){
        $user = Auth::user();

        // Recupera gli insegnamenti assegnati a questo docente
        //$insegnamentiCorsi = InsegnamentiCorso::where('user_id', $user->getUtenteId())->with('insegnamento')->toArray(); //insegnamento è il nome del metodo in InsegnamentiCorso che definisce la relazione con il modello Insegnamento
        $insegnamentiCorsi = $this->insAssociati($user);
        //$insegnamentiCorsi->firstWhere("ins_corso_id", 3)->insegnamento->nome;

        /*foreach ($insegnamentiCorsi as $insC) {
            $insC->insegnamento_nome = $insC->insegnamento->nome;
        }
        va usato nella view per restiruire il nome dell'insegnamento associato a ciascun insegnamento del corso.*/

        return view('docente.dashboard', compact('insegnamentiCorsi'));
    }

    public function viewCalendarioPersonale(int $sessione_id){
        /** @var \App\Models\Utente $user */
        $user = Auth::user();

        // 1. Recupera la base degli insegnamenti usando la tua funzione
        $insegnamentiCorsi = $this->insAssociati($user);

        // 2. Carica in differita (Lazy Eager Loading) gli appelli filtrati per quella sessione
        $insegnamentiCorsi->load(['appelli' => function($query) use ($sessione_id) {
            $query->where('sessione_id', $sessione_id)
                  ->orderBy('data')
                  ->orderBy('ora_inizio');
        }]);

        return view('docente.calendarioPersonale', compact('insegnamentiCorsi', 'sessione_id'));
    }

    public function viewCalendarioGenerale(int $sessione_id, int $annoCorso, int $corso_id){
    $appelliAnnoCorso = Appello::join('insegnamento_corso', 'appello.ins_corso_id', '=', 'insegnamento_corso.ins_corso_id')
    ->join('insegnamento', 'insegnamento_corso.insegnamento_id', '=', 'insegnamento.insegnamento_id') 
    ->join('corso', 'insegnamento_corso.corso_id', '=', 'corso.corso_id') 
    ->join('sessione_esami', 'appello.sessione_id', '=', 'sessione_esami.sessione_id') // Nuova join con la tabella sessione_esami
    ->where([
        'insegnamento_corso.corso_id' => $corso_id,
        'insegnamento_corso.anno_di_corso' => $annoCorso,
        'appello.sessione_id' => $sessione_id,
    ])
    ->orderBy('appello.data')
    ->orderBy('appello.ora_inizio')
    ->select(
        'appello.data', 
        'appello.ora_inizio', 
        'appello.ora_fine', 
        'insegnamento_corso.ins_corso_id',
        'insegnamento.nome as nome_insegnamento', 
        'corso.nome as nome_corso',
        // Aggiunta dei campi della sessione:
        'sessione_esami.nome as nome_sessione', 
        'sessione_esami.anno as anno_sessione'
    )
    ->get();

    return view('docente.calendarioGenerale', compact('appelliAnnoCorso', 'sessione_id', 'annoCorso', 'corso_id'));
}

    private function insAssociati(Utente $user){
        $insegnamentiTemp = DocenteInsegnamento::where('utente_id', $user->getKey())
            ->pluck('ins_corso_id')
            ->toArray();
            
        // Aggiungi with('insegnamento') per ottimizzare le query
        return InsegnamentiCorso::whereIn('ins_corso_id', $insegnamentiTemp)
            ->with('insegnamento','corso')
            ->get();
    }
}
