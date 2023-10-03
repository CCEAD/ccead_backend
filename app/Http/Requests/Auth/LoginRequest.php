<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\UsesCustomErrorMessage;

class LoginRequest extends FormRequest
{
    use UsesCustomErrorMessage;
    
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];
        return $rules;
    }
    
    public function message()
    {
        return 'Por favor verifique los errores a continuación.';
    }
}
