<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\OpenBill;
use App\Models\OpenBillDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OpenBillController extends Controller
{
    public function createOpenBill(Request $request, string $outlet_id)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'voucher_id' => 'nullable|uuid',
            'discount_price' => 'nullable|numeric',
            'total_qty' => 'required|numeric',
            'total_price' => 'required|numeric',
            'products' => 'required|array',
            'products.*.product_id' => 'required|uuid|exists:products,id',
            'products.*.qty' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();

        try {
            $validated['open_at'] = now();
            $validated['status'] = 'open';
            $validated['code'] = OpenBill::generateCustomCode('BILL', 'code', 3);
            $validated['outlet_id'] = $outlet_id;
            $validated['cashier_id'] = auth()->user()->id;

            $openBill = OpenBill::create($validated);

            $this->createBillDetails($request->products, $openBill);

            Cart::where('outlet_id', $outlet_id)
                ->where('user_id', auth()->id())
                ->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'data' => $openBill->load(['details', 'cashier']),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Create Open Bill Failed', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function getOpenBills(string $outlet_id)
    {
        $openBills = OpenBill::where('outlet_id', $outlet_id)
            ->where('cashier_id', auth()->id())
            ->with(['details.product', 'cashier'])
            ->get();

        if ($openBills->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No open bills found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $openBills,
        ]);
    }

    public function getOpenBillById(string $outlet_id, string $id)
    {
        $openBill = OpenBill::where('outlet_id', $outlet_id)
            ->where('cashier_id', auth()->id())
            ->with(['details.product', 'cashier'])
            ->find($id);

        if (!$openBill) {
            return response()->json([
                'status' => 'error',
                'message' => 'Open bill not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $openBill,
        ]);
    }

    public function deleteOpenBill(string $outlet_id, string $id)
    {
        $openBill = OpenBill::where('outlet_id', $outlet_id)->find($id);

        if (!$openBill) {
            return response()->json([
                'status' => 'error',
                'message' => 'Open bill not found'
            ], 404);
        }

        $openBill->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Open bill deleted successfully'
        ]);
    }

    public function closeOpenBill(string $outlet_id, string $id)
    {
        $openBill = OpenBill::where('outlet_id', $outlet_id)->find($id);

        if (!$openBill) {
            return response()->json([
                'status' => 'error',
                'message' => 'Open bill not found'
            ], 404);
        }

        $openBill->update([
            'closed_at' => now(),
            'status' => 'closed',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Open bill closed successfully'
        ]);
    }

    public function updateOpenBill(Request $request, string $outlet_id, string $id)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'voucher_id' => 'nullable|uuid',
            'discount_price' => 'nullable|numeric',
            'total_qty' => 'required|numeric',
            'total_price' => 'required|numeric',
            'products' => 'required|array',
            'products.*.product_id' => 'required|uuid|exists:products,id',
            'products.*.qty' => 'required|numeric|min:1',
        ]);

        $openBill = OpenBill::with('details')->where('outlet_id', $outlet_id)->find($id);
        if (!$openBill) {
            return response()->json([
                'status' => 'error',
                'message' => 'Open bill not found'
            ], 404);
        }

        DB::beginTransaction();

        try {
            $existingDetails = $openBill->details->keyBy('product_id');
            $newProducts = collect($validated['products'])->keyBy('product_id');

            foreach ($newProducts as $productId => $newData) {
                $product = Product::findOrFail($productId);
                $newQty = $newData['qty'];

                if ($existingDetails->has($productId)) {
                    $existingDetail = $existingDetails->get($productId);
                    $qtyDiff = $newQty - $existingDetail->qty;

                    if ($qtyDiff !== 0) {
                        $existingDetail->update([
                            'qty' => $newQty,
                            'price' => $product->selling_price * $newQty,
                            'cost' => $product->initial_price * $newQty,
                        ]);

                        $product->decrement('stock', $qtyDiff);
                    }

                    $existingDetails->forget($productId); // Mark as handled
                } else {
                    // New product
                    $openBill->details()->create([
                        'code' => $openBill->code,
                        'product_id' => $product->id,
                        'qty' => $newQty,
                        'price' => $product->selling_price * $newQty,
                        'cost' => $product->initial_price * $newQty,
                    ]);

                    $product->decrement('stock', $newQty);
                }
            }

            // Handle deletions
            foreach ($existingDetails as $detailToDelete) {
                $product = Product::find($detailToDelete->product_id);
                if ($product) {
                    $product->increment('stock', $detailToDelete->qty);
                }

                $detailToDelete->delete();
            }

            // Update parent open bill data
            $openBill->update([
                'customer_name' => $validated['customer_name'],
                'voucher_id' => $validated['voucher_id'] ?? null,
                'discount_price' => $validated['discount_price'] ?? 0,
                'total_qty' => $validated['total_qty'],
                'total_price' => $validated['total_price'],
            ]);

            Cart::where('outlet_id', $outlet_id)
                ->where('user_id', auth()->id())
                ->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Open bill updated successfully',
                'data' => $openBill->fresh('details'),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Update Open Bill Failed', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong during update',
            ], 500);
        }
    }


    private function createBillDetails(array $products, OpenBill $openBill): void
    {
        $details = [];

        foreach ($products as $productData) {
            $product = Product::findOrFail($productData['product_id']);

            $qty = $productData['qty'];
            $price = $product->selling_price * $qty;
            $cost = $product->initial_price * $qty;

            $details[] = [
                'code' => $openBill->code,
                'product_id' => $product->id,
                'qty' => $qty,
                'price' => $price,
                'cost' => $cost,
            ];

            // Decrement stock
            $product->decrement('stock', $qty);
        }

        $openBill->details()->createMany(records: $details);
    }
}
