<?php

namespace App\Services;

use App\Models\Outlet;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Number;

class CashierReportService
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

        $transactions = Transaction::query()
            ->with(['transactionDetails.product', 'paymentMethod', 'user'])
            ->where('outlet_id', $data['outlet_id'])
            ->whereBetween('created_at', [
                $startDate->startOfDay(),
                $endDate->endOfDay(),
            ])
            ->get();

        if ($transactions->isEmpty()) {
            throw new \InvalidArgumentException('No transactions found for the given date range.');
        }

        $header = [
            'outlet_name' => $about->outlet_name,
            'address' => $about->address,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'total_transactions' => $transactions->count(),
        ];

        $reports = [];

        $totalCost = 0;
        $totalGross = 0;
        $totalNet = 0;
        $totalGrossProfit = 0;
        $totalDiscount = 0;
        $totalNetProfitBeforeDiscountSelling = 0;
        $totalNetProfitAfterDiscountSelling = 0;

        foreach ($transactions as $selling) {
            $totalBeforeDiscountPerSelling = 0;
            $totalAfterDiscountPerSelling = 0;
            $totalNetProfitPerSelling = 0;
            $totalGrossProfitPerSelling = 0;
            $totalCostPerSelling = 0;

            $items = $selling->transactionDetails->map(function ($item) use (
                &$totalBeforeDiscountPerSelling,
                &$totalAfterDiscountPerSelling,
                &$totalNetProfitPerSelling,
                &$totalGrossProfitPerSelling,
                &$totalCostPerSelling
            ) {
                $totalBeforeDiscountPerSelling += $item->price;
                $totalAfterDiscountPerSelling += $item->price;
                $totalCostPerSelling += $item->cost;
                $grossProfit = $item->price - $item->cost;
                $netProfit = $grossProfit; // no item-level discount
                $totalGrossProfitPerSelling += $grossProfit;
                $totalNetProfitPerSelling += $netProfit;

                return [
                    'product' => $item->product?->name ?? '-',
                    'quantity' => $item->qty,
                    'product_price' => $this->formatCurrency($item->price / $item->qty),
                    'product_cost' => $this->formatCurrency($item->cost / $item->qty),
                    'price' => $this->formatCurrency($item->price),
                    'cost' => $this->formatCurrency($item->cost),
                    'discount_price' => $this->formatCurrency(0),
                    'total_after_discount' => $this->formatCurrency($item->price),
                    'net_profit' => $this->formatCurrency($netProfit),
                    'gross_profit' => $this->formatCurrency($grossProfit),
                ];
            });

            $transactionDiscount = $selling->discount_price ?? 0;

            $reports[] = [
                'id' => $selling->id,
                'created_at' => Carbon::parse($selling->created_at)->format('d F Y H:i'),
                'number' => $selling->code,
                'user' => $selling->user?->username ?? $selling->user?->email,
                'transaction' => [
                    'items' => $items,
                ],
                'total' => [
                    'cost' => $this->formatCurrency($totalCostPerSelling),
                    'discount' => $this->formatCurrency($transactionDiscount),
                    'gross_selling' => $this->formatCurrency($totalBeforeDiscountPerSelling),
                    'net_selling' => $this->formatCurrency($totalAfterDiscountPerSelling),
                    'discount_selling' => $this->formatCurrency($transactionDiscount),
                    'total_net_profit' => $this->formatCurrency($totalNetProfitPerSelling),
                    'total_gross_profit' => $this->formatCurrency($totalGrossProfitPerSelling),
                    'grand_total' => $this->formatCurrency($totalAfterDiscountPerSelling - $transactionDiscount),
                ],
            ];

            // Update total for footer
            $totalCost += $totalCostPerSelling;
            $totalDiscount += $transactionDiscount;
            $totalGross += $totalBeforeDiscountPerSelling;
            $totalNet += $totalAfterDiscountPerSelling - $transactionDiscount;
            $totalGrossProfit += $totalGrossProfitPerSelling;
            $totalNetProfitBeforeDiscountSelling += $totalNetProfitPerSelling;
            $totalNetProfitAfterDiscountSelling += $totalNetProfitPerSelling - $transactionDiscount;
        }

        $footer = [
            'total_cost' => $this->formatCurrency($totalCost),
            'total_gross' => $this->formatCurrency($totalGross),
            'total_net' => $this->formatCurrency($totalNet),
            'total_discount' => $this->formatCurrency($totalDiscount),
            'total_gross_profit' => $this->formatCurrency($totalGrossProfit),
            'total_net_profit_before_discount_selling' => $this->formatCurrency($totalNetProfitBeforeDiscountSelling),
            'total_net_profit_after_discount_selling' => $this->formatCurrency($totalNetProfitAfterDiscountSelling),
        ];

        return [
            'header' => $header,
            'reports' => $reports,
            'footer' => $footer,
        ];
    }

    private function formatCurrency($value)
    {
        return Number::format($value);
    }
}
