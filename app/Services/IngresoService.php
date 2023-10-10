<?php

namespace App\Services;

use App\Models\Ingreso;
use Illuminate\Http\Request;
use App\Exports\PdfExport;
use App\Transformers\IngresoTransformer;
use App\Exports\Excel\IngresosExport;

class IngresoService
{
    protected $transformer;

    public function __construct(IngresoTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function singlePdfDownload(Ingreso $ingreso) 
    {
        $ingreso = $this->transformer->item($ingreso);

        $export = new PdfExport('pdf.ingreso', ['ingreso' => $ingreso['ingreso']]);
        return $export->setMargin(2,2,2,2)->legal()->download();
    }

    public function manyPdfDownload(Request $request) 
    {
        if (empty($request->ingreso)) {
            $ingresos = $this->transformer->collection2(Ingreso::desc()->where('agencia_id', get_user_agencia())->get());
        } else {
            $ingresos = $this->transformer->collection2(Ingreso::in($request->ingreso)->get());
        }

        $export = new PdfExport('pdf.ingreso-list', $ingresos);
        return $export->options()->letter()->landscape()->download();
    }

    public function manyExcelDownload(Request $request) 
    {
        if (empty($request->ingreso)) {
            $ingresos = $this->transformer->collection2(Ingreso::desc()->where('agencia_id', get_user_agencia())->get());
        } else {
            $ingresos = $this->transformer->collection2(Ingreso::in($request->ingreso)->get());
        }

        return (new IngresosExport($ingresos))->download('ingresos.xlsx');
    }

    public function generateCubiPdf(Ingreso $ingreso) 
    {
        $data = $this->transformer->customItem('ingresoCubi', $ingreso);
            
        $export = new PdfExport('pdf.cubi', ['data' => $data['ingreso']]);
        return $export->options()->letter()->download();
    }
}
