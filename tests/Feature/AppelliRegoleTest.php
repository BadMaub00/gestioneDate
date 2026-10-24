<?php

namespace Tests\Feature;

use App\Models\Appello;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AppelliRegoleTest extends BaseDati
{
    public function test_docente_crea_appello_proprio(): void
    {
        $this->actingAs($this->docA)->post('/docente/appelli', $this->payload())->assertSessionHasNoErrors();
        $this->assertSame(1, Appello::count());
    }

    public function test_docente_non_puo_creare_per_insegnamento_altrui(): void
    {
        $this->actingAs($this->docA)->post('/docente/appelli', $this->payload(['ins_corso_id' => $this->insB]))->assertForbidden();
        $this->assertSame(0, Appello::count());
    }

    public function test_docente_non_puo_modificare_ne_eliminare_appello_altrui(): void
    {
        $a = Appello::create($this->payload(['ins_corso_id' => $this->insB]));
        $this->actingAs($this->docA)->put("/docente/appelli/{$a->appello_id}", ['data' => $this->lunedi, 'ora_inizio' => '10:00', 'ora_fine' => '11:00', 'aula_id' => $this->aula1])->assertForbidden();
        $this->actingAs($this->docA)->delete("/docente/appelli/{$a->appello_id}")->assertForbidden();
        $this->assertSame(1, Appello::count());
    }

    public function test_stessa_aula_stesso_orario_rifiutata(): void
    {
        Appello::create($this->payload(['ins_corso_id' => $this->insB]));
        // insC è di un altro corso, quindi l'unico conflitto è l'aula
        $this->actingAs($this->docB)->post('/docente/appelli', $this->payload(['ins_corso_id' => $this->insC, 'ora_inizio' => '10:00']))->assertSessionHasErrors('aula_id');
    }

    public function test_stesso_anno_stesso_corso_rifiutato_ma_corso_diverso_ok(): void
    {
        Appello::create($this->payload(['ins_corso_id' => $this->insA]));
        // insB: stesso corso e anno di insA, aula diversa -> rifiutato
        $this->actingAs($this->docB)->post('/docente/appelli', $this->payload(['ins_corso_id' => $this->insB, 'aula_id' => $this->aula2]))->assertSessionHasErrors('data');
        // insC: altro corso, stesso anno, aula diversa -> consentito
        $this->actingAs($this->docB)->post('/docente/appelli', $this->payload(['ins_corso_id' => $this->insC, 'aula_id' => $this->aula2]))->assertSessionHasNoErrors();
        $this->assertSame(2, Appello::count());
    }

    public function test_docente_non_puo_iniziare_dopo_le_18(): void
    {
        $this->actingAs($this->docA)->post('/docente/appelli', $this->payload(['ora_inizio' => '18:30', 'ora_fine' => '19:00']))->assertSessionHasErrors('ora_inizio');
    }

    public function test_admin_aula_di_altro_edificio_rifiutata(): void
    {
        $a = Appello::create($this->payload());
        $this->actingAs($this->admin)->put("/admin/appelli/{$a->appello_id}", ['data' => $this->lunedi, 'ora_inizio' => '09:00', 'ora_fine' => '12:00', 'edificio_id' => 1, 'aula_id' => $this->aula2])->assertSessionHasErrors('aula_id');
        $this->actingAs($this->admin)->put("/admin/appelli/{$a->appello_id}", ['data' => $this->lunedi, 'ora_inizio' => '09:00', 'ora_fine' => '12:00', 'edificio_id' => 2, 'aula_id' => $this->aula2])->assertSessionHasNoErrors();
        $this->assertSame($this->aula2, (int) $a->fresh()->aula_id);
    }

    public function test_sessione_crea_senza_campo_anno(): void
    {
        $inizio = Carbon::today()->addMonths(6)->toDateString();
        $fine = Carbon::today()->addMonths(6)->addWeeks(3)->toDateString();
        $this->actingAs($this->admin)->post('/admin/sessioni', ['nome' => 'Nuova', 'corsi' => [1], 'numero_appelli' => 3, 'data_inizio' => $inizio, 'data_fine' => $fine])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('sessione_esami', ['nome' => 'Nuova', 'anno' => Carbon::parse($inizio)->year]);
    }

    public function test_sessione_sovrapposta_rifiutata(): void
    {
        $this->actingAs($this->admin)->post('/admin/sessioni', ['nome' => 'Dup', 'corsi' => [1], 'numero_appelli' => 2, 'data_inizio' => $this->sessione->data_inizio, 'data_fine' => Carbon::parse($this->sessione->data_inizio)->addWeeks(3)->toDateString()])->assertSessionHasErrors('corsi');
    }

    public function test_sessione_update_non_lascia_appelli_fuori_periodo_ne_riduce_sotto_il_presente(): void
    {
        Appello::create($this->payload(['ins_corso_id' => $this->insA]));
        Appello::create($this->payload(['ins_corso_id' => $this->insA, 'ora_inizio' => '14:00', 'ora_fine' => '16:00', 'aula_id' => $this->aula2]));
        $id = $this->sessione->sessione_id;
        $base = ['nome' => 'S', 'corsi' => [1, 2], 'numero_appelli' => 2, 'data_inizio' => $this->lunedi, 'data_fine' => Carbon::parse($this->lunedi)->addWeeks(3)->toDateString()];

        // accorciare la sessione lasciando fuori l'appello (data = primo giorno, quindi spostiamo l'inizio avanti)
        $this->actingAs($this->admin)->put("/admin/sessioni/{$id}", array_merge($base, ['data_inizio' => Carbon::parse($this->lunedi)->addDay()->toDateString()]))->assertSessionHasErrors('data_inizio');
        // ridurre numero_appelli sotto i 2 già presenti
        $this->actingAs($this->admin)->put("/admin/sessioni/{$id}", array_merge($base, ['numero_appelli' => 1]))->assertSessionHasErrors('numero_appelli');
        // modifica valida
        $this->actingAs($this->admin)->put("/admin/sessioni/{$id}", array_merge($base, ['nome' => 'Rinominata']))->assertSessionHasNoErrors();
    }

    public function test_calendario_generale_ordinato_e_docente_primario(): void
    {
        DB::table('docente_insegnamento')->insert(['ins_corso_id' => $this->insA, 'utente_id' => $this->docB->utente_id, 'ruolo' => 'Secondario']);
        Appello::create($this->payload(['ins_corso_id' => $this->insA, 'data' => Carbon::parse($this->lunedi)->addDays(2)->toDateString()]));
        Appello::create($this->payload(['ins_corso_id' => $this->insA, 'ora_inizio' => '14:00', 'ora_fine' => '15:00', 'aula_id' => $this->aula2]));

        DB::enableQueryLog();
        $r = $this->actingAs($this->docB)->get("/docente/calendario-generale/{$this->sessione->sessione_id}/1/1");
        $r->assertOk();
        $appelli = $r->viewData('appelliAnnoCorso');
        $this->assertSame([$this->lunedi, Carbon::parse($this->lunedi)->addDays(2)->toDateString()], $appelli->pluck('data')->map(fn ($d) => substr($d, 0, 10))->all());
        $this->assertSame('A', $appelli->first()->docente_nome, 'deve mostrare il Primario (A), non il primo docente associato');
        $this->assertLessThan(12, count(DB::getQueryLog()), 'niente query per ogni appello');
    }
}
