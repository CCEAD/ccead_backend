<?php

namespace App\Http\Requests\Agencia;

use Illuminate\Foundation\Http\FormRequest;

class RegistroAgenciaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'archivos.poder_representacion' => 'required|mimes:pdf|max:2048',
            'archivos.matricula_comercio' => 'required|mimes:pdf|max:2048',
            'archivos.licencia_funcionamiento' => 'required|mimes:pdf|max:2048',
        ];
    }

    public function attributes()
    {
        return [
            'archivos.poder_representacion' => 'poder de representación',
            'archivos.matricula_comercio' => 'matrícula de comercio',
            'archivos.licencia_funcionamiento' => 'licencia de funcionamiento',
        ];
    }
}
