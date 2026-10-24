<?php

namespace Tests\Feature;

use App\Models\Corso;
use App\Models\DocenteInsegnamento;
use App\Models\Insegnamento;
use App\Models\InsegnamentiCorso;
use App\Services\ImportStrutturaDidattica;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Richiede phpoffice/phpspreadsheet (composer require phpoffice/phpspreadsheet).
 * Nel database di prova: corsi C1, C2, C3 (Triennale); insegnamenti I1, I2, I3;
 * docenti con username "a" e "b"; amministratore "ad".
 */
class ImportExcelTest extends BaseDati
{
    private const H_CORSI = ['Nome corso', 'Tipo corso'];
    private const H_PIANO = ['Corso', 'Tipo corso', 'Insegnamento', 'Anno di corso', 'CFU', 'Ore'];
    private const H_ASSOC = ['Corso', 'Tipo corso', 'Insegnamento', 'Username docente', 'Ruolo'];

    private array $temp = [];

    protected function setUp(): void
    {
        parent::setUp();
        if (!class_exists(Spreadsheet::class)) {
            $this->markTestSkipped('phpoffice/phpspreadsheet non installato.');
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->temp as $f) {
            @unlink($f);
        }
        parent::tearDown();
    }

    /** Crea un .xlsx da ['Nome foglio' => [riga intestazioni, riga1, ...]] */
    private function xlsx(array $fogli): string
    {
        $s = new Spreadsheet();
        $s->removeSheetByIndex(0);
        $i = 0;
        foreach ($fogli as $nome => $righe) {
            $ws = $s->createSheet($i++);
            $ws->setTitle($nome);
            $ws->fromArray($righe, null, 'A1');
        }
        $path = tempnam(sys_get_temp_dir(), 'imp') . '.xlsx';
        (new Xlsx($s))->save($path);

        return $this->temp[] = $path;
    }

    private function carica(string $path, bool $soloVerifica = false)
    {
        $file = new UploadedFile($path, 'import.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        return $this->actingAs($this->admin)->post('/admin/import', ['file' => $file, 'solo_verifica' => $soloVerifica ? 1 : 0]);
    }

    private function report(): array
    {
        return session('import_report');
    }

    private function fileValido(): string
    {
        return $this->xlsx([
            'Corsi' => [self::H_CORSI, ['Lettere', 'Triennale'], ['Fisica', 'Magistrale']],
            'Piano di studi' => [self::H_PIANO,
                ['Lettere', 'Triennale', 'Storia', 1, 9, 72],
                ['Lettere', null, 'Latino', '2', 6.0, 48],                // tipo omesso, numeri come testo/decimale
                ['Fisica', 'Magistrale', 'I1', 1, 6, 48],                // insegnamento già esistente: non viene duplicato
                ['C1', 'Triennale', 'I1', 2, 9, 72],                     // già nel piano: viene aggiornato
            ],
            'Associazioni docenti' => [self::H_ASSOC,
                ['Lettere', 'Triennale', 'Storia', 'a', 'Primario'],
                ['Lettere', null, 'Storia', 'B', 'secondario'],          // username e ruolo senza badare alle maiuscole
                ['Lettere', null, 'Latino', 'a', 'Primario'],            // stesso docente, anno diverso: consentito
            ],
        ]);
    }

    // ------------------------------------------------------------------ accesso e modello

    public function test_solo_l_admin_accede_alla_pagina_e_all_importazione(): void
    {
        $this->actingAs($this->admin)->get('/admin/import')->assertOk()->assertSee('Importa da Excel')->assertDontSee('Libreria Excel non installata');
        $this->actingAs($this->docA)->get('/admin/import')->assertForbidden();
        $this->actingAs($this->docA)->get('/admin/import/modello')->assertForbidden();

        $file = new UploadedFile($this->fileValido(), 'import.xlsx', null, null, true);
        $this->actingAs($this->docA)->post('/admin/import', ['file' => $file])->assertForbidden();
        $this->assertSame(3, Corso::count());
    }

    public function test_la_dashboard_ha_il_link_all_importazione(): void
    {
        $this->actingAs($this->admin)->get('/admin/dashboard')->assertOk()->assertSee(route('admin.import.index'), false);
    }

    public function test_modello_scaricabile_con_fogli_e_intestazioni(): void
    {
        $r = $this->actingAs($this->admin)->get('/admin/import/modello')->assertOk();
        $this->assertStringContainsString('.xlsx', $r->headers->get('content-disposition'));

        $path = tempnam(sys_get_temp_dir(), 'mod') . '.xlsx';
        $this->temp[] = $path;
        file_put_contents($path, $r->streamedContent());

        $excel = IOFactory::load($path);
        $this->assertSame(['Corsi', 'Piano di studi', 'Associazioni docenti', 'Istruzioni'], $excel->getSheetNames());
        $this->assertSame(self::H_CORSI, array_slice($excel->getSheetByName('Corsi')->toArray()[0], 0, 2));
        $this->assertSame(self::H_PIANO, array_slice($excel->getSheetByName('Piano di studi')->toArray()[0], 0, 6));
        $this->assertSame(self::H_ASSOC, array_slice($excel->getSheetByName('Associazioni docenti')->toArray()[0], 0, 5));
    }

    public function test_il_modello_vuoto_e_accettato_senza_errori(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'mod') . '.xlsx';
        $this->temp[] = $path;
        (new Xlsx(app(ImportStrutturaDidattica::class)->modello()))->save($path);

        $this->carica($path)->assertRedirect('/admin/import');
        $this->assertTrue($this->report()['ok']);
        $this->assertSame(3, Corso::count());
    }

