<?php

namespace App\Services;

use App\Models\Corso;
use App\Models\DocenteInsegnamento;
use App\Models\Insegnamento;
use App\Models\InsegnamentiCorso;
use App\Models\Utente;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Throwable;

/**
 * Importazione della struttura didattica da un file Excel (.xlsx).
 *
 * Fogli riconosciuti (anche solo alcuni):
 *   - "Corsi":                 Nome corso | Tipo corso
 *   - "Piano di studi":        Corso | Tipo corso | Insegnamento | Anno di corso | CFU | Ore
 *   - "Associazioni docenti":  Corso | Tipo corso | Insegnamento | Username docente | Ruolo
 *
 * Prima si legge e si controlla TUTTO il file con le stesse regole dell'interfaccia admin;
 * solo se non ci sono errori i dati vengono salvati, in un'unica transazione.
 * Si può reimportare lo stesso file: ciò che esiste già viene riconosciuto e non duplicato.
 */
class ImportStrutturaDidattica
{
    public const FOGLIO_CORSI = 'Corsi';
    public const FOGLIO_PIANO = 'Piano di studi';
    public const FOGLIO_ASSOC = 'Associazioni docenti';
    public const FOGLIO_ISTRUZIONI = 'Istruzioni';

    public const TIPI = ['Triennale', 'Magistrale', 'Magistrale a ciclo unico'];
    public const RUOLI = ['Primario', 'Secondario'];

    private const MAX_RIGHE = 5000;
    private const MAX_ERRORI = 100;

    /** Intestazioni attese: campo => [titolo mostrato, alias accettati, obbligatoria] */
    private const COLONNE = [
        self::FOGLIO_CORSI => [
            'nome' => ['Nome corso', ['nome corso', 'corso', 'nome'], true],
            'tipo' => ['Tipo corso', ['tipo corso', 'tipo'], true],
        ],
        self::FOGLIO_PIANO => [
            'corso' => ['Corso', ['corso', 'nome corso'], true],
            'tipo' => ['Tipo corso', ['tipo corso', 'tipo'], false],
            'insegnamento' => ['Insegnamento', ['insegnamento', 'nome insegnamento'], true],
            'anno' => ['Anno di corso', ['anno di corso', 'anno'], true],
            'cfu' => ['CFU', ['cfu'], true],
            'ore' => ['Ore', ['ore', 'durata', 'ore di lezione'], true],
        ],
        self::FOGLIO_ASSOC => [
            'corso' => ['Corso', ['corso', 'nome corso'], true],
            'tipo' => ['Tipo corso', ['tipo corso', 'tipo'], false],
            'insegnamento' => ['Insegnamento', ['insegnamento', 'nome insegnamento'], true],
            'username' => ['Username docente', ['username docente', 'username', 'docente'], true],
            'ruolo' => ['Ruolo', ['ruolo'], true],
        ],
    ];

    /** @var string[] */
    private array $errori = [];
    private int $erroriOmessi = 0;

    // ------------------------------------------------------------------ utilità

    public static function durataAnni(string $tipo): int
    {
        return match ($tipo) {
            'Magistrale' => 2,
            'Magistrale a ciclo unico' => 5,
            default => 3,
        };
    }

    private function norm(?string $s): string
    {
        return mb_strtolower(trim(preg_replace('/\s+/u', ' ', (string) $s)));
    }

    private function tipoCanonico(?string $s): ?string
    {
        $n = $this->norm($s);
        foreach (self::TIPI as $t) {
            if ($this->norm($t) === $n) {
                return $t;
            }
        }

        return null;
    }

    private function ruoloCanonico(?string $s): ?string
    {
        $n = $this->norm($s);
        foreach (self::RUOLI as $r) {
            if ($this->norm($r) === $n) {
                return $r;
            }
        }

        return null;
    }

    private function intero($v): ?int
    {
        if (is_int($v)) {
            return $v;
        }
        if (is_float($v) && floor($v) == $v) {
            return (int) $v;
        }
        if (is_string($v) && preg_match('/^\s*\d+\s*$/', $v)) {
            return (int) trim($v);
        }

        return null;
    }

