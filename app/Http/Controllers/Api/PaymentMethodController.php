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

        return response()->json($paymentMethods);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(PaymentMethodRequest $request, string $outlet_id)
    {
        $paymentMethod = PaymentMethod::create($request->validated());

        if (!$paymentMethod) {
            return response()->json(['message' => 'Payment method creation failed'], 500);
        }

        return response()->json($paymentMethod, 201);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $paymentMethod = PaymentMethod::find($id);
        if (!$paymentMethod) {
            return response()->json(['message' => 'Payment method not found'], 404);
        }

        return response()->json($paymentMethod);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $paymentMethod = PaymentMethod::find($id);
        if (!$paymentMethod) {
            return response()->json(['message' => 'Payment method not found'], 404);
        }

        $paymentMethod->update($request->validated());

        return response()->json($paymentMethod);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $paymentMethod = PaymentMethod::find($id);
        if (!$paymentMethod) {
            return response()->json(['message' => 'Payment method not found'], 404);
        }

        $paymentMethod->delete();

        return response()->json(['message' => 'Payment method deleted successfully']);
    }
}