    // ------------------------------------------------------------------ importazione valida

    public function test_importazione_completa_crea_aggiorna_e_non_duplica(): void
    {
        $this->carica($this->fileValido())->assertRedirect('/admin/import');
        $r = $this->report();

        $this->assertTrue($r['ok'], implode(' | ', $r['errori']));
        $this->assertTrue($r['applicato']);
        $this->assertSame(['creati' => 2, 'esistenti' => 0], $r['riepilogo']['corsi']);
        $this->assertSame(['creati' => 2], $r['riepilogo']['insegnamenti']);             // Storia, Latino (I1 esiste già)
        $this->assertSame(['creati' => 3, 'aggiornati' => 1, 'invariati' => 0], $r['riepilogo']['piano']);
        $this->assertSame(['create' => 3, 'aggiornate' => 0, 'invariate' => 0], $r['riepilogo']['associazioni']);

        $lettere = Corso::where('nome', 'Lettere')->first();
        $this->assertSame('Triennale', $lettere->tipo_corso);
        $this->assertSame('Magistrale', Corso::where('nome', 'Fisica')->value('tipo_corso'));
        $this->assertSame(5, Insegnamento::count());                                      // 3 + Storia + Latino

        $storia = InsegnamentiCorso::where('corso_id', $lettere->corso_id)->where('insegnamento_id', Insegnamento::where('nome', 'Storia')->value('insegnamento_id'))->first();
        $this->assertSame([1, 9, 72], [(int) $storia->anno_di_corso, (int) $storia->cfu, (int) $storia->durata]);
        $latino = InsegnamentiCorso::where('corso_id', $lettere->corso_id)->where('insegnamento_id', Insegnamento::where('nome', 'Latino')->value('insegnamento_id'))->first();
        $this->assertSame([2, 6, 48], [(int) $latino->anno_di_corso, (int) $latino->cfu, (int) $latino->durata]);

        // piano esistente aggiornato (insA = I1 nel corso 1)
        $esistente = InsegnamentiCorso::findOrFail($this->insA);
        $this->assertSame([2, 9, 72], [(int) $esistente->anno_di_corso, (int) $esistente->cfu, (int) $esistente->durata]);

        // associazioni
        $this->assertSame('Primario', DocenteInsegnamento::where('ins_corso_id', $storia->ins_corso_id)->where('utente_id', $this->docA->utente_id)->value('ruolo'));
        $this->assertSame('Secondario', DocenteInsegnamento::where('ins_corso_id', $storia->ins_corso_id)->where('utente_id', $this->docB->utente_id)->value('ruolo'));
    }

