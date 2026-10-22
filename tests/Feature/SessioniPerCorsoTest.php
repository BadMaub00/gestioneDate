<?php

namespace Tests\Feature;

use App\Models\Appello;
use App\Models\Sessione;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SessioniPerCorsoTest extends BaseDatiTest
{
    private function nuovaSessione(array $o = []): array
    {
        $inizio = Carbon::parse($this->lunedi)->addMonths(5);

        return array_merge([
            'nome' => 'Invernale', 'corsi' => [1, 2], 'numero_appelli' => 2,
            'data_inizio' => $inizio->toDateString(), 'data_fine' => $inizio->copy()->addWeeks(3)->toDateString(),
        ], $o);
    }

    public function test_crea_sessione_per_piu_corsi_in_una_volta(): void
    {
        $this->actingAs($this->admin)->post('/admin/sessioni', $this->nuovaSessione(['corsi' => [1, 2, 3]]))->assertSessionHasNoErrors();

        $this->assertSame(3, Sessione::where('nome', 'Invernale')->count());
        $this->assertSame([1, 2, 3], Sessione::where('nome', 'Invernale')->orderBy('corso_id')->pluck('corso_id')->map(fn ($c) => (int) $c)->all());
    }

    public function test_serve_almeno_un_corso(): void
    {
        $this->actingAs($this->admin)->post('/admin/sessioni', $this->nuovaSessione(['corsi' => []]))->assertSessionHasErrors('corsi');
        $this->actingAs($this->admin)->post('/admin/sessioni', $this->nuovaSessione(['corsi' => [999]]))->assertSessionHasErrors('corsi.0');
        $this->assertSame(2, Sessione::count());
    }

    public function test_distanza_minima_di_un_mese_nello_stesso_corso(): void
    {
        $fine = Carbon::parse($this->sessione->data_fine);

        // 10 giorni dopo la fine della sessione del corso 1 -> rifiutato (anche se il corso 2 sarebbe ok)
        $inizio = $fine->copy()->addDays(10);
        $this->actingAs($this->admin)->post('/admin/sessioni', $this->nuovaSessione([
            'corsi' => [1], 'data_inizio' => $inizio->toDateString(), 'data_fine' => $inizio->copy()->addWeeks(3)->toDateString(),
        ]))->assertSessionHasErrors('corsi');

        // anche prima della sessione esistente (fine nuova + 1 mese oltre l'inizio dell'altra)
        $fineNuova = Carbon::parse($this->sessione->data_inizio)->subDays(10);
        $this->actingAs($this->admin)->post('/admin/sessioni', $this->nuovaSessione([
            'corsi' => [1], 'data_inizio' => $fineNuova->copy()->subWeeks(3)->toDateString(), 'data_fine' => $fineNuova->toDateString(),
        ]))->assertSessionHasErrors('corsi');

        // un corso senza sessioni vicine non è bloccato
        $this->actingAs($this->admin)->post('/admin/sessioni', $this->nuovaSessione([
            'corsi' => [3], 'data_inizio' => $inizio->toDateString(), 'data_fine' => $inizio->copy()->addWeeks(3)->toDateString(),
        ]))->assertSessionHasNoErrors();

        // un mese esatto dopo la fine -> consentito
        $inizioOk = $fine->copy()->addMonth();
        $this->actingAs($this->admin)->post('/admin/sessioni', $this->nuovaSessione([
            'corsi' => [1], 'data_inizio' => $inizioOk->toDateString(), 'data_fine' => $inizioOk->copy()->addWeeks(3)->toDateString(),
        ]))->assertSessionHasNoErrors();
    }

    public function test_un_corso_non_blocca_gli_altri_se_uno_solo_e_in_conflitto(): void
    {
        $fine = Carbon::parse($this->sessione->data_fine)->addDays(5);
        $this->actingAs($this->admin)->post('/admin/sessioni', $this->nuovaSessione([
            'corsi' => [1, 3], 'data_inizio' => $fine->toDateString(), 'data_fine' => $fine->copy()->addWeeks(3)->toDateString(),
        ]))->assertSessionHasErrors('corsi');

        // niente è stato creato per il corso 3: o tutte o nessuna
        $this->assertSame(0, Sessione::where('corso_id', 3)->count());
    }

    public function test_modifica_aggiunge_e_rimuove_corsi_del_gruppo(): void
    {
        $id = $this->sessione->sessione_id;
        $base = ['nome' => 'S', 'numero_appelli' => 2, 'data_inizio' => $this->sessione->data_inizio, 'data_fine' => $this->sessione->data_fine];

        // aggiunta del corso 3
        $this->actingAs($this->admin)->put("/admin/sessioni/{$id}", $base + ['corsi' => [1, 2, 3]])->assertSessionHasNoErrors();
        $this->assertSame(1, Sessione::where('corso_id', 3)->where('nome', 'S')->count());

        // rimozione del corso 3 (senza appelli) -> ok
        $this->actingAs($this->admin)->put("/admin/sessioni/{$id}", $base + ['corsi' => [1, 2]])->assertSessionHasNoErrors();
        $this->assertSame(0, Sessione::where('corso_id', 3)->count());

        // rimozione del corso 2 che ha appelli -> bloccata
        Appello::create($this->payload(['ins_corso_id' => $this->insC]));
        $this->actingAs($this->admin)->put("/admin/sessioni/{$id}", $base + ['corsi' => [1]])->assertSessionHasErrors('corsi');
        $this->assertSame(1, Sessione::where('corso_id', 2)->count());
    }

    public function test_modifica_date_per_tutti_i_corsi_del_gruppo(): void
    {
        $id = $this->sessione->sessione_id;
        $nuovaFine = Carbon::parse($this->sessione->data_fine)->addDays(3)->toDateString();

        $this->actingAs($this->admin)->put("/admin/sessioni/{$id}", [
            'nome' => 'S', 'numero_appelli' => 2, 'corsi' => [1, 2],
            'data_inizio' => $this->sessione->data_inizio, 'data_fine' => $nuovaFine,
        ])->assertSessionHasNoErrors();

        $this->assertSame($nuovaFine, substr((string) $this->sessione->fresh()->data_fine, 0, 10));
        $this->assertSame($nuovaFine, substr((string) $this->sessione2->fresh()->data_fine, 0, 10));
    }

    public function test_elimina_solo_la_sessione_del_corso_scelto(): void
    {
        $this->actingAs($this->admin)->delete("/admin/sessioni/{$this->sessione->sessione_id}")->assertSessionHasNoErrors();
        $this->assertNull(Sessione::find($this->sessione->sessione_id));
        $this->assertNotNull(Sessione::find($this->sessione2->sessione_id));
    }

    public function test_pagine_admin_sessioni_mostrano_corsi(): void
    {
        DB::table('corso')->where('corso_id', 1)->update(['nome' => 'IngInf']);

        $this->actingAs($this->admin)->get('/admin/sessioni')->assertOk()->assertSee('IngInf')->assertSee('filtroCorsoSessione', false);
        $this->actingAs($this->admin)->get('/admin/sessioni/crea')->assertOk()->assertSee('name="corsi[]"', false);

        $r = $this->actingAs($this->admin)->get("/admin/sessioni/{$this->sessione->sessione_id}/modifica")->assertOk();
        $r->assertSee('id="corso_1"', false);
        $this->assertMatchesRegularExpression('/id="corso_1"[^>]*checked/', $r->getContent());
        $this->assertMatchesRegularExpression('/id="corso_2"[^>]*checked/', $r->getContent());
        $this->assertDoesNotMatchRegularExpression('/id="corso_3"[^>]*checked/', $r->getContent());
    }

    public function test_appello_solo_su_sessioni_del_corso_dell_insegnamento(): void
    {
        // insA è del corso 1, sessione2 è del corso 2
        $this->actingAs($this->docA)->post('/docente/appelli', $this->payload(['sessione_id' => $this->sessione2->sessione_id]))->assertSessionHasErrors('sessione_id');
        $this->assertSame(0, Appello::count());

        $this->actingAs($this->docA)->get('/docente/appelli/crea?ins_corso_id=' . $this->insA . '&sessione_id=' . $this->sessione2->sessione_id)->assertSessionHas('error');
        $this->actingAs($this->docA)->get('/docente/appelli/crea?ins_corso_id=' . $this->insA . '&sessione_id=' . $this->sessione->sessione_id)->assertOk();
    }

    public function test_form_appelli_mostra_solo_sessioni_del_corso(): void
    {
        $r = $this->actingAs($this->docA)->get("/docente/insegnamento/{$this->insA}/appelli")->assertOk();
        $sessioni = $r->viewData('sessioni');
        $this->assertSame([$this->sessione->sessione_id], $sessioni->pluck('sessione_id')->all());
    }

    public function test_docente_seleziona_prima_il_corso_poi_la_sessione(): void
    {
        // docB insegna nei corsi 1 e 2; docA solo nel corso 1
        $r = $this->actingAs($this->docA)->get('/docente/calendario-personale/seleziona')->assertOk();
        $this->assertSame([1], $r->viewData('corsi')->pluck('corso_id')->map(fn ($c) => (int) $c)->all());
        $this->assertSame([$this->sessione->sessione_id], $r->viewData('sessioni')->pluck('sessione_id')->all());
        $r->assertSee('data-corso="1"', false)->assertDontSee('data-corso="2"', false);

        $r = $this->actingAs($this->docB)->get('/docente/calendario-personale/seleziona')->assertOk();
        $this->assertCount(2, $r->viewData('sessioni'));
    }

    public function test_calendario_personale_con_filtri_corso_e_sessione(): void
    {
        $url = '/docente/calendario-personale/seleziona';

        // coerente -> redirect al calendario personale con la sessione e il corso scelti
        $this->actingAs($this->docB)->post($url, ['corso_id' => 2, 'sessione_id' => $this->sessione2->sessione_id])
            ->assertRedirect("/docente/calendario/{$this->sessione2->sessione_id}?corso_id=2");

        // sessione di un altro corso -> errore
        $this->actingAs($this->docB)->post($url, ['corso_id' => 1, 'sessione_id' => $this->sessione2->sessione_id])->assertSessionHasErrors('sessione_id');

        // corso in cui il docente non insegna -> errore
        $this->actingAs($this->docA)->post($url, ['corso_id' => 2, 'sessione_id' => $this->sessione2->sessione_id])->assertSessionHasErrors('corso_id');
    }

    public function test_calendario_personale_mostra_solo_insegnamenti_del_corso_della_sessione(): void
    {
        Appello::create($this->payload(['ins_corso_id' => $this->insB]));                                  // corso 1
        Appello::create($this->payload(['ins_corso_id' => $this->insC, 'aula_id' => $this->aula2]));       // corso 2

        $r = $this->actingAs($this->docB)->get("/docente/calendario/{$this->sessione->sessione_id}")->assertOk();
        $this->assertSame([$this->insB], $r->viewData('insegnamentiCorsi')->pluck('ins_corso_id')->map(fn ($c) => (int) $c)->all());
        $this->assertSame(1, (int) $r->viewData('corso')->corso_id);

        $r = $this->actingAs($this->docB)->get("/docente/calendario/{$this->sessione2->sessione_id}")->assertOk();
        $this->assertSame([$this->insC], $r->viewData('insegnamentiCorsi')->pluck('ins_corso_id')->map(fn ($c) => (int) $c)->all());
    }

    public function test_calendario_generale_docente_coerenza_corso_sessione(): void
    {
        $url = '/docente/calendario-generale/seleziona';

        $this->actingAs($this->docB)->post($url, ['corso_id' => 1, 'annoCorso' => 1, 'sessione_id' => $this->sessione2->sessione_id])->assertSessionHasErrors('sessione_id');
        $this->actingAs($this->docB)->post($url, ['corso_id' => 2, 'annoCorso' => 1, 'sessione_id' => $this->sessione2->sessione_id])
            ->assertRedirect("/docente/calendario-generale/{$this->sessione2->sessione_id}/1/2");

        // anche aprendo direttamente l'URL con combinazione incoerente
        $this->actingAs($this->docB)->get("/docente/calendario-generale/{$this->sessione2->sessione_id}/1/1")->assertRedirect();

        // la pagina di selezione offre solo le sessioni dei corsi del docente, con il corso associato
        $r = $this->actingAs($this->docA)->get($url)->assertOk();
        $this->assertSame([$this->sessione->sessione_id], $r->viewData('sessioni')->pluck('sessione_id')->all());
    }

    public function test_calendario_admin_filtra_per_corso_e_sessione(): void
    {
        Appello::create($this->payload(['ins_corso_id' => $this->insA]));
        Appello::create($this->payload(['ins_corso_id' => $this->insC, 'aula_id' => $this->aula2]));

        // solo corso -> solo appelli di quel corso
        $r = $this->actingAs($this->admin)->get('/admin/calendario?corso_id=2')->assertOk();
        $this->assertSame([$this->sessione2->sessione_id], $r->viewData('appelliGrouped')->keys()->map(fn ($k) => (int) $k)->all());

        // sessione senza corso -> il corso viene ricavato dalla sessione
        $r = $this->actingAs($this->admin)->get('/admin/calendario?sessione_id=' . $this->sessione->sessione_id)->assertOk();
        $this->assertSame(1, (int) $r->viewData('corso_id'));
        $this->assertSame([$this->sessione->sessione_id], $r->viewData('appelliGrouped')->keys()->map(fn ($k) => (int) $k)->all());

        // sessione di un altro corso -> la sessione è ignorata e vale il corso
        $r = $this->actingAs($this->admin)->get('/admin/calendario?corso_id=1&sessione_id=' . $this->sessione2->sessione_id)->assertOk();
        $this->assertNull($r->viewData('sessione_id'));
        $this->assertSame([$this->sessione->sessione_id], $r->viewData('appelliGrouped')->keys()->map(fn ($k) => (int) $k)->all());

        // le opzioni sessione portano il corso (usato dal filtro a cascata)
        $r->assertSee('data-corso="1"', false)->assertSee('data-corso="2"', false);
    }

    public function test_script_di_migrazione_sposta_gli_appelli_sulla_sessione_del_loro_corso(): void
    {
        // situazione "vecchia": tutti gli appelli puntano alla sessione del corso 1
        Appello::create($this->payload(['ins_corso_id' => $this->insA]));
        $vecchio = Appello::create($this->payload(['ins_corso_id' => $this->insC, 'aula_id' => $this->aula2, 'sessione_id' => $this->sessione->sessione_id]));
        DB::table('sessione_esami')->where('sessione_id', $this->sessione2->sessione_id)->delete();

        $sql = file_get_contents(base_path('SQL/migrazione_sessioni_per_corso.sql'));
        DB::unprepared($sql);
        DB::unprepared($sql); // rieseguibile

        $nuova = Sessione::where('corso_id', 2)->get();
        $this->assertCount(1, $nuova);
        $this->assertSame((int) $nuova->first()->sessione_id, (int) $vecchio->fresh()->sessione_id);
        $this->assertSame($this->sessione->sessione_id, (int) Appello::where('ins_corso_id', $this->insA)->first()->sessione_id);
    }
}
