<?php

namespace App\Imports;

use App\Models\Carpeta;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;

class CarpetasImport implements ToModel, WithHeadingRow
{
    private $data;
    
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function model(array $row)
    {
        switch ($row['aduana']) {
            case '701':
                $aduana = 1;
                break;
            case '711':
                $aduana = 2;
                break;
            case '735':
                $aduana = 3;
                break;
            case '732':
                $aduana = 4;
                break;
            case '737':
                $aduana = 5;
                break;
            case '738':
                $aduana = 6;
                break;
            case '761':
                $aduana = 7;
                break;
            case '721':
                $aduana = 8;
                break;
            case '722':
                $aduana = 9;
                break;
            case '621':
                $aduana = 10;
                break;
            case '643':
                $aduana = 11;
                break;
            case '422':
                $aduana = 12;
                break;
            case '241':
                $aduana = 13;
                break;
            case '421':
                $aduana = 14;
                break;
            case '702':
                $aduana = 15;
                break;
            case '751':
                $aduana = 16;
                break;
            case '741':
                $aduana = 16;
                break;
            default:
                $aduana = 1;
                break;
        }

        return new Carpeta([
            'nro_declaracion' => str_replace ( 'C-', '', strtoupper($row['dui'])),
            'nro_registro' => $row['interno'],
            'regimen_aduanero' => $row['regimen'],
            'caja_id' => $this->data['caja'],
            'aduana_id' => $aduana
        ]);
    }

    public function headingRow(): int
    {
        return 9;
    }
}