    public function test_reimportare_lo_stesso_file_non_crea_doppioni(): void
    {
        $file = $this->fileValido();
        $this->carica($file);
        $prima = [Corso::count(), Insegnamento::count(), InsegnamentiCorso::count(), DocenteInsegnamento::count()];

        $this->carica($file);
        $r = $this->report();

        $this->assertTrue($r['ok'], implode(' | ', $r['errori']));
        $this->assertSame($prima, [Corso::count(), Insegnamento::count(), InsegnamentiCorso::count(), DocenteInsegnamento::count()]);
        $this->assertSame(['creati' => 0, 'esistenti' => 2], $r['riepilogo']['corsi']);
        $this->assertSame(['creati' => 0], $r['riepilogo']['insegnamenti']);
        $this->assertSame(['creati' => 0, 'aggiornati' => 0, 'invariati' => 4], $r['riepilogo']['piano']);
        $this->assertSame(['create' => 0, 'aggiornate' => 0, 'invariate' => 3], $r['riepilogo']['associazioni']);
    }

    public function test_solo_verifica_non_salva_nulla(): void
    {
        $this->carica($this->fileValido(), true);
        $r = $this->report();

        $this->assertTrue($r['ok']);
        $this->assertFalse($r['applicato']);
        $this->assertSame(2, $r['riepilogo']['corsi']['creati']);
        $this->assertSame(3, Corso::count());
        $this->assertSame(3, Insegnamento::count());
        $this->assertSame(0, Corso::where('nome', 'Lettere')->count());
    }

    public function test_l_associazione_cambia_ruolo_se_gia_presente(): void
    {
        // nel database: docA è Primario di I1 nel corso C1 (insA)
        $file = $this->xlsx(['Associazioni docenti' => [self::H_ASSOC, ['C1', 'Triennale', 'I1', 'a', 'Secondario']]]);
        $this->carica($file);

        $this->assertTrue($this->report()['ok']);
        $this->assertSame(1, $this->report()['riepilogo']['associazioni']['aggiornate']);
        $this->assertSame('Secondario', DocenteInsegnamento::where('ins_corso_id', $this->insA)->where('utente_id', $this->docA->utente_id)->value('ruolo'));
    }

    // ------------------------------------------------------------------ errori: niente viene salvato

    public function test_con_errori_non_viene_salvato_nulla_e_gli_errori_indicano_foglio_e_riga(): void
    {
        $file = $this->xlsx([
            'Corsi' => [self::H_CORSI,
                ['', 'Triennale'],                 // riga 2
                ['Nuovo', 'Boh'],                  // riga 3
                ['Dup', 'Triennale'],              // riga 4 (ok)
                ['dup ', 'triennale'],             // riga 5 duplicato
            ],
            'Piano di studi' => [self::H_PIANO,
                ['C1', 'Triennale', 'I9', 4, 6, 48],           // riga 2: anno oltre la durata
                ['C1', 'Triennale', 'I8', 1, 40, 48],          // riga 3: cfu
                ['Inesistente', null, 'X', 1, 6, 48],          // riga 4: corso inesistente
                ['C1', 'Triennale', 'I7', 1, 6, 0],            // riga 5: ore
                ['C1', 'Triennale', 'I6', 'uno', 6, 48],       // riga 6: anno non numerico
            ],
            'Associazioni docenti' => [self::H_ASSOC,
                ['C1', 'Triennale', 'I3', 'a', 'Secondario'],        // riga 2: I3 non è nel piano di C1
                ['C1', 'Triennale', 'I1', 'nessuno', 'Secondario'],  // riga 3: docente inesistente
                ['C1', 'Triennale', 'I1', 'ad', 'Secondario'],       // riga 4: non è un docente
                ['C1', 'Triennale', 'I1', 'b', 'Boss'],              // riga 5: ruolo non valido
            ],
        ]);

        $assocPrima = DocenteInsegnamento::count();

        $this->carica($file)->assertRedirect('/admin/import');
        $r = $this->report();
        $testo = implode("\n", $r['errori']);

        $this->assertFalse($r['ok']);
        $this->assertFalse($r['applicato']);
        $this->assertNull($r['riepilogo']);
        foreach ([
            'Foglio «Corsi», riga 2: il nome del corso è obbligatorio',
            'Foglio «Corsi», riga 3: tipo di corso non valido',
            'Foglio «Corsi», riga 5: il corso «dup» (Triennale) è già presente alla riga 4',
            'Foglio «Piano di studi», riga 2: l\'anno di corso deve essere un numero da 1 a 3',
            'Foglio «Piano di studi», riga 3: i CFU',
            'Foglio «Piano di studi», riga 4: corso «Inesistente» non trovato',
            'Foglio «Piano di studi», riga 5: le ore',
            'Foglio «Piano di studi», riga 6: l\'anno di corso',
            'Foglio «Associazioni docenti», riga 2: l\'insegnamento «I3» non fa parte del piano di studi',
            'Foglio «Associazioni docenti», riga 3: il docente «nessuno» non esiste',
            'Foglio «Associazioni docenti», riga 4: l\'utente «ad» non ha il ruolo Docente',
            'Foglio «Associazioni docenti», riga 5: ruolo non valido',
        ] as $atteso) {
            $this->assertStringContainsString($atteso, $testo);
        }

        // nessuna scrittura nel database
        $this->assertSame(3, Corso::count());
        $this->assertSame(3, Insegnamento::count());
        $this->assertSame(3, InsegnamentiCorso::count());
        $this->assertSame($assocPrima, DocenteInsegnamento::count());
    }

