<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Exports\PdfExport;

class ReporteService
{
    private $views = [1 => 'pdf.reporte', 2 => 'pdf.report-customer-accounts'];
    

    public function __construct()
    {
        setlocale(LC_ALL, "es_ES");
        date_default_timezone_set('America/Caracas');
    }

    public function manyPdfDownload(Request $request) 
    {
        $data = [];

        foreach ($request->data as $key => $value) {
            $data[$key] = $value;
        }

        $data['now'] = date("d/m/Y");

        $export = new PdfExport($this->views[$request->data['type']], $data);
        return $export->{$export->types[$request->data['type']]}();
    }

    public function totalInvGestionAgencia($data, $agencia) 
    {
        $export = new PdfExport('pdf.reporte', ['data' => $data, 'agencia' => $agencia]);
        return $export->options()->letter()->download();
    }

    public function totalGeneralAgencia($data) 
    {
        $export = new PdfExport('pdf.reporte2', ['data' => $data]);
        return $export->options()->letter()->download();
    }

    public function totalInvAduanaAgencia($data)
    {
        $export = new PdfExport('pdf.reporte', ['data' => $data]);
        return $export->options()->letter()->download();
    }
}
