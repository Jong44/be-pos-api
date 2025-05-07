<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\TransactionRequest;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function createTransaction(TransactionRequest $request, string $outlet_id)
    {
        $validated = $request->validated();
        $validated['outlet_id'] = $outlet_id;
        $validated['cashier_id'] = auth()->user()->id;
        $validated['code'] = Transaction::generateCustomCode();

        $transaction = Transaction::create($validated);


        if ($transaction) {
            $transactionDetails = [];
            foreach ($request->products as $product) {
                $productModel = Product::find($product['product_id']);
                if (!$productModel) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Product not found'
                    ], 404);
                }

                $cost = $productModel->initial_price * $product['qty'];
                $price = $productModel->selling_price * $product['qty'];

                TransactionDetail::create([
                    'code' => $validated['code'],
                    'transaction_id' => $transaction->id,
                    'product_id' => $product['product_id'],
                    'qty' => $product['qty'],
                    'price' => $price,
                    'cost' => $cost,
                ]);
            }

            // delete cart
            $cart = Cart::where('outlet_id', $outlet_id)
                ->where('user_id', auth()->user()->id)
                ->first();
            if ($cart) {
                $cart->delete();
            }


            // decrement product stock
            foreach ($request->products as $product) {
                $productModel = Product::find($product['product_id']);
                if ($productModel) {
                    $productModel->decrement('stock', $product['qty']);
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Transaction created successfully',
                'data' => $transaction
            ], 201);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create transaction'
            ], 500);
        }
    }

    public function getHistoryTransaction(string $outlet_id)
    {
        $transactions = TransactionDetail::with(['transaction', 'product'])
            ->whereHas('transaction', function ($query) use ($outlet_id) {
                $query->where('outlet_id', $outlet_id);
            })
            ->get();

            if ($transactions->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No transactions found'
                ], 404);
            }

        return response()->json([
            'status' => 'success',
            'message' => 'Transaction history retrieved successfully',
            'data' => $transactions
        ], 200);
    }
}
