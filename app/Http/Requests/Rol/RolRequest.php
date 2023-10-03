<?php

namespace App\Http\Requests\Rol;

use Illuminate\Foundation\Http\FormRequest;

class RolRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'nombre' => 'required|min:3|max:60',
            'permisos' => 'required|array|min:1',
            'permisos.*' => 'required'
        ];

        // if($this->method() == 'PATCH' || $this->method() == 'PUT') {
        //     $rules['name'] .= ',' . $this->id;
        // }

        return $rules;
    }
}
