<?php

namespace App\Http\Requests\Representante;

use Illuminate\Foundation\Http\FormRequest;

class StoreRepresentanteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'nombres' => 'required|max:64',
            'apellidos' => 'required|max:64|',
            'telefono' => 'required|max:32',
            'correo' => 'required|max:128',
        ];

        return $rules;
    }

    public function attributes()
    {
        return [
            'nombres' => 'nombres',
            'apellidos' => 'apellidos',
            'telefono' => 'teléfono',
            'correo' => 'correo',
        ];
    }
}
