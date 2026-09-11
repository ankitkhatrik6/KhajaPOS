<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::today('Asia/Kathmandu')->subDays(6)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::today('Asia/Kathmandu')->format('Y-m-d'));

        $salesSummary = $this->reportService->getSalesSummary($startDate, $endDate);
        $stockValuation = $this->reportService->getStockValuation();
        $topSelling = $this->reportService->getTopSellingItems(10, $startDate, $endDate);
        $paymentBreakdown = $this->reportService->getPaymentMethodBreakdown($startDate, $endDate);

        $lowStockItems = InventoryItem::where('status', 'active')
            ->whereRaw('current_quantity <= minimum_stock')
            ->with('category')
            ->orderBy('current_quantity')
            ->get();

        return view('reports.index', compact(
            'salesSummary',
            'stockValuation',
            'topSelling',
            'paymentBreakdown',
            'lowStockItems',
            'startDate',
            'endDate'
        ));
    }
}
