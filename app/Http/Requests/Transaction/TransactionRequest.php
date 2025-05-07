<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'date' => 'required|date',
            'note' => 'nullable|string|max:255',
            'voucher_id' => 'nullable|exists:vouchers,id',
            'discout_price' => 'nullable|numeric|min:0',
            'payed_money' => 'required|numeric|min:0',
            'money_changes' => 'nullable|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
            'total_cost' => 'required|numeric|min:0',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'tax' => 'nullable|numeric',
            'tax_price' => 'nullable|numeric|min:0',
            'total_qty' => 'required|integer|min:1',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.qty' => 'required|integer|min:1',

        ];
    }

    public function messages()
    {
        return [
            'date.required' => 'The date field is required.',
            'date.date' => 'The date field must be a valid date.',
            'note.string' => 'The note field must be a string.',
            'note.max' => 'The note field may not be greater than 255 characters.',
            'voucher_id.exists' => 'The selected voucher is invalid.',
            'discout_price.numeric' => 'The discount price must be a number.',
            'discout_price.min' => 'The discount price must be at least 0.',
            'payed_money.required' => 'The payed money field is required.',
            'payed_money.numeric' => 'The payed money must be a number.',
            'payed_money.min' => 'The payed money must be at least 0.',
            'money_changes.numeric' => 'The money changes must be a number.',
            'money_changes.min' => 'The money changes must be at least 0.',
            'total_price.required' => 'The total price field is required.',
            'total_price.numeric' => 'The total price must be a number.',
            'total_price.min' => 'The total price must be at least 0.',
            'total_cost.required' => 'The total cost field is required.',
            'total_cost.numeric' => 'The total cost must be a number.',
            'total_cost.min' => 'The total cost must be at least 0.',
            'payment_method_id.required' => 'The payment method field is required.',
            'payment_method_id.exists' => 'The selected payment method is invalid.',
            'tax.numeric' => 'The tax must be a number.',
            'tax_price.numeric' => 'The tax price must be a number.',
            'tax_price.min' => 'The tax price must be at least 0.',
            'total_qty.required' => 'The total quantity field is required.',
            'total_qty.integer' => 'The total quantity must be an integer.',
            'total_qty.min' => 'The total quantity must be at least 1.',
            'products.required' => 'The products field is required.',
            'products.array' => 'The products field must be an array.',
            'products.*.product_id.required' => 'The product ID field is required.',
            'products.*.product_id.exists' => 'The selected product ID is invalid.',
            'products.*.qty.required' => 'The quantity field is required.',
            'products.*.qty.integer' => 'The quantity must be an integer.',
            'products.*.qty.min' => 'The quantity must be at least 1.',
        ];
    }
}
