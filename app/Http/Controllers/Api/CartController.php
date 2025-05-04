<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function getCart(string $outlet_id)
    {
        $user_id = auth()->user()->id;
        $cart = Cart::with('product')
            ->where('outlet_id', $outlet_id)
            ->where('user_id', $user_id)
            ->get();

        if ($cart->isEmpty()) {
            return response()->json(['message' => 'No items in cart'], 404);
        }

        return response()->json([
            'cart' => $cart,
            'message' => 'Cart fetched successfully',
        ], 200);
    }

    public function addProductToCart(Request $request, string $outlet_id)
    {
        $validatedData = $request->validate([
            'product_id' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $user_id = auth()->user()->id;

        $cartItem = Cart::where('outlet_id', $outlet_id)
            ->where('user_id', $user_id)
            ->where('product_id', $validatedData['product_id'])
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $validatedData['quantity'];
            $cartItem->save();
        } else {
            Cart::create([
                'outlet_id' => $outlet_id,
                'user_id' => $user_id,
                'product_id' => $validatedData['product_id'],
                'quantity' => $validatedData['quantity'],
            ]);
        }

        return response()->json([
            'message' => 'Product added to cart successfully',
        ], 201);
    }

    public function removeProductFromCart(string $id)
    {
        $user_id = auth()->user()->id;

        $cartItem = Cart::where('id', $id)
            ->where('user_id', $user_id)
            ->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Cart item not found'], 404);
        }

        $cartItem->delete();

        return response()->json([
            'message' => 'Product removed from cart successfully',
        ], 200);
    }

    public function updateCartItem(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $user_id = auth()->user()->id;

        $cartItem = Cart::where('id', $id)
            ->where('user_id', $user_id)
            ->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Cart item not found'], 404);
        }

        $cartItem->update($validatedData);

        return response()->json([
            'message' => 'Cart item updated successfully',
        ], 200);
    }

    public function clearCart(string $outlet_id)
    {
        $user_id = auth()->user()->id;

        Cart::where('outlet_id', $outlet_id)
            ->where('user_id', $user_id)
            ->delete();

        return response()->json([
            'message' => 'Cart cleared successfully',
        ], 200);
    }


}
