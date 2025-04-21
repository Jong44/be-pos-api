<?php

namespace App\Http\Requests\Outlet;

use Illuminate\Foundation\Http\FormRequest;

class OutletRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'outlet_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15',
            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
            'tax' => 'nullable|numeric',
            'email' => 'nullable|email|max:255',
        ];
    }

    public function messages()
    {
        return [
            'outlet_name.required' => 'Outlet name is required',
            'address.required' => 'Address is required',
            'phone_number.required' => 'Phone number is required',
            'longitude.numeric' => 'Longitude must be a number',
            'latitude.numeric' => 'Latitude must be a number',
            'tax.numeric' => 'Tax must be a number',
            'email.email' => 'Email must be a valid email address',
            'email.max' => 'Email must not exceed 255 characters',
        ];
    }
}
