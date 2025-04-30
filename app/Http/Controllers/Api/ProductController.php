<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;

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
    public function show(string $id)
    {
        // Find the product by ID
        $product = Product::find($id);

        // Check if the product exists
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json([
            'product' => $product,
            'message' => 'Product fetched successfully',
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'selling_price' => 'sometimes|required|numeric|min:0',
            'category_id' => 'sometimes|required|exists:categories,id',
            'stock' => 'sometimes|nullable|integer|min:0',
            'is_non_stock' => 'sometimes|nullable|boolean',
            'initial_price' => 'sometimes|required|numeric|min:0',
            'unit' => 'sometimes|required|string|max:50',
            'hero_images' => 'sometimes|nullable|string',
        ]);

        // Update the product
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
