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

    public function getDetailTransaction(string $outlet_id, string $transaction_id)
    {
        $transaction = TransactionDetail::with(['transaction', 'product'])
            ->whereHas('transaction', function ($query) use ($outlet_id) {
                $query->where('outlet_id', $outlet_id);
            })
            ->where('transaction_id', $transaction_id)
            ->first();

        if (!$transaction) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaction not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Transaction detail retrieved successfully',
            'data' => $transaction
        ], 200);
    }

    public function getTodayIncome(string $outlet_id)
    {
        $today = now()->format('Y-m-d');
        // get just total price from transaction
        $transactions = Transaction::where('outlet_id', $outlet_id)
            ->whereDate('created_at', $today)
            ->get();

        $income = 0;
        foreach ($transactions as $transaction) {
            $income += $transaction->total_price;
        }

        if ($transactions->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No transactions found for today'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Today income retrieved successfully',
            'data' => [
                'income' => $income,
                'date' => $today,
            ]
        ], 200);
    }

    public function getTodaySell(string $outlet_id)
    {
        $today = now()->format('Y-m-d');
        // get just total price from transaction
        $transactions = Transaction::where('outlet_id', $outlet_id)
            ->whereDate('created_at', $today)
            ->get();

        $sell = 0;
        foreach ($transactions as $transaction) {
            $sell += $transaction->total_qty;
        }

        if ($transactions->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No transactions found for today'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Today sell retrieved successfully',
            'data' => [
                'sell' => $sell,
                'date' => $today,
            ]
        ], 200);
    }

    public function getSumTodayTransaction(string $outlet_id)
    {
        $today = now()->format('Y-m-d');

        // get total transaction
        $transactions = Transaction::where('outlet_id', $outlet_id)
            ->whereDate('created_at', $today)
            ->get();
        $totalTransaction = 0;
        foreach ($transactions as $transaction) {
            $totalTransaction += 1;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Today transaction retrieved successfully',
            'data' => [
                'transactions' => $transactions,
                'date' => $today,
            ]
        ], 200);
    }

    public function getBestSellingProduct(string $outlet_id)
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

        $bestSellingProduct = [];
        foreach ($transactions as $transaction) {
            if (!isset($bestSellingProduct[$transaction->product->id])) {
                $bestSellingProduct[$transaction->product->id] = [
                    'product' => $transaction->product,
                    'qty' => 0,
                ];
            }
            $bestSellingProduct[$transaction->product->id]['qty'] += $transaction->qty;
        }

        usort($bestSellingProduct, function ($a, $b) {
            return $b['qty'] <=> $a['qty'];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Best selling product retrieved successfully',
            'data' => array_values($bestSellingProduct)
        ], 200);
    }
}