    public function test_un_errore_in_un_solo_foglio_blocca_anche_i_fogli_corretti(): void
    {
        $file = $this->xlsx([
            'Corsi' => [self::H_CORSI, ['Lettere', 'Triennale']],                     // corretto
            'Piano di studi' => [self::H_PIANO, ['Lettere', 'Triennale', 'Storia', 9, 9, 72]],   // anno non valido
        ]);
        $this->carica($file);

        $this->assertFalse($this->report()['ok']);
        $this->assertSame(0, Corso::where('nome', 'Lettere')->count());
    }

    public function test_regola_stesso_anno_stesso_corso_per_il_docente_primario(): void
    {
        // nel database docA è già Primario di I1 (C1, anno 1) -> non può esserlo anche di un altro insegnamento di C1 anno 1
        $file = $this->xlsx([
            'Piano di studi' => [self::H_PIANO, ['C1', 'Triennale', 'Nuova materia', 1, 6, 48]],
            'Associazioni docenti' => [self::H_ASSOC, ['C1', 'Triennale', 'Nuova materia', 'a', 'Primario']],
        ]);
        $this->carica($file);
        $r = $this->report();

        $this->assertFalse($r['ok']);
        $this->assertStringContainsString('sarebbe primario di più insegnamenti dell\'anno 1 del corso «C1»', implode("\n", $r['errori']));
        $this->assertSame(3, Insegnamento::count());

        // da Secondario, invece, va bene
        $file = $this->xlsx([
            'Piano di studi' => [self::H_PIANO, ['C1', 'Triennale', 'Nuova materia', 1, 6, 48]],
            'Associazioni docenti' => [self::H_ASSOC, ['C1', 'Triennale', 'Nuova materia', 'a', 'Secondario']],
        ]);
        $this->carica($file);
        $this->assertTrue($this->report()['ok'], implode(' | ', $this->report()['errori']));
    }

    public function test_un_solo_primario_per_insegnamento(): void
    {
        // due Primari nel file per lo stesso insegnamento
        $file = $this->xlsx([
            'Corsi' => [self::H_CORSI, ['Z', 'Triennale']],
            'Piano di studi' => [self::H_PIANO, ['Z', null, 'Mat', 1, 6, 48]],
            'Associazioni docenti' => [self::H_ASSOC, ['Z', null, 'Mat', 'a', 'Primario'], ['Z', null, 'Mat', 'b', 'Primario']],
        ]);
        $this->carica($file);
        $this->assertFalse($this->report()['ok']);
        $this->assertStringContainsString('più di un docente primario', implode("\n", $this->report()['errori']));

        // un secondo Primario rispetto a quello già presente nel database (I2 ha già b come Primario)
        $file = $this->xlsx(['Associazioni docenti' => [self::H_ASSOC, ['C1', 'Triennale', 'I2', 'a', 'Primario']]]);
        $this->carica($file);
        $this->assertFalse($this->report()['ok']);
        $this->assertStringContainsString('più di un docente primario', implode("\n", $this->report()['errori']));
        $this->assertSame(1, DocenteInsegnamento::where('ins_corso_id', $this->insB)->where('ruolo', 'Primario')->count());
    }