    private function errore(string $foglio, ?int $riga, string $msg): void
    {
        if (count($this->errori) >= self::MAX_ERRORI) {
            $this->erroriOmessi++;

            return;
        }
        $this->errori[] = ($riga ? "Foglio «{$foglio}», riga {$riga}: " : "Foglio «{$foglio}»: ") . $msg;
    }

    // ------------------------------------------------------------------ lettura

    /**
     * @return array<string, array<int, array<string, mixed>>> foglio => [riga => [campo => valore]]
     */
    private function leggi(string $percorso): array
    {
        $righe = [];

        try {
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $excel = $reader->load($percorso);
        } catch (Throwable $e) {
            $this->errori[] = 'Il file non è un Excel (.xlsx) valido o è danneggiato.';

            return [];
        }

        $perNome = [];
        foreach ($excel->getWorksheetIterator() as $ws) {
            $perNome[$this->norm($ws->getTitle())] = $ws;
        }

        foreach (self::COLONNE as $foglio => $colonne) {
            $ws = $perNome[$this->norm($foglio)] ?? null;
            if (!$ws) {
                continue;
            }
            if ($ws->getHighestDataRow() > self::MAX_RIGHE + 1) {
                $this->errore($foglio, null, 'troppe righe (massimo ' . self::MAX_RIGHE . ').');
                continue;
            }

            $tabella = $ws->toArray(null, true, false, false);
            if (!$tabella) {
                $righe[$foglio] = [];
                continue;
            }

            // intestazioni
            $mappa = [];
            foreach ($tabella[0] as $i => $titolo) {
                $mappa[$this->norm((string) $titolo)] = $i;
            }
            $colIdx = [];
            $ok = true;
            foreach ($colonne as $campo => [$titolo, $alias, $obbligatoria]) {
                $trovata = null;
                foreach ($alias as $a) {
                    if (array_key_exists($a, $mappa)) {
                        $trovata = $mappa[$a];
                        break;
                    }
                }
                if ($trovata === null && $obbligatoria) {
                    $this->errore($foglio, 1, "manca la colonna «{$titolo}».");
                    $ok = false;
                }
                $colIdx[$campo] = $trovata;
            }
            if (!$ok) {
                continue;
            }

            $righe[$foglio] = [];
            for ($r = 1; $r < count($tabella); $r++) {
                $dati = [];
                $vuota = true;
                foreach ($colIdx as $campo => $idx) {
                    $v = $idx === null ? null : ($tabella[$r][$idx] ?? null);
                    if (is_string($v)) {
                        $v = trim($v);
                        if ($v === '') {
                            $v = null;
                        }
                    }
                    if ($v !== null) {
                        $vuota = false;
                    }
                    $dati[$campo] = $v;
                }
                if (!$vuota) {
                    $righe[$foglio][$r + 1] = $dati;
                }
            }
        }

        return $righe;
    }

    // ------------------------------------------------------------------ esecuzione

