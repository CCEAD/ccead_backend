<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\CurrentPassword;

class UsuarioPasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'password_current'=>['required', new CurrentPassword()],
            'password' => 'required|min:5',
            'password_confirmation' => 'required|min:5|same:password',
        ];

        return $rules;
    }
}
