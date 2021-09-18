<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return config('custom_settings.is_registration_available');
    }

    public function rules()
    {
        $id = request()->is('user/add') ? 0 : request()->get('Id', 0);

        $maxStringValue = config('custom_settings.input_max_sizes.string');

        if($id) {
            $rules1 = [
                'Id' => 'required|exists:users,id,deleted_at,NULL'
            ];
        } else {
            $rules1 = [
                'Username' => "required|unique:users,username,$id,id,deleted_at,NULL|max:$maxStringValue"
            ];
        }

        $rules2 = [
            'Email' => "required|email:filter|unique:users,email,$id,id,deleted_at,NULL|max:$maxStringValue",
            'Password' => "required|max:50",
            'Group' => 'nullable|array',
            'Group.*' => 'exists:groups,id,deleted_at,NULL|distinct',
            'Avatar' => 'nullable|mimes:jpg,jpeg,png|max:1024'
        ];

        return array_merge($rules1, $rules2);
    }
}