    /**
     * @return array{ok: bool, applicato: bool, errori: string[], errori_omessi: int, riepilogo: ?array}
     */
    public function esegui(string $percorso, bool $soloVerifica = false): array
    {
        $this->errori = [];
        $this->erroriOmessi = 0;

        $righe = $this->leggi($percorso);

        if (!$this->errori && !$righe) {
            $this->errori[] = 'Nessun foglio riconosciuto: servono «' . self::FOGLIO_CORSI . '», «' . self::FOGLIO_PIANO . '» e/o «' . self::FOGLIO_ASSOC . '». Scarica il modello dalla pagina di importazione.';
        }
        if ($this->errori) {
            return $this->esito(false, false, null);
        }

        // ---- stato attuale del database
        $corsi = [];        // chiave "nome|tipo" => ['id','nome','tipo','nuovo']
        foreach (Corso::all() as $c) {
            $corsi[$this->norm($c->nome) . '|' . $c->tipo_corso] = ['id' => $c->corso_id, 'nome' => $c->nome, 'tipo' => $c->tipo_corso, 'nuovo' => false];
        }
        $insegnamenti = []; // nome normalizzato => ['id','nome','nuovo']
        foreach (Insegnamento::all() as $i) {
            $insegnamenti[$this->norm($i->nome)] = ['id' => $i->insegnamento_id, 'nome' => $i->nome, 'nuovo' => false];
        }
        $corsoDaId = [];
        foreach ($corsi as $k => $c) {
            $corsoDaId[$c['id']] = $k;
        }
        $insDaId = [];
        foreach ($insegnamenti as $k => $i) {
            $insDaId[$i['id']] = $k;
        }

        $piano = [];        // "corsoKey#insKey" => ['corso','ins','anno','cfu','ore','ic_id','stato','foglio','riga']
        $icDaId = [];
        foreach (InsegnamentiCorso::all() as $ic) {
            if (!isset($corsoDaId[$ic->corso_id], $insDaId[$ic->insegnamento_id])) {
                continue;
            }
            $k = $corsoDaId[$ic->corso_id] . '#' . $insDaId[$ic->insegnamento_id];
            $piano[$k] = ['corso' => $corsoDaId[$ic->corso_id], 'ins' => $insDaId[$ic->insegnamento_id], 'anno' => (int) $ic->anno_di_corso,
                'cfu' => (int) $ic->cfu, 'ore' => (int) $ic->durata, 'ic_id' => $ic->ins_corso_id, 'stato' => 'invariato', 'tocc' => null];
            $icDaId[$ic->ins_corso_id] = $k;
        }

        $docenti = [];      // username normalizzato => ['id','ruolo']
        foreach (Utente::all() as $u) {
            $docenti[$this->norm($u->username)] = ['id' => $u->utente_id, 'ruolo' => $u->ruolo, 'username' => $u->username];
        }
        $docDaId = [];
        foreach ($docenti as $k => $d) {
            $docDaId[$d['id']] = $k;
        }

        $assoc = [];        // "pianoKey@userKey" => ['piano','user','ruolo','doc_ins_id','stato','tocc']
        foreach (DocenteInsegnamento::all() as $a) {
            if (!isset($icDaId[$a->ins_corso_id], $docDaId[$a->utente_id])) {
                continue;
            }
            $k = $icDaId[$a->ins_corso_id] . '@' . $docDaId[$a->utente_id];
            $assoc[$k] = ['piano' => $icDaId[$a->ins_corso_id], 'user' => $docDaId[$a->utente_id], 'ruolo' => $a->ruolo, 'doc_ins_id' => $a->doc_ins_id, 'stato' => 'invariato', 'tocc' => null];
        }

        $ris = [
            'corsi' => ['creati' => 0, 'esistenti' => 0],
            'insegnamenti' => ['creati' => 0],
            'piano' => ['creati' => 0, 'aggiornati' => 0, 'invariati' => 0],
            'associazioni' => ['create' => 0, 'aggiornate' => 0, 'invariate' => 0],
        ];

        // ---- 1) foglio Corsi
        $visti = [];
        foreach ($righe[self::FOGLIO_CORSI] ?? [] as $n => $r) {
            $nome = $r['nome'] === null ? '' : preg_replace('/\s+/u', ' ', trim((string) $r['nome']));
            $tipo = $this->tipoCanonico($r['tipo'] === null ? null : (string) $r['tipo']);
            $ok = true;
            if ($nome === '') { $this->errore(self::FOGLIO_CORSI, $n, 'il nome del corso è obbligatorio.'); $ok = false; }
            elseif (mb_strlen($nome) > 150) { $this->errore(self::FOGLIO_CORSI, $n, 'il nome del corso supera i 150 caratteri.'); $ok = false; }
            if ($tipo === null) { $this->errore(self::FOGLIO_CORSI, $n, 'tipo di corso non valido (valori ammessi: ' . implode(', ', self::TIPI) . ').'); $ok = false; }
            if (!$ok) { continue; }

            $k = $this->norm($nome) . '|' . $tipo;
            if (isset($visti[$k])) { $this->errore(self::FOGLIO_CORSI, $n, "il corso «{$nome}» ({$tipo}) è già presente alla riga {$visti[$k]}."); continue; }
            $visti[$k] = $n;

            if (isset($corsi[$k])) {
                $ris['corsi']['esistenti']++;
            } else {
                $corsi[$k] = ['id' => null, 'nome' => $nome, 'tipo' => $tipo, 'nuovo' => true];
                $ris['corsi']['creati']++;
            }
        }

        // risolve il corso indicato in una riga (con tipo facoltativo se il nome è univoco)
        $risolviCorso = function (string $foglio, int $n, $nomeRaw, $tipoRaw) use (&$corsi): ?string {
            $nome = $nomeRaw === null ? '' : $this->norm((string) $nomeRaw);
            if ($nome === '') {
                $this->errore($foglio, $n, 'il corso è obbligatorio.');
                return null;
            }
            $tipo = null;
            if ($tipoRaw !== null) {
                $tipo = $this->tipoCanonico((string) $tipoRaw);
                if ($tipo === null) {
                    $this->errore($foglio, $n, 'tipo di corso non valido (valori ammessi: ' . implode(', ', self::TIPI) . ').');
                    return null;
                }
            }
            $candidati = [];
            foreach ($corsi as $k => $c) {
                if ($this->norm($c['nome']) === $nome && ($tipo === null || $c['tipo'] === $tipo)) {
                    $candidati[] = $k;
                }
            }
            if (count($candidati) === 1) {
                return $candidati[0];
            }
            if (!$candidati) {
                $this->errore($foglio, $n, "corso «{$nomeRaw}»" . ($tipo ? " ({$tipo})" : '') . ' non trovato: aggiungilo nel foglio «' . self::FOGLIO_CORSI . '» o crealo prima dall\'area admin.');
            } else {
                $tipi = implode(', ', array_map(fn ($k) => $corsi[$k]['tipo'], $candidati));
                $this->errore($foglio, $n, "il corso «{$nomeRaw}» esiste in più tipi ({$tipi}): indica la colonna «Tipo corso».");
            }

            return null;
        };

        // ---- 2) foglio Piano di studi
        $vistiPiano = [];
        foreach ($righe[self::FOGLIO_PIANO] ?? [] as $n => $r) {
            $corsoKey = $risolviCorso(self::FOGLIO_PIANO, $n, $r['corso'], $r['tipo']);
            $nomeIns = $r['insegnamento'] === null ? '' : preg_replace('/\s+/u', ' ', trim((string) $r['insegnamento']));
            $ok = $corsoKey !== null;
            if ($nomeIns === '') { $this->errore(self::FOGLIO_PIANO, $n, 'l\'insegnamento è obbligatorio.'); $ok = false; }
            elseif (mb_strlen($nomeIns) > 150) { $this->errore(self::FOGLIO_PIANO, $n, 'il nome dell\'insegnamento supera i 150 caratteri.'); $ok = false; }

            $anno = $this->intero($r['anno']);
            $cfu = $this->intero($r['cfu']);
            $ore = $this->intero($r['ore']);
            if ($corsoKey !== null) {
                $durata = self::durataAnni($corsi[$corsoKey]['tipo']);
                if ($anno === null || $anno < 1 || $anno > $durata) {
                    $this->errore(self::FOGLIO_PIANO, $n, "l'anno di corso deve essere un numero da 1 a {$durata} (corso {$corsi[$corsoKey]['tipo']}).");
                    $ok = false;
                }
            } elseif ($anno === null || $anno < 1) {
                $ok = false;
            }
            if ($cfu === null || $cfu < 1 || $cfu > 30) { $this->errore(self::FOGLIO_PIANO, $n, 'i CFU devono essere un numero intero da 1 a 30.'); $ok = false; }
            if ($ore === null || $ore < 1) { $this->errore(self::FOGLIO_PIANO, $n, 'le ore devono essere un numero intero maggiore di 0.'); $ok = false; }
            if (!$ok) { continue; }

            $insKey = $this->norm($nomeIns);
            $k = $corsoKey . '#' . $insKey;
            if (isset($vistiPiano[$k])) { $this->errore(self::FOGLIO_PIANO, $n, "l'insegnamento «{$nomeIns}» è già presente per questo corso alla riga {$vistiPiano[$k]}."); continue; }
            $vistiPiano[$k] = $n;

            if (!isset($insegnamenti[$insKey])) {
                $insegnamenti[$insKey] = ['id' => null, 'nome' => $nomeIns, 'nuovo' => true];
                $ris['insegnamenti']['creati']++;
            }

            if (isset($piano[$k])) {
                $p = $piano[$k];
                if ($p['anno'] !== $anno || $p['cfu'] !== $cfu || $p['ore'] !== $ore) {
                    $piano[$k] = ['anno' => $anno, 'cfu' => $cfu, 'ore' => $ore, 'stato' => 'aggiornato', 'tocc' => [self::FOGLIO_PIANO, $n]] + $p;
                    $ris['piano']['aggiornati']++;
                } else {
                    $piano[$k]['tocc'] = [self::FOGLIO_PIANO, $n];
                    $ris['piano']['invariati']++;
                }
            } else {
                $piano[$k] = ['corso' => $corsoKey, 'ins' => $insKey, 'anno' => $anno, 'cfu' => $cfu, 'ore' => $ore, 'ic_id' => null, 'stato' => 'nuovo', 'tocc' => [self::FOGLIO_PIANO, $n]];
                $ris['piano']['creati']++;
            }
        }

        // ---- 3) foglio Associazioni docenti
        $vistiAssoc = [];
        foreach ($righe[self::FOGLIO_ASSOC] ?? [] as $n => $r) {
            $corsoKey = $risolviCorso(self::FOGLIO_ASSOC, $n, $r['corso'], $r['tipo']);
            $ok = $corsoKey !== null;

            $nomeIns = $r['insegnamento'] === null ? '' : $this->norm((string) $r['insegnamento']);
            $pianoKey = null;
            if ($nomeIns === '') {
                $this->errore(self::FOGLIO_ASSOC, $n, 'l\'insegnamento è obbligatorio.');
                $ok = false;
            } elseif ($corsoKey !== null) {
                $pianoKey = $corsoKey . '#' . $nomeIns;
                if (!isset($piano[$pianoKey])) {
                    $this->errore(self::FOGLIO_ASSOC, $n, "l'insegnamento «{$r['insegnamento']}» non fa parte del piano di studi del corso «{$corsi[$corsoKey]['nome']}» ({$corsi[$corsoKey]['tipo']}).");
                    $ok = false;
                }
            }

            $userKey = $r['username'] === null ? '' : $this->norm((string) $r['username']);
            if ($userKey === '') {
                $this->errore(self::FOGLIO_ASSOC, $n, 'lo username del docente è obbligatorio.');
                $ok = false;
            } elseif (!isset($docenti[$userKey])) {
                $this->errore(self::FOGLIO_ASSOC, $n, "il docente «{$r['username']}» non esiste.");
                $ok = false;
            } elseif ($docenti[$userKey]['ruolo'] !== 'Docente') {
                $this->errore(self::FOGLIO_ASSOC, $n, "l'utente «{$r['username']}» non ha il ruolo Docente.");
                $ok = false;
            }

            $ruolo = $this->ruoloCanonico($r['ruolo'] === null ? null : (string) $r['ruolo']);
            if ($ruolo === null) {
                $this->errore(self::FOGLIO_ASSOC, $n, 'ruolo non valido (valori ammessi: ' . implode(', ', self::RUOLI) . ').');
                $ok = false;
            }
            if (!$ok) { continue; }

            $k = $pianoKey . '@' . $userKey;
            if (isset($vistiAssoc[$k])) { $this->errore(self::FOGLIO_ASSOC, $n, "il docente «{$r['username']}» è già associato a questo insegnamento alla riga {$vistiAssoc[$k]}."); continue; }
            $vistiAssoc[$k] = $n;

            if (isset($assoc[$k])) {
                if ($assoc[$k]['ruolo'] !== $ruolo) {
                    $assoc[$k]['ruolo'] = $ruolo;
                    $assoc[$k]['stato'] = 'aggiornato';
                    $ris['associazioni']['aggiornate']++;
                } else {
                    $ris['associazioni']['invariate']++;
                }
                $assoc[$k]['tocc'] = [self::FOGLIO_ASSOC, $n];
            } else {
                $assoc[$k] = ['piano' => $pianoKey, 'user' => $userKey, 'ruolo' => $ruolo, 'doc_ins_id' => null, 'stato' => 'nuovo', 'tocc' => [self::FOGLIO_ASSOC, $n]];
                $ris['associazioni']['create']++;
            }
        }

        // ---- 4) regole sui primari, sullo stato finale (si segnalano solo i casi toccati dal file)
        $primariPerIns = [];   // pianoKey => [userKey,...]
        foreach ($assoc as $a) {
            if ($a['ruolo'] === 'Primario') {
                $primariPerIns[$a['piano']][] = $a['user'];
            }
        }
        foreach ($primariPerIns as $pk => $utenti) {
            if (count($utenti) < 2) { continue; }
            $toccati = array_filter($assoc, fn ($a) => $a['piano'] === $pk && $a['ruolo'] === 'Primario' && $a['tocc']);
            foreach ($toccati as $a) {
                $altri = array_map(fn ($u) => $docenti[$u]['username'], array_values(array_diff($utenti, [$a['user']])));
                $this->errore($a['tocc'][0], $a['tocc'][1], "l'insegnamento «{$insegnamenti[$piano[$pk]['ins']]['nome']}» avrebbe più di un docente primario (anche: " . implode(', ', $altri) . ').');
            }
        }
        $gruppi = [];          // json [corsoKey, anno, userKey] => [pianoKey,...]
        foreach ($assoc as $a) {
            if ($a['ruolo'] !== 'Primario' || !isset($piano[$a['piano']])) { continue; }
            $gruppi[json_encode([$piano[$a['piano']]['corso'], $piano[$a['piano']]['anno'], $a['user']])][] = $a['piano'];
        }
        foreach ($gruppi as $chiave => $pks) {
            if (count($pks) < 2) { continue; }
            [$corsoKey, $anno, $userKey] = json_decode($chiave, true);
            $toccati = [];
            foreach ($pks as $pk) {
                if ($piano[$pk]['tocc']) { $toccati[] = $piano[$pk]['tocc']; }
                foreach ($assoc as $a) {
                    if ($a['piano'] === $pk && $a['user'] === $userKey && $a['tocc']) { $toccati[] = $a['tocc']; }
                }
            }
            if (!$toccati) { continue; }
            $nomi = array_map(fn ($pk) => '«' . $insegnamenti[$piano[$pk]['ins']]['nome'] . '»', $pks);
            [$f, $r] = $toccati[0];
            $this->errore($f, $r, "il docente «{$docenti[$userKey]['username']}» sarebbe primario di più insegnamenti dell'anno {$anno} del corso «{$corsi[$corsoKey]['nome']}»: " . implode(', ', $nomi) . '.');
        }

        if ($this->errori) {
            return $this->esito(false, false, null);
        }

        if ($soloVerifica) {
            return $this->esito(true, false, $ris);
        }

        // ---- 5) salvataggio, tutto o niente
        DB::transaction(function () use (&$corsi, &$insegnamenti, &$piano, &$assoc, &$docenti) {
            foreach ($corsi as $k => $c) {
                if ($c['nuovo']) {
                    $corsi[$k]['id'] = Corso::create(['nome' => $c['nome'], 'tipo_corso' => $c['tipo']])->corso_id;
                }
            }
            foreach ($insegnamenti as $k => $i) {
                if ($i['nuovo']) {
                    $insegnamenti[$k]['id'] = Insegnamento::create(['nome' => $i['nome']])->insegnamento_id;
                }
            }
            foreach ($piano as $k => $p) {
                if ($p['stato'] === 'nuovo') {
                    $piano[$k]['ic_id'] = InsegnamentiCorso::create([
                        'corso_id' => $corsi[$p['corso']]['id'],
                        'insegnamento_id' => $insegnamenti[$p['ins']]['id'],
                        'anno_di_corso' => $p['anno'], 'cfu' => $p['cfu'], 'durata' => $p['ore'],
                    ])->ins_corso_id;
                } elseif ($p['stato'] === 'aggiornato') {
                    InsegnamentiCorso::where('ins_corso_id', $p['ic_id'])->update(['anno_di_corso' => $p['anno'], 'cfu' => $p['cfu'], 'durata' => $p['ore']]);
                }
            }
            foreach ($assoc as $a) {
                if ($a['stato'] === 'nuovo') {
                    DocenteInsegnamento::create(['ins_corso_id' => $piano[$a['piano']]['ic_id'], 'utente_id' => $docenti[$a['user']]['id'], 'ruolo' => $a['ruolo']]);
                } elseif ($a['stato'] === 'aggiornato') {
                    DocenteInsegnamento::where('doc_ins_id', $a['doc_ins_id'])->update(['ruolo' => $a['ruolo']]);
                }
            }
        });

        return $this->esito(true, true, $ris);
    }

