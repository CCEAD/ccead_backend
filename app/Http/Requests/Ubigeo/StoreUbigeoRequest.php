<?php

namespace App\Http\Requests\Ubigeo;

use Illuminate\Foundation\Http\FormRequest;

class StoreUbigeoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'numero_interno' => 'required',
            'codigo' => 'required|max:64',
        ];

        if($this->method() == 'PATCH' || $this->method() == 'PUT') {
            $rules['codigo'] .= ',' . $this->id;
        }

        return $rules;
    }

    public function attributes()
    {
        return [
            'numero_interno' => 'número interno',
            'codigo' => 'código',
        ];
    }
}
