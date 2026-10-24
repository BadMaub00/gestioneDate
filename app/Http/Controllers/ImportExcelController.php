<?php

namespace App\Http\Controllers;

use App\Services\ImportStrutturaDidattica;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ImportExcelController extends Controller
{
    /** La libreria si installa con: composer require phpoffice/phpspreadsheet */
    private function libreriaDisponibile(): bool
    {
        return class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class);
    }

    public function index()
    {
        return view('admin.import.index', [
            'tipi' => ImportStrutturaDidattica::TIPI,
            'ruoli' => ImportStrutturaDidattica::RUOLI,
            'libreriaOk' => $this->libreriaDisponibile(),
        ]);
    }

    /** Scarica il file Excel vuoto con le intestazioni e le istruzioni. */
    public function modello(ImportStrutturaDidattica $import)
    {
        if (!$this->libreriaDisponibile()) {
            return redirect()->route('admin.import.index');
        }

        $excel = $import->modello();

        return response()->streamDownload(function () use ($excel) {
            (new Xlsx($excel))->save('php://output');
        }, 'modello_struttura_didattica.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function store(Request $request, ImportStrutturaDidattica $import)
    {
        if (!$this->libreriaDisponibile()) {
            return redirect()->route('admin.import.index');
        }

        $request->validate([
            'file' => 'required|file|mimes:xlsx|max:5120',
        ], [
            'file.required' => 'Seleziona un file Excel (.xlsx).',
            'file.mimes'    => 'Il file deve essere in formato Excel (.xlsx).',
            'file.max'      => 'Il file non può superare i 5 MB.',
            'file.uploaded' => 'Caricamento non riuscito: il file è troppo grande o il caricamento è stato interrotto.',
        ]);

        $report = $import->esegui($request->file('file')->getRealPath(), $request->boolean('solo_verifica'));
        $report['file'] = $request->file('file')->getClientOriginalName();

        return redirect()->route('admin.import.index')->with('import_report', $report);
    }
}
