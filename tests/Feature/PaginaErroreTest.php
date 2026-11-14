<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;

class PaginaErroreTest extends BaseDati
{
    private function assertPaginaErrore($risposta, int $stato): void
    {
        $risposta->assertStatus($stato)
            ->assertSee('Ops... si è verificato un errore!', false)
            ->assertSee('unibsLogo.png', false)
            ->assertSee('Torna alla schermata di accesso', false)
            ->assertSee('action="' . route('logout') . '"', false)
            ->assertSee('name="_token"', false);
    }

    public function test_pagina_inesistente_mostra_la_pagina_errore(): void
    {
        $this->assertPaginaErrore($this->actingAs($this->docA)->get('/non-esiste'), 404);
    }

    public function test_accesso_negato_mostra_la_pagina_errore(): void
    {
        $this->assertPaginaErrore($this->actingAs($this->docA)->get(route('admin.dashboard')), 403);
    }

    public function test_sessione_scaduta_419_e_errore_server_mostrano_la_pagina_errore(): void
    {
        Route::get('/_prova419', fn () => abort(419));
        Route::get('/_prova500', fn () => abort(500));

        $this->assertPaginaErrore($this->get('/_prova419'), 419);
        $this->assertPaginaErrore($this->get('/_prova500'), 500);
    }

    public function test_il_pulsante_fa_logout_e_porta_al_login(): void
    {
        $this->actingAs($this->docA)->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_richieste_json_restano_json(): void
    {
        $this->getJson('/non-esiste')->assertStatus(404)->assertJsonStructure(['message']);
    }
}
