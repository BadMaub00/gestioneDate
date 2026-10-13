<?php

namespace App\Http\Controllers;

use App\Models\Appello;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\InsegnamentiCorso;
use App\Models\DocenteInsegnamento;
use App\Models\DatiAccesso;


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
        $user = Auth::user();

        // Recupera gli insegnamenti assegnati a questo docente
        $insegnamentiCorsi = $this->insAssociati($user);

        // Recupera gli appelli per la sessione specificata
        $appelli = [];
        foreach ($insegnamentiCorsi as $insegnamentoCorso) {
            $appelliForInsegnamento = $insegnamentoCorso->appelli()->where('sessione_id', $sessione_id)->get();
            $appelli[$insegnamentoCorso->insegnamento->nome] = $appelliForInsegnamento;
        }

        return view('docente.calendarioPersonale', compact('appelli', 'sessione_id'));
    }

    public function viewCalendarioGenerale(int $sessione_id, int $annoCorso, int $corso_id){
        $appelliAnnoCorso = Appello::join('insegnamento_corso', 'appello.ins_corso_id', '=', 'insegnamento_corso.ins_corso_id')
        ->where([
            'insegnamento_corso.corso_id' => $corso_id,
            'insegnamento_corso.anno_di_corso' => $annoCorso,
            'appello.sessione_id' => $sessione_id,
        ])
        ->orderBy('appello.data')
        ->orderBy('appello.ora_inizio')
        ->select('appello.data', 'appello.ora_inizio', 'appello.ora_fine', 'insegnamento_corso.ins_corso_id')
        ->get();

        return view('docente.calendarioGenerale', compact('appelliAnnoCorso', 'sessione_id', 'annoCorso', 'corso_id'));
    
    }

    private function insAssociati(DatiAccesso $user){
        $insegnamentiTemp = DocenteInsegnamento::where('docente_id', $user->getKey())
            ->pluck('ins_corso_id')
            ->toArray();
            
        // Aggiungi with('insegnamento') per ottimizzare le query
        return InsegnamentiCorso::whereIn('ins_corso_id', $insegnamentiTemp)
            ->with('insegnamento')
            ->get();
    }
}