    private function esito(bool $ok, bool $applicato, ?array $riepilogo): array
    {
        return ['ok' => $ok, 'applicato' => $applicato, 'errori' => $this->errori, 'errori_omessi' => $this->erroriOmessi, 'riepilogo' => $riepilogo];
    }

    // ------------------------------------------------------------------ modello scaricabile

    public function modello(): Spreadsheet
    {
        $excel = new Spreadsheet();
        $excel->removeSheetByIndex(0);

        $stileTesta = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F4E79']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];

        $i = 0;
        foreach (self::COLONNE as $foglio => $colonne) {
            $ws = $excel->createSheet($i++);
            $ws->setTitle($foglio);
            $c = 1;
            foreach ($colonne as $campo => [$titolo, , $obbligatoria]) {
                $lettera = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                $ws->setCellValue($lettera . '1', $titolo);
                $ws->getColumnDimension($lettera)->setWidth(in_array($campo, ['cfu', 'ore', 'anno', 'ruolo'], true) ? 16 : 34);

                if ($campo === 'tipo') {
                    $this->elenco($ws, $lettera, '"' . implode(',', self::TIPI) . '"');
                } elseif ($campo === 'ruolo') {
                    $this->elenco($ws, $lettera, '"' . implode(',', self::RUOLI) . '"');
                }
                $c++;
            }
            $ultima = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($colonne));
            $ws->getStyle("A1:{$ultima}1")->applyFromArray($stileTesta);
            $ws->freezePane('A2');
        }