    public function test_corso_con_lo_stesso_nome_in_due_tipi_richiede_il_tipo(): void
    {
        Corso::create(['nome' => 'C1', 'tipo_corso' => 'Magistrale']);

        $file = $this->xlsx(['Piano di studi' => [self::H_PIANO, ['C1', null, 'Nuova', 1, 6, 48]]]);
        $this->carica($file);
        $this->assertFalse($this->report()['ok']);
        $this->assertStringContainsString('esiste in più tipi', implode("\n", $this->report()['errori']));

        $file = $this->xlsx(['Piano di studi' => [self::H_PIANO, ['C1', 'Magistrale', 'Nuova', 2, 6, 48]]]);
        $this->carica($file);
        $this->assertTrue($this->report()['ok'], implode(' | ', $this->report()['errori']));
    }

    public function test_anno_massimo_dipende_dal_tipo_di_corso(): void
    {
        $file = $this->xlsx([
            'Corsi' => [self::H_CORSI, ['Mag', 'Magistrale'], ['CicloUnico', 'Magistrale a ciclo unico']],
            'Piano di studi' => [self::H_PIANO, ['Mag', null, 'A', 3, 6, 48], ['CicloUnico', null, 'B', 5, 6, 48], ['CicloUnico', null, 'C', 6, 6, 48]],
        ]);
        $this->carica($file);
        $testo = implode("\n", $this->report()['errori']);

        $this->assertFalse($this->report()['ok']);
        $this->assertStringContainsString('riga 2: l\'anno di corso deve essere un numero da 1 a 2 (corso Magistrale)', $testo);
        $this->assertStringContainsString('riga 4: l\'anno di corso deve essere un numero da 1 a 5', $testo);
        $this->assertStringNotContainsString('riga 3:', $testo);
    }

    // ------------------------------------------------------------------ file non valido

    public function test_file_senza_fogli_riconosciuti_o_senza_colonne(): void
    {
        $this->carica($this->xlsx(['Altro' => [['x'], ['y']]]));
        $this->assertFalse($this->report()['ok']);
        $this->assertStringContainsString('Nessun foglio riconosciuto', $this->report()['errori'][0]);

        $this->carica($this->xlsx(['Corsi' => [['Nome corso'], ['Lettere']]]));
        $this->assertFalse($this->report()['ok']);
        $this->assertStringContainsString('manca la colonna «Tipo corso»', implode("\n", $this->report()['errori']));
    }

    public function test_file_che_non_e_un_excel_viene_rifiutato(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'txt') . '.txt';
        file_put_contents($path, "Nome corso;Tipo corso\nLettere;Triennale\n");
        $this->temp[] = $path;
        $file = new UploadedFile($path, 'corsi.txt', 'text/plain', null, true);

        $this->actingAs($this->admin)->post('/admin/import', ['file' => $file])->assertSessionHasErrors('file');
        $this->actingAs($this->admin)->post('/admin/import', [])->assertSessionHasErrors('file');
        $this->assertSame(3, Corso::count());
    }

    // ------------------------------------------------------------------ correzione collegata

    public function test_associare_un_docente_dall_area_admin_non_va_piu_in_errore(): void
    {
        // la rotta di destinazione 'admin.personale.associazioni_index.index' non esisteva
        $this->actingAs($this->admin)->post('/admin/personale/associa', [
            'ins_corso_id' => $this->insC, 'utente_id' => $this->docA->utente_id, 'ruolo' => 'Secondario',
        ])->assertRedirect('/admin/personale/associazioni');
    }
}
