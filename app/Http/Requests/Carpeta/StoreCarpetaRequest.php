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
            'nro_declaracion' => 'nullable|max:64',
            'nro_registro' => 'nullable|max:64',
            'fecha_aceptacion' => 'nullable|date_format:Y-m-d',
            'regimen_aduanero' => 'nullable|max:128',
            'modalidad_regimen' => 'nullable|max:64',
            'modalidad_despacho' => 'nullable|max:64',
            'importador' => 'nullable|integer',
            'declarante' => 'nullable|integer',
            'pais_exportacion' => 'nullable|max:64',
            'aduana_ingreso' => 'nullable|max:128',
            'total_nro_facturas' => 'nullable|integer',
            'total_nro_items' => 'nullable|integer',
            'total_nro_bultos' => 'nullable|integer',
            'total_peso_bruto' => 'nullable|max:15|regex:/^-?[0-9]+(?:\.[0-9]{1,2})?$/',
            'total_valor_fob' => 'nullable|max:15|regex:/^-?[0-9]+(?:\.[0-9]{1,2})?$/',
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
