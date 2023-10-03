<?php

namespace App\Http\Requests\Caja;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCajaRequest extends FormRequest
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
        ];

        if($this->method() == 'PATCH' || $this->method() == 'PUT') {
            $rules['cod_interno'] .= ',' . $this->caja->id;
        }

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
        ];
    }
}
