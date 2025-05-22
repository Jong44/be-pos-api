<?php

namespace App\Services;

use App\Models\Outlet;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Number;

class SellingReportService
{
    public function generate(array $data)
    {
        if (empty($data['start_date']) || empty($data['end_date'])) {
            throw new \InvalidArgumentException('Start date and end date are required.');
        }

        $about = Outlet::query()
            ->where('id', $data['outlet_id'])
            ->first();

        if (!$about) {
            throw new \InvalidArgumentException('Outlet not found.');
        }

        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);

        $transaction = Transaction::query()
            ->with(['transactionDetails', 'paymentMethod'])
            ->where('outlet_id', $data['outlet_id'])
            ->whereBetween('created_at', [
                $startDate->startOfDay(),
                $endDate->endOfDay(),
            ])
            ->get();

        if (!$transaction) {
            throw new \InvalidArgumentException('No transactions found for the given date range.');
        }



        $header = [
            'outlet_name' => $about->outlet_name,
            'address' => $about->address,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'total_transactions' => $transaction->count(),
        ];

        $reports = [];

        $totalQty = 0;
        $totalCost = 0;
        $totalGross = 0;
        $totalNet = 0;
        $totalGrossProfit = 0;
        $totalDiscount = 0;
        $totalDiscountPerItem = 0;
        $totalNetProfitBeforeDiscountSelling = 0;
        $totalNetProfitAfterDiscountSelling = 0;

        foreach ($transaction as $selling) {
            $totalBeforeDiscountPerSelling = 0;
            $totalAfterDiscountPerSelling = 0;
            $totalNetProfitPerSelling = 0;
            $totalGrossProfitPerSelling = 0;
            $totalCostPerSelling = 0;
            $totalQtyPerSelling = 0;

            foreach ($selling->transactionDetails as $item) {
                $totalQtyPerSelling += $item->qty;
                $totalCostPerSelling += $item->cost;
                $totalBeforeDiscountPerSelling += $item->price;
                $totalAfterDiscountPerSelling += $item->price;
                $totalGrossProfitPerSelling += ($item->price - $item->cost);
                $totalNetProfitPerSelling += ($item->price - $item->cost);
            }

            $discount = $selling->discout_price ?? 0;

            $totalQty += $totalQtyPerSelling;
            $totalCost += $totalCostPerSelling;
            $totalGross += $totalBeforeDiscountPerSelling;
            $totalNet += $totalAfterDiscountPerSelling;
            $totalGrossProfit += $totalGrossProfitPerSelling;
            $totalNetProfitBeforeDiscountSelling += $totalNetProfitPerSelling;
            $totalNetProfitAfterDiscountSelling += ($totalNetProfitPerSelling - $discount);
            $totalDiscount += $discount;
            $totalDiscountPerItem += $discount;
        }

        $footer = [
            'total_cost' => $this->formatCurrency($totalCost),
            'total_gross' => $this->formatCurrency($totalGross),
            'total_net' => $this->formatCurrency($totalNet - $totalDiscount),
            'total_net_price_after_discount_per_item' => $this->formatCurrency($totalNet),
            'total_net_price_after_discount_selling' => $this->formatCurrency($totalNet - $totalDiscount),
            'total_discount' => $this->formatCurrency($totalDiscount),
            'total_discount_per_item' => $this->formatCurrency($totalDiscountPerItem),
            'total_gross_profit' => $this->formatCurrency($totalGross - $totalCost),
            'total_net_profit_before_discount_selling' => $this->formatCurrency($totalNet - $totalCost),
            'total_net_profit_after_discount_selling' => $this->formatCurrency($totalNet - $totalDiscount - $totalCost),
            'total_qty' => $totalQty,
        ];

        $reports = [
            'header' => $header,
            'sellings' => $transaction,
            'footer' => $footer,
        ];

        return $reports;
    }


    private function formatCurrency($value)
    {
        return Number::format($value);
    }
}
