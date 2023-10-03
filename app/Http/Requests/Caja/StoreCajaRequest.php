<?php

namespace App\Http\Requests\Caja;

use Illuminate\Foundation\Http\FormRequest;

class StoreCajaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'gestion' => 'required',
            'cod_interno' => 'required|max:64',
            'cant_carpetas' => 'required|integer',
            'reg_inicial' => 'nullable|integer',
            'reg_final' => 'nullable|integer',
            'observaciones' => 'nullable|max:128',
            'agencia_id' => 'nullable|integer',
        ];

        return $rules;
    }

    public function attributes()
    {
        return [
            'gestion' => 'gestión',
            'cod_interno' => 'código interno',
            'cant_carpetas' => 'cantidad carpetas',
            'reg_inicial' => 'n° registro inicial',
            'reg_final' => 'n° registro final',
            'observaciones' => 'observaciones',
            'agencia_id' => 'agencia',
        ];
    }
}
