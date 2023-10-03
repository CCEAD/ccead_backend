<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'nombres' => 'required|min:3|max:128',
            'apellidos' => 'required|min:3|max:128',
            'name' => 'required|min:3|max:64|unique:users,name',
            'email' => 'required|email|max:64|unique:users,email',
            'telefono' => 'required|min:6|max:32',
            'agencia_id' => verificar_agencia() ? 'required|integer' : 'nullable|integer',
        ];

        if($this->method() == 'POST') {
            $rules['password'] = 'required|min:5';
            $rules['password_confirmation'] = 'required|min:5|same:password';
        }

        if($this->method() == 'PATCH' || $this->method() == 'PUT') {
            $rules['name'] .= ',' . $this->user->id;
            $rules['email'] .= ',' . $this->user->id;
        }

        return $rules;
    }

    public function attributes()
    {
        return [
            'name' => 'nombre usuario',
            'email' => 'correo',
            'telefono' => 'teléfono',
            'agencia_id' => 'agencia',
        ];
    }
}
