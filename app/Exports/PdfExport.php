<?php

namespace App\Exports;
use PDF;
use Illuminate\Http\Response;

class PdfExport
{
    private $view;

    private $pdf;

    public $types = [1 => 'configPdfLandscape', 2 => 'configPdfPortrait'];

    public function __construct($view, $data)
    {
        $this->pdf = PDF::loadView($view, $data);
        $this->view = $view;
    }

    public function setMargin($top, $bottom, $left, $right)
    {
        $this->pdf->setOption('margin-top', $top)->setOption('margin-bottom', $bottom)->setOption('margin-left', $left)->setOption('margin-right', $right);
        return $this;
    }

    public function options()
    {
        $this->pdf->setOption('margin-top', 5)->setOption('margin-bottom', 5)->setOption('margin-left', 5)->setOption('margin-right', 5);
        return $this;
    }

    public function letter()
    {
        $this->pdf->setPaper('letter');
        return $this;
    }

    public function legal()
    {
        $this->pdf->setPaper('legal');
        return $this;
    }

    public function portrait()
    {
        $this->pdf->setOrientation('portrait');
        return $this;
    }

    public function landscape()
    {
        $this->pdf->setOrientation('landscape');
        return $this;
    }

    public function download($filename = null)
    {
        return $this->pdf->download();
    }

    public function configPdfLandscape()
    {
        return $this->options()->letter()->landscape()->download();
    }

    public function configPdfPortrait()
    {
        return $this->setMargin(1,1,1,1)->letter()->portrait()->download();
    }

    public function setMarginInvoice()
    {
        return $this->setMargin(2,2,2,2);
    }
}