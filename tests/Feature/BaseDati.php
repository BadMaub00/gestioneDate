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

abstract class BaseDati extends TestCase
{
    protected Sessione $sessione;      // sessione del corso 1
    protected Sessione $sessione2;     // sessione del corso 2 (stesso periodo)
    protected Utente $docA;
    protected Utente $docB;
    protected Utente $admin;
    protected int $insA;   // corso 1, anno 1, di docA
    protected int $insB;   // corso 1, anno 1, di docB
    protected int $insC;   // corso 2, anno 1, di docB
    protected int $aula1;
    protected int $aula2;
    protected string $lunedi;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        $this->creaSchema();

        $this->lunedi = Carbon::today()->addMonths(2)->next(Carbon::MONDAY)->toDateString();
        $fine = Carbon::parse($this->lunedi)->addWeeks(3)->toDateString();

        $this->sessione = Sessione::create(['corso_id' => 1, 'nome' => 'S', 'anno' => 2026, 'numero_appelli' => 2, 'data_inizio' => $this->lunedi, 'data_fine' => $fine]);
        $this->sessione2 = Sessione::create(['corso_id' => 2, 'nome' => 'S', 'anno' => 2026, 'numero_appelli' => 2, 'data_inizio' => $this->lunedi, 'data_fine' => $fine]);

        $this->docA = Utente::create(['username' => 'a', 'password' => 'x', 'nome' => 'A', 'cognome' => 'A', 'data_nascita' => '1980-01-01', 'ruolo' => 'Docente']);
        $this->docB = Utente::create(['username' => 'b', 'password' => 'x', 'nome' => 'B', 'cognome' => 'B', 'data_nascita' => '1980-01-01', 'ruolo' => 'Docente']);
        $this->admin = Utente::create(['username' => 'ad', 'password' => 'x', 'nome' => 'Ad', 'cognome' => 'Ad', 'data_nascita' => '1980-01-01', 'ruolo' => 'Amministratore']);

        DB::table('corso')->insert([['corso_id' => 1, 'nome' => 'C1', 'tipo_corso' => 'Triennale'], ['corso_id' => 2, 'nome' => 'C2', 'tipo_corso' => 'Triennale'], ['corso_id' => 3, 'nome' => 'C3', 'tipo_corso' => 'Triennale']]);
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

    protected function creaSchema(): void
    {
        $s = Schema::connection('sqlite');
        $s->create('utente', function ($t) { $t->increments('utente_id'); $t->string('username'); $t->string('password'); $t->string('nome'); $t->string('cognome'); $t->date('data_nascita')->nullable(); $t->string('ruolo'); });
        $s->create('corso', function ($t) { $t->integer('corso_id')->primary(); $t->string('nome'); $t->string('tipo_corso'); });
        $s->create('insegnamento', function ($t) { $t->integer('insegnamento_id')->primary(); $t->string('nome'); });
        $s->create('insegnamento_corso', function ($t) { $t->integer('ins_corso_id')->primary(); $t->integer('corso_id'); $t->integer('insegnamento_id'); $t->integer('anno_di_corso'); $t->integer('cfu'); $t->integer('durata'); });
        $s->create('docente_insegnamento', function ($t) { $t->increments('doc_ins_id'); $t->integer('ins_corso_id'); $t->integer('utente_id'); $t->string('ruolo'); });
        $s->create('edificio', function ($t) { $t->integer('edificio_id')->primary(); $t->string('nome'); });
        $s->create('aula', function ($t) { $t->integer('aula_id')->primary(); $t->string('nome'); $t->integer('edificio_id'); $t->integer('capienza')->nullable(); });
        $s->create('sessione_esami', function ($t) { $t->increments('sessione_id'); $t->integer('corso_id'); $t->string('nome'); $t->integer('anno'); $t->integer('numero_appelli'); $t->date('data_inizio'); $t->date('data_fine'); });
        $s->create('appello', function ($t) { $t->increments('appello_id'); $t->integer('ins_corso_id'); $t->integer('sessione_id'); $t->date('data'); $t->string('ora_inizio'); $t->string('ora_fine'); $t->integer('aula_id'); });
    }

    protected function payload(array $o = []): array
    {
        $insCorsoId = $o['ins_corso_id'] ?? $this->insA;
        $sessione = $insCorsoId === $this->insC ? $this->sessione2 : $this->sessione;

        return array_merge([
            'sessione_id' => $sessione->sessione_id, 'ins_corso_id' => $insCorsoId,
            'data' => $this->lunedi, 'ora_inizio' => '09:00', 'ora_fine' => '12:00', 'aula_id' => $this->aula1,
        ], $o);
    }
}
