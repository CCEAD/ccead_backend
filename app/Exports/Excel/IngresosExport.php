<?php

namespace App\Exports\Excel;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;

class IngresosExport implements FromCollection
{
	use Exportable;

	private $ingresos;

	public function __construct($ingresos)
    {
        $this->ingresos = $ingresos;
    }

    public function collection()
    {
        return collect($this->ingresos);
    }
}
