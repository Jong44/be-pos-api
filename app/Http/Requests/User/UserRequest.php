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
        return [
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'outlet_id' => 'required|exists:outlets,id',

        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'password.required' => 'The password field is required.',
            'role.required' => 'The role field is required.',
            'outlet_id.required' => 'The outlet field is required.',
            'outlet_id.exists' => 'The selected outlet does not exist.',
            'email.unique' => 'The email has already been taken.',
            'username.string' => 'The name must be a string.',
            'username.max' => 'The name may not be greater than 255 characters.',
            'email.email' => 'The email must be a valid email address.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }
}
