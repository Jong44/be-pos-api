<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $outlet = DB::table('outlets')->first();
        $products = DB::table('products')->where('outlet_id', $outlet->id)->get();
        $paymentMethods = DB::table('payment_methods')->where('outlet_id', $outlet->id)->get();
        $user = DB::table('users')->first();

        // Generate N transaction codes
        $transactionCodes = [];
        for ($i = 0; $i < 5; $i++) {
            $transactionCodes[] = strtoupper(Str::random(8));
        }

        foreach ($transactionCodes as $code) {
            $transaction = Transaction::create([
                'cashier_id' => $user->id,
                'outlet_id' => $outlet->id,
                'date' => now(),
                'note' => 'Test transaction',
                'voucher_id' => null,
                'discout_price' => 0,
                'code' => $code,
                'payed_money' => 0,
                'money_changes' => 0,
                'total_price' => 0,
                'total_cost' => 0,
                'payment_method_id' => $paymentMethods->random()->id,
                'tax' => 0,
                'tax_price' => 0,
                'total_qty' => 0,
            ]);

            $totalPrice = 0;
            $totalCost = 0;
            $totalQty = 0;

            foreach ($products->random(3) as $product) {
                $qty = rand(1, 5);
                $price = $product->selling_price * $qty;
                $cost = $product->initial_price * $qty;

                TransactionDetail::create([
                    'code' => $code,
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'qty' => $qty,
                    'price' => $price,
                    'cost' => $cost,
                ]);

                $totalPrice += $price;
                $totalCost += $cost;
                $totalQty += $qty;
            }

            // Update the transaction totals
            $transaction->update([
                'total_price' => $totalPrice,
                'total_cost' => $totalCost,
                'total_qty' => $totalQty,
                'payed_money' => $totalPrice,
                'money_changes' => 0, // assuming full payment
            ]);
        }
    }
}
