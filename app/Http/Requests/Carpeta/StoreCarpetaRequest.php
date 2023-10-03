<?php

namespace App\Http\Requests\Carpeta;

use Illuminate\Foundation\Http\FormRequest;

class StoreCarpetaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            //'codigo' => 'required|max:64',
            'nro_declaracion' => 'required|max:64',
            'nro_registro' => 'nullable|max:64',
            'fecha_aceptacion' => 'required|date_format:Y-m-d',
            'regimen_aduanero' => 'required|max:128',
            'modalidad_regimen' => 'required|max:64',
            'modalidad_despacho' => 'required|max:64',
            'importador' => 'required|integer',
            'declarante' => 'required|integer',
            'pais_exportacion' => 'required|max:64',
            'aduana_ingreso' => 'required|max:128',
            'total_nro_facturas' => 'required|integer',
            'total_nro_items' => 'required|integer',
            'total_nro_bultos' => 'required|integer',
            'total_peso_bruto' => 'required|max:15|regex:/^-?[0-9]+(?:\.[0-9]{1,2})?$/',
            'total_valor_fob' => 'required|max:15|regex:/^-?[0-9]+(?:\.[0-9]{1,2})?$/',
            'caja_id' => 'required|integer',
        ];

        return $rules;
    }

    public function attributes()
    {
        return [
            //'codigo' => 'código',
            'nro_declaracion' => 'n° de declaración',
            'nro_registro' => 'n° de registro',
            'fecha_aceptacion' => 'fecha de aceptación',
            'regimen_aduanero' => 'régimen aduanero',
            'modalidad_regimen' => 'modalidad del régimen',
            'modalidad_despacho' => 'modalidad de despacho',
            'importador' => 'importador',
            'declarante' => 'declarante',
            'pais_exportacion' => 'país de exportación',
            'aduana_ingreso' => 'aduana de ingreso',
            'total_nro_facturas' => 'total nro de facturas',
            'total_nro_items' => 'total nro de items',
            'total_nro_bultos' => 'total nro de bultos',
            'total_peso_bruto' => 'total peso bruto',
            'total_valor_fob' => 'total valor FOB (USD)',
            'caja_id' => 'caja',
        ];
    }
}
