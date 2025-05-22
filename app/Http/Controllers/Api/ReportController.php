<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CashierReportService;
use App\Services\SellingReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function generateReportSellings(Request $request, string $outlet_id)
    {
        $validatedData = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $sellings = new SellingReportService();

        $reportData = $sellings->generate([
            'outlet_id' => $outlet_id,
            'start_date' => $validatedData['start_date'],
            'end_date' => $validatedData['end_date'],
        ]);

        if (empty($reportData)) {
            return response()->json(['message' => 'No transactions found for the given date range.'], 404);
        }

        return response()->json([
            'data' => $reportData,
            'message' => 'Report generated successfully',
        ], 200);
    }

    public function exportReportSellings(Request $request, string $outlet_id)
    {
        $validatedData = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $sellings = new SellingReportService();
        $reportData = $sellings->generate([
            'outlet_id' => $outlet_id,
            'start_date' => $validatedData['start_date'],
            'end_date' => $validatedData['end_date'],
        ]);

        if (empty($reportData)) {
            return response()->json(['message' => 'No transactions found for the given date range.'], 404);
        }



        $reports = $reportData['sellings'];
        $footer = $reportData['footer'];
        $header = $reportData['header'];

        $pdf = Pdf::loadView('reports.selling', compact('reports', 'footer', 'header'));
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        return $pdf->download('sellings_report_' . date('Y-m-d') . '.pdf');

    }

    public function generateReportCashier(Request $request, string $outlet_id)
    {
        $validatedData = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $cashiers = new CashierReportService();

        $reportData = $cashiers->generate([
            'outlet_id' => $outlet_id,
            'start_date' => $validatedData['start_date'],
            'end_date' => $validatedData['end_date'],
        ]);

        if (empty($reportData)) {
            return response()->json(['message' => 'No transactions found for the given date range.'], 404);
        }

        return response()->json([
            'data' => $reportData,
            'message' => 'Report generated successfully',
        ], 200);
    }

    public function exportReportCashier(Request $request, string $outlet_id)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $cashiers = new CashierReportService();
        // Generate the report using the service
        $reportData = $cashiers->generate([
            'outlet_id' => $outlet_id,
            'start_date' => $validatedData['start_date'],
            'end_date' => $validatedData['end_date'],
        ]);

        if (empty($reportData)) {
            return response()->json(['message' => 'No transactions found for the given date range.'], 404);
        }

        $reports = $reportData['reports'];
        $footer = $reportData['footer'];
        $header = $reportData['header'];

        $pdf = Pdf::loadView('reports.cashier', compact('reports', 'footer', 'header'));
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        // Download the PDF file
        return $pdf->download('cashiers_report_' . date('Y-m-d') . '.pdf');

    }


}
