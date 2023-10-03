<?php

namespace App\Services;

use App\Models\Caja;
use Illuminate\Http\Request;
use App\Exports\PdfExport;
use App\Transformers\CajaTransformer;
use App\Transformers\CarpetaTransformer;
use App\Exports\Excel\CajasExport;
use App\Exports\Excel\CarpetasExport;

class CajaService
{
    protected $transformer;
    protected $carpeta;

    public function __construct(CajaTransformer $transformer, CarpetaTransformer $carpeta)
    {
        $this->transformer = $transformer;
        $this->carpeta = $carpeta;
    }

    public function generateCubiPdf(Caja $caja) 
    {
        $data = $this->transformer->item($caja);
            
        $export = new PdfExport('pdf.cubi-caja', ['data' => $data['caja']]);
        return $export->options()->letter()->download();
    }

    public function manyPdfDownload(Request $request) 
    {
        if (empty($request->caja)) {
            $cajas = $this->transformer->collection2(Caja::desc()->where('agencia_id', get_user_agencia())->get());
        } else {
            $cajas = $this->transformer->collection2(Caja::in($request->caja)->get());
        }

        $export = new PdfExport('pdf.caja-list', $cajas);
        return $export->options()->letter()->landscape()->download();
    }

    public function manyExcelDownload(Request $request) 
    {
        if (empty($request->caja)) {
            $cajas = $this->transformer->collection2(Caja::desc()->where('agencia_id', get_user_agencia())->get());
        } else {
            $cajas = $this->transformer->collection2(Caja::in($request->caja)->get());
        }

        return (new CajasExport($cajas))->download('cajas.xlsx');
    }

    public function listCarpetasCajaPdf(Caja $caja) 
    {
        $export = new PdfExport('pdf.caja-carpetas-list', ['caja' => $caja]);
        return $export->options()->letter()->landscape()->download();
    }

    public function listCarpetasCajaExcel(Caja $caja) 
    {
        $carpetas = $this->carpeta->collection($caja->carpetas);
            
        return (new CarpetasExport($carpetas))->download('caja-carpetas-list.xlsx');
    }
}
