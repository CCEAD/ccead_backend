<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class PasswordRegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'password' => 'required|min:5',
            'password_confirmation' => 'required|min:5|same:password',
        ];

        return $rules;
    }

    public function attributes()
    {
        return [
            'password' => 'contraseña',
            'password_confirmation' => 'confirmación contraseña',
        ];
    }
}
