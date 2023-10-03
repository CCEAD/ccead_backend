<?php

namespace App\Exports\Excel;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;

class CarpetasExport implements FromCollection
{
	use Exportable;

	private $carpetas;

	public function __construct($carpetas)
    {
        $this->carpetas = $carpetas;
    }

    public function collection()
    {
        return collect($this->carpetas);
    }
}
