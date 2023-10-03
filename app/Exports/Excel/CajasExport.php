<?php

namespace App\Exports\Excel;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;

class CajasExport implements FromCollection
{
	use Exportable;

	private $cajas;

	public function __construct($cajas)
    {
        $this->cajas = $cajas;
    }

    public function collection()
    {
        return collect($this->cajas);
    }
}
