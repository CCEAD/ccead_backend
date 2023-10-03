<?php

namespace App\Exports\Excel;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;

class SalidasExport implements FromCollection
{
	use Exportable;

	private $salidas;

	public function __construct($salidas)
    {
        $this->salidas = $salidas;
    }

    public function collection()
    {
        return collect($this->salidas);
    }
}
