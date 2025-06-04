<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentMethod\PaymentMethodRequest;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $outlet_id)
    {
        $paymentMethods = PaymentMethod::where('outlet_id', $outlet_id)->get();
        if ($paymentMethods->isEmpty()) {
            return response()->json(['message' => 'No payment methods found'], 404);
        }

        return response()->json([
            'message' => 'Payment methods retrieved successfully',
            'data' => $paymentMethods,
        ], 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(PaymentMethodRequest $request, string $outlet_id)
    {
        $validated = $request->validated();
        $paymentMethod = PaymentMethod::create([
            'outlet_id' => $outlet_id,
            'name' => $validated['name'],
        ]);

        if (!$paymentMethod) {
            return response()->json(['message' => 'Payment method creation failed'], 500);
        }

        return response()->json([
            'message' => 'Payment method created successfully',
            'data' => $paymentMethod,
        ], 201);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $outlet_id, string $id)
    {
        $paymentMethod = PaymentMethod::find($id);
        if (!$paymentMethod) {
            return response()->json(['message' => 'Payment method not found'], 404);
        }

        return response()->json([
            'message' => 'Payment method retrieved successfully',
            'data' => $paymentMethod,
        ], 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(PaymentMethodRequest $request, string $outlet_id, string $id)
    {
        $paymentMethod = PaymentMethod::find($id);
        if (!$paymentMethod) {
            return response()->json(['message' => 'Payment method not found'], 404);
        }

        $paymentMethod->update($request->validated());

        return response()->json([
            'message' => 'Payment method updated successfully',
            'data' => $paymentMethod,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $outlet_id, string $id)
    {
        $paymentMethod = PaymentMethod::find($id);
        if (!$paymentMethod) {
            return response()->json(['message' => 'Payment method not found'], 404);
        }

        $paymentMethod->delete();

        return response()->json(['message' => 'Payment method deleted successfully']);
    }
}
