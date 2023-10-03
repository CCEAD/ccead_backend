<?php

namespace App\Http\Requests\Salida;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalidaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'salida.fecha_solicitud' => 'required|date_format:Y-m-d',
            'salida.observacion' => 'nullable|max:128',
        ];

        return $rules;
    }

    public function attributes()
    {
        return [
            'salida.fecha_solicitud' => 'fecha de solicitud',
            'salida.observacion' => 'observación',
        ];
    }
}
