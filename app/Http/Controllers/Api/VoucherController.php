<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Voucher\VoucherRequest;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $outlet_id)
    {
        $vouchers = Voucher::where('outlet_id', $outlet_id)->get();
        if ($vouchers->isEmpty()) {
            return response()->json(['message' => 'No vouchers found'], 404);
        }

        return response()->json($vouchers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VoucherRequest $request, string $outlet_id)
    {
        $request->merge(['outlet_id' => $outlet_id]);
        $voucher = Voucher::create($request->validated());

        if (!$voucher) {
            return response()->json(['message' => 'Voucher creation failed'], 500);
        }

        return response()->json($voucher, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $voucher = Voucher::find($id);
        if (!$voucher) {
            return response()->json(['message' => 'Voucher not found'], 404);
        }

        return response()->json($voucher);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $voucher = Voucher::find($id);
        if (!$voucher) {
            return response()->json(['message' => 'Voucher not found'], 404);
        }

        $voucher->update($request->all());

        return response()->json($voucher);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $voucher = Voucher::find($id);
        if (!$voucher) {
            return response()->json(['message' => 'Voucher not found'], 404);
        }

        $voucher->delete();

        return response()->json(['message' => 'Voucher deleted successfully']);
    }
}
