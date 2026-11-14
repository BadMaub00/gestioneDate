<?php

namespace Tests\Feature;

use App\Models\Appello;
use App\Models\DocenteInsegnamento;
use App\Models\Sessione;
use Illuminate\Support\Facades\DB;

/** Copre le pagine e le regole spostate dai controller ai Model/Service. */
class StrutturaTest extends BaseDati
{
    private function appello(int $insCorsoId, ?Sessione $s = null, string $ora = '09:00'): Appello
    {
        $s ??= $this->sessione;

        return Appello::create([
            'ins_corso_id' => $insCorsoId, 'sessione_id' => $s->sessione_id, 'data' => $this->lunedi,
            'ora_inizio' => $ora, 'ora_fine' => '11:00', 'aula_id' => $this->aula1,
        ]);
    }

    public function test_calendario_admin_con_filtri_e_filtro_incoerente(): void
    {
        $this->appello($this->insA);

        $this->actingAs($this->admin)->get(route('admin.calendario'))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.calendario', ['sessione_id' => $this->sessione->sessione_id]))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.calendario', ['sessione_id' => $this->sessione->sessione_id, 'corso_id' => 2, 'anno_di_corso' => 1]))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.calendario', ['sessione_id' => 9999]))->assertOk();
    }

    public function test_calendari_docente_personale_e_generale_con_visibilita_differenziata(): void
    {
        $this->appello($this->insA);                       // di docA
        $this->appello($this->insB, null, '12:00');        // di docB (stesso anno e corso)

        $this->actingAs($this->docA)->get(route('docente.calendario', ['sessione_id' => $this->sessione->sessione_id]))->assertOk();

        $this->actingAs($this->docA)->get(route('docente.calendario.generale', [
            'sessione_id' => $this->sessione->sessione_id, 'annoCorso' => 1, 'corso_id' => 1,
        ]))->assertOk()->assertSee('Occupato')->assertSee('I1');

        // corso in cui il docente non insegna: vietato
        $this->actingAs($this->docA)->get(route('docente.calendario.generale', [
            'sessione_id' => $this->sessione2->sessione_id, 'annoCorso' => 1, 'corso_id' => 2,
        ]))->assertForbidden();

        $this->actingAs($this->docA)->get(route('docente.api.anniCorso', 1))->assertOk()->assertJson([1]);
        $this->actingAs($this->docA)->get(route('docente.api.anniCorso', 2))->assertOk()->assertJson([]);
    }

    public function test_pagine_insegnamento_del_docente(): void
    {
        $this->appello($this->insA);

        $this->actingAs($this->docA)->get(route('docente.dashboard'))->assertOk();
        $this->actingAs($this->docA)->get(route('docente.dettaglioInsegnamento', $this->insA))->assertOk();
        $this->actingAs($this->docA)->get(route('docente.appelliInsegnamento', $this->insA))->assertOk();
        // insegnamento di un altro docente
        $this->actingAs($this->docA)->get(route('docente.dettaglioInsegnamento', $this->insB))->assertNotFound();
    }

    public function test_associazione_docente_regole_nel_model(): void
    {
        $post = fn ($u, $ins, $ruolo) => $this->actingAs($this->admin)
            ->post(route('admin.personale.storeAssocia'), ['ins_corso_id' => $ins, 'utente_id' => $u, 'ruolo' => $ruolo]);

        $post($this->docA->utente_id, $this->insA, 'Primario')->assertSessionHasErrors('utente_id');    // già associato
        $post($this->docB->utente_id, $this->insA, 'Primario')->assertSessionHasErrors('ruolo');        // secondo primario
        $post($this->admin->utente_id, $this->insA, 'Secondario')->assertSessionHasErrors('utente_id'); // non docente
        $post($this->docB->utente_id, $this->insA, 'Secondario')->assertSessionHasNoErrors()->assertRedirect();

        $this->assertTrue(DocenteInsegnamento::where('ins_corso_id', $this->insA)->where('utente_id', $this->docB->utente_id)->exists());

        $this->actingAs($this->admin)->get(route('admin.personale.associazioni_index', ['corso_id' => 1, 'docente_id' => $this->docB->utente_id]))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.personale.associa'))->assertOk();
    }

    public function test_piano_di_studi_admin(): void
    {
        $this->actingAs($this->admin)->get(route('admin.corsi.piano_di_studi', 1))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.corsi.insegnamenti.create', 1))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.corsi.insegnamenti.edit', $this->insA))->assertOk();

        // già nel piano
        $this->actingAs($this->admin)->post(route('admin.corsi.insegnamenti.store', 1), [
            'insegnamento_id' => 1, 'anno_di_corso' => 1, 'cfu' => 6, 'durata' => 1,
        ])->assertSessionHasErrors('insegnamento_id');

        // nuovo
        $this->actingAs($this->admin)->post(route('admin.corsi.insegnamenti.store', 1), [
            'insegnamento_id' => 3, 'anno_di_corso' => 2, 'cfu' => 6, 'durata' => 1,
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('insegnamento_corso', ['corso_id' => 1, 'insegnamento_id' => 3]);

        $this->actingAs($this->admin)->put(route('admin.corsi.insegnamenti.update', $this->insA), [
            'anno_di_corso' => 2, 'cfu' => 9, 'durata' => 1,
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('insegnamento_corso', ['ins_corso_id' => $this->insA, 'cfu' => 9]);

        // sessione conclusa con appelli: non si può rimuovere dal piano
        $passata = Sessione::create(['corso_id' => 1, 'nome' => 'Vecchia', 'anno' => 2020, 'numero_appelli' => 2, 'data_inizio' => '2020-01-06', 'data_fine' => '2020-01-31']);
        DB::table('appello')->insert(['ins_corso_id' => $this->insA, 'sessione_id' => $passata->sessione_id, 'data' => '2020-01-13', 'ora_inizio' => '09:00', 'ora_fine' => '11:00', 'aula_id' => $this->aula1]);

        $this->actingAs($this->admin)->delete(route('admin.corsi.insegnamenti.destroy', $this->insA))->assertSessionHas('error');
        $this->assertDatabaseHas('insegnamento_corso', ['ins_corso_id' => $this->insA]);
    }

    public function test_corsi_crud_admin(): void
    {
        $this->actingAs($this->admin)->post(route('admin.corsi.store'), ['nome' => 'Nuovo', 'tipo_corso' => 'Magistrale'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('corso', ['nome' => 'Nuovo']);
        $this->actingAs($this->admin)->post(route('admin.corsi.store'), ['nome' => 'X', 'tipo_corso' => 'Boh'])->assertSessionHasErrors('tipo_corso');
        $this->actingAs($this->admin)->get(route('admin.corsi.index'))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.dashboard'))->assertOk();
    }
}
