<?php

namespace App\Services;

use App\Models\Salida;
use Illuminate\Http\Request;
use App\Exports\PdfExport;
use App\Transformers\SalidaTransformer;
use App\Exports\Excel\SalidasExport;

class SalidaService
{
    protected $transformer;

    public function __construct(SalidaTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function singlePdfDownload(Salida $salida) 
    {
        $salida = $this->transformer->item($salida);
        $export = new PdfExport('pdf.salida', ['salida' => $salida['salida']]);
        return $export->setMargin(2,2,2,2)->letter()->download();
    }

    public function manyPdfDownload(Request $request) 
    {
        if (empty($request->salida)) {
            $salidas = $this->transformer->collection2(Salida::desc()->where('agencia_id', get_user_agencia())->get());
        } else {
            $salidas = $this->transformer->collection2(Salida::in($request->salida)->get());
        }

        $export = new PdfExport('pdf.salida-list', $salidas);
        return $export->options()->letter()->landscape()->download();
    }

    public function manyExcelDownload(Request $request) 
    {
        if (empty($request->salida)) {
            $salidas = $this->transformer->collection2(Salida::desc()->where('agencia_id', get_user_agencia())->get());
        } else {
            $salidas = $this->transformer->collection2(Salida::in($request->salida)->get());
        }

        return (new SalidasExport($salidas))->download('salidas.xlsx');
    }
}
