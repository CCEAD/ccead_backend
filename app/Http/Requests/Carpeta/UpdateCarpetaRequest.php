<?php

namespace App\Http\Requests\Carpeta;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCarpetaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'nro_declaracion' => 'nullable|max:64',
            'nro_registro' => 'nullable|max:64',
        ];

        return $rules;
    }

    public function attributes()
    {
        return [
            'nro_declaracion' => 'n° de declaración',
            'nro_registro' => 'n° de registro',
        ];
    }
}
