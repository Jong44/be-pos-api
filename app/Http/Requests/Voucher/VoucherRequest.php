<?php

namespace App\Http\Requests\Voucher;

use Illuminate\Foundation\Http\FormRequest;

class VoucherRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'outlet_id' => 'required|exists:outlets,id',
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,nominal',
            'nominal' => 'required|numeric',
            'start_date' => 'required|date',
            'expired_date' => 'required|date|after_or_equal:start_date',
            'minimum_buying' => 'required|numeric',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function messages()
    {
        return [
            'outlet_id.required' => 'Outlet ID is required',
            'outlet_id.exists' => 'Outlet ID must exist in the outlets table',
            'code.required' => 'Code is required',
            'name.required' => 'Name is required',
            'type.required' => 'Type is required',
            'type.in' => 'Type must be either percentage or nominal',
            'nominal.required' => 'Nominal is required',
            'start_date.required' => 'Start date is required',
            'expired_date.required' => 'Expired date is required',
            'expired_date.after_or_equal' => 'Expired date must be after or equal to start date',
            'minimum_buying.required' => 'Minimum buying is required',
            'status.required' => 'Status is required',
            'status.in' => 'Status must be either active or inactive',
        ];
    }
}
