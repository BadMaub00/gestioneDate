<?php

namespace Tests\Feature;

use App\Models\Appello;
use App\Models\Sessione;
use App\Models\Utente;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AppelliRegoleTest extends TestCase
{
    private Sessione $sessione;
    private Utente $docA;
    private Utente $docB;
    private Utente $admin;
    private int $insA;   // corso 1, anno 1, di docA
    private int $insB;   // corso 1, anno 1, di docB
    private int $insC;   // corso 2, anno 1, di docB
    private int $aula1;
    private int $aula2;
    private string $lunedi;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        $this->creaSchema();

        $this->lunedi = Carbon::today()->addMonths(2)->next(Carbon::MONDAY)->toDateString();
        $fine = Carbon::parse($this->lunedi)->addWeeks(3)->toDateString();

        $this->sessione = Sessione::create(['nome' => 'S', 'anno' => 2026, 'numero_appelli' => 2, 'data_inizio' => $this->lunedi, 'data_fine' => $fine]);

        $this->docA = Utente::create(['username' => 'a', 'password' => 'x', 'nome' => 'A', 'cognome' => 'A', 'data_nascita' => '1980-01-01', 'ruolo' => 'Docente']);
        $this->docB = Utente::create(['username' => 'b', 'password' => 'x', 'nome' => 'B', 'cognome' => 'B', 'data_nascita' => '1980-01-01', 'ruolo' => 'Docente']);
        $this->admin = Utente::create(['username' => 'ad', 'password' => 'x', 'nome' => 'Ad', 'cognome' => 'Ad', 'data_nascita' => '1980-01-01', 'ruolo' => 'Amministratore']);

        DB::table('corso')->insert([['corso_id' => 1, 'nome' => 'C1', 'tipo_corso' => 'Triennale'], ['corso_id' => 2, 'nome' => 'C2', 'tipo_corso' => 'Triennale']]);
        DB::table('insegnamento')->insert([['insegnamento_id' => 1, 'nome' => 'I1'], ['insegnamento_id' => 2, 'nome' => 'I2'], ['insegnamento_id' => 3, 'nome' => 'I3']]);
        DB::table('insegnamento_corso')->insert([
            ['ins_corso_id' => 1, 'corso_id' => 1, 'insegnamento_id' => 1, 'anno_di_corso' => 1, 'cfu' => 6, 'durata' => 1],
            ['ins_corso_id' => 2, 'corso_id' => 1, 'insegnamento_id' => 2, 'anno_di_corso' => 1, 'cfu' => 6, 'durata' => 1],
            ['ins_corso_id' => 3, 'corso_id' => 2, 'insegnamento_id' => 3, 'anno_di_corso' => 1, 'cfu' => 6, 'durata' => 1],
        ]);
        $this->insA = 1; $this->insB = 2; $this->insC = 3;
        DB::table('docente_insegnamento')->insert([
            ['ins_corso_id' => 1, 'utente_id' => $this->docA->utente_id, 'ruolo' => 'Primario'],
            ['ins_corso_id' => 2, 'utente_id' => $this->docB->utente_id, 'ruolo' => 'Primario'],
            ['ins_corso_id' => 3, 'utente_id' => $this->docB->utente_id, 'ruolo' => 'Primario'],
        ]);
        DB::table('edificio')->insert([['edificio_id' => 1, 'nome' => 'E1'], ['edificio_id' => 2, 'nome' => 'E2']]);
        DB::table('aula')->insert([['aula_id' => 1, 'nome' => 'A1', 'edificio_id' => 1, 'capienza' => 50], ['aula_id' => 2, 'nome' => 'A2', 'edificio_id' => 2, 'capienza' => 50]]);
        $this->aula1 = 1; $this->aula2 = 2;
    }

    private function creaSchema(): void
    {
        $s = Schema::connection('sqlite');
        $s->create('utente', function ($t) { $t->increments('utente_id'); $t->string('username'); $t->string('password'); $t->string('nome'); $t->string('cognome'); $t->date('data_nascita')->nullable(); $t->string('ruolo'); });
        $s->create('corso', function ($t) { $t->integer('corso_id')->primary(); $t->string('nome'); $t->string('tipo_corso'); });
        $s->create('insegnamento', function ($t) { $t->integer('insegnamento_id')->primary(); $t->string('nome'); });
        $s->create('insegnamento_corso', function ($t) { $t->integer('ins_corso_id')->primary(); $t->integer('corso_id'); $t->integer('insegnamento_id'); $t->integer('anno_di_corso'); $t->integer('cfu'); $t->integer('durata'); });
        $s->create('docente_insegnamento', function ($t) { $t->increments('doc_ins_id'); $t->integer('ins_corso_id'); $t->integer('utente_id'); $t->string('ruolo'); });
        $s->create('edificio', function ($t) { $t->integer('edificio_id')->primary(); $t->string('nome'); });
        $s->create('aula', function ($t) { $t->integer('aula_id')->primary(); $t->string('nome'); $t->integer('edificio_id'); $t->integer('capienza')->nullable(); });
        $s->create('sessione_esami', function ($t) { $t->increments('sessione_id'); $t->string('nome'); $t->integer('anno'); $t->integer('numero_appelli'); $t->date('data_inizio'); $t->date('data_fine'); });
        $s->create('appello', function ($t) { $t->increments('appello_id'); $t->integer('ins_corso_id'); $t->integer('sessione_id'); $t->date('data'); $t->string('ora_inizio'); $t->string('ora_fine'); $t->integer('aula_id'); });
    }

    private function payload(array $o = []): array
    {
        return array_merge([
            'sessione_id' => $this->sessione->sessione_id, 'ins_corso_id' => $this->insA,
            'data' => $this->lunedi, 'ora_inizio' => '09:00', 'ora_fine' => '12:00', 'aula_id' => $this->aula1,
        ], $o);
    }

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
        $this->actingAs($this->admin)->post('/admin/sessioni', ['nome' => 'Nuova', 'numero_appelli' => 3, 'data_inizio' => $inizio, 'data_fine' => $fine])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('sessione_esami', ['nome' => 'Nuova', 'anno' => Carbon::parse($inizio)->year]);
    }

    public function test_sessione_sovrapposta_rifiutata(): void
    {
        $this->actingAs($this->admin)->post('/admin/sessioni', ['nome' => 'Dup', 'numero_appelli' => 2, 'data_inizio' => $this->sessione->data_inizio, 'data_fine' => Carbon::parse($this->sessione->data_inizio)->addWeeks(3)->toDateString()])->assertSessionHasErrors('data_fine');
    }

    public function test_sessione_update_non_lascia_appelli_fuori_periodo_ne_riduce_sotto_il_presente(): void
    {
        Appello::create($this->payload(['ins_corso_id' => $this->insA]));
        Appello::create($this->payload(['ins_corso_id' => $this->insA, 'ora_inizio' => '14:00', 'ora_fine' => '16:00', 'aula_id' => $this->aula2]));
        $id = $this->sessione->sessione_id;
        $base = ['nome' => 'S', 'numero_appelli' => 2, 'data_inizio' => $this->lunedi, 'data_fine' => Carbon::parse($this->lunedi)->addWeeks(3)->toDateString()];

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
