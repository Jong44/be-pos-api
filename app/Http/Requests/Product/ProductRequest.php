<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'nullable|integer|min:0',
            'is_non_stock' => 'nullable|boolean',
            'initial_price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'hero_images' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Product name is required',
            'name.string' => 'Product name must be a string',
            'name.max' => 'Product name must not exceed 255 characters',
            'price.required' => 'Price is required',
            'price.numeric' => 'Price must be a number',
            'price.min' => 'Price must be at least 0',
            'category_id.required' => 'Category ID is required',
            'category_id.exists' => 'Category ID must exist in the categories table',
            'stock.integer' => 'Stock must be an integer',
            'stock.min' => 'Stock must be at least 0',
            'is_non_stock.boolean' => 'Is non-stock must be true or false',
            'initial_price.required' => 'Initial price is required',
            'initial_price.numeric' => 'Initial price must be a number',
            'initial_price.min' => 'Initial price must be at least 0',
            'unit.required' => 'Unit is required',
            'unit.string' => 'Unit must be a string',
            'unit.max' => 'Unit must not exceed 50 characters',
            'hero_images.string' => 'Hero images must be a string',

        ];
    }
}
