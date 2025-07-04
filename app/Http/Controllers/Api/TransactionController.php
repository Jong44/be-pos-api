<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\TransactionRequest;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function createTransaction(TransactionRequest $request, string $outlet_id)
{
    $validated = $request->validated();

    DB::beginTransaction();

    try {
        $validated['outlet_id'] = $outlet_id;
        $validated['cashier_id'] = auth()->user()->id;
        $validated['code'] = Transaction::generateCustomCode();

        // validate voucher if exists
        if (isset($validated['voucher_id'])) {
            $voucher = \App\Models\Voucher::find($validated['voucher_id']);
            if (!$voucher) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Voucher not found'
                ], 404);
            }

            // Check if voucher is valid for this outlet
            if ($voucher->outlet_id !== $outlet_id) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Voucher is not valid for this outlet'
                ], 400);
            }

            // Check if voucher is still valid
            if ($voucher->expired_at < now()) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Voucher has expired'
                ], 400);
            }

            // Check if voucher is already used
            if ($voucher->is_used) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Voucher has already been used'
                ], 400);
            }
        }

        $transaction = Transaction::create($validated);

        $products = collect($request->products);
        $productIds = $products->pluck('product_id')->all();

        $productModels = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $details = [];

        foreach ($products as $product) {
            $productModel = $productModels->get($product['product_id']);

            if (!$productModel) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => "Product not found: {$product['product_id']}"
                ], 404);
            }

            $qty = $product['qty'];

            if ($qty < 1) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => "Invalid quantity for product: {$product['product_id']}"
                ], 400);
            }

            $details[] = [
                'code' => $validated['code'],
                'transaction_id' => $transaction->id,
                'product_id' => $product['product_id'],
                'qty' => $qty,
                'price' => $productModel->selling_price * $qty,
                'cost' => $productModel->initial_price * $qty,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $productModel->decrement('stock', $qty);
        }

        foreach ($details as $detail) {

            TransactionDetail::create($detail);
        }

        // Hapus cart
        Cart::where('outlet_id', $outlet_id)
            ->where('user_id', auth()->id())
            ->delete();

        DB::commit();

        return response()->json([
            'status' => 'success',
            'message' => 'Transaction created successfully',
            'data' => $transaction->load('transactionDetails')
        ], 201);
    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('Transaction creation failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to create transactions'
        ], 500);
    }
}

    public function getHistoryTransaction(string $outlet_id)
    {
        $transactions = TransactionDetail::with(['transaction', 'product'])
            ->whereHas('transaction.paymentMethod, transaction.voucher',  function ($query) use ($outlet_id) {
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
