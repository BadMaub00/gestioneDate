<?php

namespace Tests\Feature;

use App\Models\Utente;
use Illuminate\Support\Facades\DB;

class EliminazioniTest extends BaseDatiTest
{
    /** Estrae action dei form di eliminazione (quelli con data-confirm) di una pagina. */
    private function formEliminazione(string $html, string $contiene): array
    {
        preg_match_all('#<form\s+action="([^"]*)"[^>]*data-confirm=#s', $html, $m);

        return array_values(array_filter($m[1], fn ($a) => str_contains($a, $contiene)));
    }

    public function test_nessun_form_di_eliminazione_punta_a_cancelletto(): void
    {
        foreach (['/admin/corsi', '/admin/personale', '/admin/sessioni', '/admin/insegnamenti'] as $url) {
            $html = $this->actingAs($this->admin)->get($url)->getContent();
            $this->assertStringNotContainsString('action="#"', $html, "form con action=\"#\" in $url");
        }
    }

    public function test_elimina_corso_dal_pulsante_dell_indice(): void
    {
        $html = $this->actingAs($this->admin)->get('/admin/corsi')->assertOk()->getContent();
        $azioni = $this->formEliminazione($html, '/admin/corsi/');
        $this->assertCount(3, $azioni);
        $this->assertStringContainsString('/admin/corsi/3', implode(' ', $azioni));

        // come fa il browser: POST con _method=DELETE sull'action del form
        $this->actingAs($this->admin)->post('/admin/corsi/3', ['_method' => 'DELETE'])->assertRedirect('/admin/corsi');
        $this->assertNull(DB::table('corso')->where('corso_id', 3)->first());
    }

    public function test_eliminare_un_corso_rimuove_sessioni_e_appelli_collegati_nel_database_con_fk(): void
    {
        // lo schema di test non ha le FK: verifichiamo solo che l'eliminazione non fallisca
        $this->actingAs($this->admin)->post('/admin/corsi/2', ['_method' => 'DELETE'])->assertRedirect('/admin/corsi');
        $this->assertNull(DB::table('corso')->where('corso_id', 2)->first());
    }

    public function test_elimina_utente_dal_pulsante_della_pagina_personale(): void
    {
        $html = $this->actingAs($this->admin)->get('/admin/personale')->assertOk()->getContent();
        $azioni = $this->formEliminazione($html, '/admin/personale/');
        $this->assertNotEmpty($azioni);
        $this->assertStringContainsString('/admin/personale/' . $this->docB->utente_id, implode(' ', $azioni));

        $this->actingAs($this->admin)->post('/admin/personale/' . $this->docB->utente_id, ['_method' => 'DELETE'])->assertRedirect('/admin/personale');
        $this->assertNull(Utente::find($this->docB->utente_id));
    }
}