        $ws = $excel->createSheet($i);
        $ws->setTitle(self::FOGLIO_ISTRUZIONI);
        $ws->getColumnDimension('A')->setWidth(120);
        $testo = [
            'ISTRUZIONI PER LA COMPILAZIONE',
            '',
            'Compila uno o più fogli, a partire dalla riga 2 (la riga 1 contiene le intestazioni e non va modificata). I fogli vuoti vengono ignorati.',
            '',
            '1) Foglio «' . self::FOGLIO_CORSI . '»: un corso per riga. Tipo corso: ' . implode(' / ', self::TIPI) . '.',
            '2) Foglio «' . self::FOGLIO_PIANO . '»: un insegnamento per riga. Il corso deve essere nel foglio «' . self::FOGLIO_CORSI . '» oppure già presente nel sistema.',
            '   Anno di corso: da 1 a 3 (Triennale), da 1 a 2 (Magistrale), da 1 a 5 (Magistrale a ciclo unico). CFU: da 1 a 30. Ore: ore di lezione (numero intero).',
            '   Gli insegnamenti non ancora presenti vengono creati. Se un insegnamento è già nel piano del corso, anno, CFU e ore vengono aggiornati.',
            '3) Foglio «' . self::FOGLIO_ASSOC . '»: un docente per riga. L\'insegnamento deve essere nel piano di studi del corso (nel foglio o già presente).',
            '   Lo username deve essere quello di un utente già registrato con ruolo Docente. Ruolo: ' . implode(' / ', self::RUOLI) . '.',
            '',
            'Se il nome di un corso esiste in più tipi (es. Triennale e Magistrale) compila sempre la colonna «Tipo corso».',
            'Regole: un solo docente Primario per insegnamento; un docente non può essere Primario di due insegnamenti dello stesso anno dello stesso corso.',
            'Prima di salvare, il sistema controlla tutte le righe: se c\'è anche solo un errore non viene salvato nulla e ti viene mostrato l\'elenco con foglio e riga.',
            'Puoi reimportare lo stesso file senza creare doppioni.',
        ];
        foreach ($testo as $n => $riga) {
            $ws->setCellValue('A' . ($n + 1), $riga);
        }
        $ws->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $ws->getStyle('A1:A' . count($testo))->getAlignment()->setWrapText(true);

        $excel->setActiveSheetIndex(0);

        return $excel;
    }

    private function elenco($ws, string $colonna, string $lista): void
    {
        $dv = $ws->getCell($colonna . '2')->getDataValidation();
        $dv->setType(DataValidation::TYPE_LIST)->setErrorStyle(DataValidation::STYLE_STOP)
            ->setAllowBlank(true)->setShowDropDown(false)->setShowErrorMessage(true)
            ->setErrorTitle('Valore non valido')->setError('Scegli un valore dall\'elenco.')
            ->setFormula1($lista);
        for ($r = 3; $r <= 500; $r++) {
            $ws->getCell($colonna . $r)->setDataValidation(clone $dv);
        }
    }
}
