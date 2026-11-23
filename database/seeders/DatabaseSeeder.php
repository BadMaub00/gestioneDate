<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    /**
     * Tabelle da popolare, nell'ordine imposto dalle chiavi esterne (prima le "genitrici").
     * Il valore è la chiave primaria, usata per aggiornare le righe se esistono già.
     * I dati stanno in database/seeders/data/<tabella>.php e riproducono SQL/coordinamento_date_esame.sql.
     */
    private const TABELLE = [
        'corso' => 'corso_id',
        'insegnamento' => 'insegnamento_id',
        'edificio' => 'edificio_id',
        'utente' => 'utente_id',
        'aula' => 'aula_id',
        'insegnamento_corso' => 'ins_corso_id',
        'sessione_esami' => 'sessione_id',
        'docente_insegnamento' => 'doc_ins_id',
        'appello' => 'appello_id',
    ];

    public function run(): void
    {
        foreach (array_keys(self::TABELLE) as $tabella) {
            if (! Schema::hasTable($tabella)) {
                throw new RuntimeException("La tabella '{$tabella}' non esiste: esegui prima 'php artisan migrate'.");
            }
        }

        // Tutto o niente: se qualcosa va storto non resta un database popolato a metà.
        DB::transaction(function () {
            foreach (self::TABELLE as $tabella => $chiave) {
                $righe = require database_path("seeders/data/{$tabella}.php");
                $colonne = array_values(array_diff(array_keys($righe[0]), [$chiave]));

                // upsert: rieseguire il seeder aggiorna le righe esistenti invece di dare errore di chiave duplicata.
                // Blocchi da 100 righe per restare sotto il limite di parametri di SQLite/MySQL.
                foreach (array_chunk($righe, 100) as $blocco) {
                    DB::table($tabella)->upsert($blocco, [$chiave], $colonne);
                }

                $this->command?->info(count($righe) . " righe in '{$tabella}'");
            }
        });
    }
}
