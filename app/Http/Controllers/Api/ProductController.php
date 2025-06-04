<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $outlet_id)
    {
        $products = Product::where('outlet_id', $outlet_id)->get();
        if ($products->isEmpty()) {
            return response()->json(['message' => 'No products found'], 404);
        }
        $products->each(function ($product) {
            $product->hero_images = $product->hero_images ? asset('storage/' . $product->hero_images) : null;
        });

        return response()->json([
            'products' => $products,
            'message' => 'Products fetched successfully',
        ], 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request, string $outlet_id)
    {
        $validatedData = $request->validated();

        $validatedData['outlet_id'] = $outlet_id;

        // Create a new product
        $product = Product::create($validatedData);

        // store image product
        if ($request->hasFile('hero_images')) {
            $imagePath = $request->file('hero_images')->store('products', 'public');
            $product->hero_images = $imagePath;
            $product->save();
        }

        if (!$product) {
            return response()->json(['message' => 'Failed to create product'], 500);
        }

        return response()->json([
            'product' => $product,
            'message' => 'Product created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $outlet_id, string $id)
    {
        // Find the product by ID
        $product = Product::find($id);

        // Check if the product exists
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        $product->hero_images = $product->hero_images ? asset('storage/' . $product->hero_images) : null;

        return response()->json([
            'product' => $product,
            'message' => 'Product fetched successfully',
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $outlet_id, string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'selling_price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'nullable|integer|min:0',
            'is_non_stock' => 'nullable',
            'initial_price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'hero_images' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);


        if ($request->hasFile('hero_images')) {
            $imagePath = $request->file('hero_images')->store('products', 'public');

            if ($product->hero_images) {
                Storage::disk('public')->delete($product->hero_images);
            }

            $validatedData['hero_images'] = $imagePath;
        }

        // Update product
        $product->update($validatedData);

        return response()->json([
            'product' => $product,
            'message' => 'Product updated successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        // Find the product by ID
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Delete the product
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully',
        ], 200);
    }
}
