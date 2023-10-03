<?php

namespace App\Services;

use App\Models\Reingreso;
use Illuminate\Http\Request;
use App\Exports\PdfExport;
use App\Transformers\ReingresoTransformer;

class ReingresoService
{
    protected $transformer;

    public function __construct(ReingresoTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function singlePdfDownload(Reingreso $reingreso) 
    {
        $reingreso = $this->transformer->item($reingreso);
        $export = new PdfExport('pdf.reingreso', ['reingreso' => $reingreso['reingreso']]);
        return $export->options()->letter()->download();
    }

    public function manyPdfDownload(Request $request) 
    {
        if (empty($request->reingreso)) {
            $reingresos = $this->transformer->collection(Reingreso::desc()->checklist()->get());
        } else {
            $reingresos = $this->transformer->collection(Reingreso::in($request->reingreso)->checklist()->get());
        }

        $export = new PdfExport('pdf.reingreso-list', $reingresos);
        return $export->options()->letter()->landscape()->download();
    }
}
