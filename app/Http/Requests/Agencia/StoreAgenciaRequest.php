<?php

namespace App\Http\Requests\Agencia;

use Illuminate\Foundation\Http\FormRequest;

class StoreAgenciaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'razon_social' => 'required|max:64',
            'nit' => 'required|min:3|max:32|unique:agencias,nit',
            'telefono' => 'required|max:32',
            'direccion' => 'required|max:128',
            'ciudad' => 'required|max:128',
        ];

        return $rules;
    }

    public function attributes()
    {
        return [
            'razon_social' => 'razón social',
            'nit' => 'nit',
            'telefono' => 'teléfono',
            'direccion' => 'dirección',
            'ciudad' => 'ciudad',
        ];
    }
}
