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
            'selling_price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'nullable|integer|min:0',
            'is_non_stock' => 'nullable',
            'initial_price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'hero_images' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Product name is required',
            'name.string' => 'Product name must be a string',
            'name.max' => 'Product name must not exceed 255 characters',
            'selling_price.required' => 'Price is required',
            'selling_price.numeric' => 'Price must be a number',
            'prselling_priceice.min' => 'Price must be at least 0',
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
            'hero_images.file' => 'Hero images must be a file',
            'hero_images.mimes' => 'Hero images must be a file of type: jpg, jpeg, png, gif, webp',
            'hero_images.max' => 'Hero images must not exceed 2MB',

        ];
    }


}
