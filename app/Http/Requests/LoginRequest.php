<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'Username' => 'required|exists:users,username,deleted_at,NULL',
            'Password' => 'required'
        ];
    }
}
