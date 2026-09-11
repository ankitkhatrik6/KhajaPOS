<?php

namespace App\Services;

use App\Models\InventoryItem;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getDashboardData(): array
    {
        $today = Carbon::today('Asia/Kathmandu');

        // Today's metrics
        $todaySalesQuery = Sale::whereDate('created_at', $today)->where('payment_status', 'Paid');
        $todaySalesTotal = (float)$todaySalesQuery->sum('total');
        $todayOrdersCount = (int)$todaySalesQuery->count();
        $todayProfit = (float)$todaySalesQuery->sum('profit');

        // Inventory metrics
        $totalItems = InventoryItem::where('status', 'active')->count();
        $lowStockItems = InventoryItem::where('status', 'active')
            ->whereRaw('current_quantity <= minimum_stock AND current_quantity > 0')
            ->with('category')
            ->get();
        $outOfStockItems = InventoryItem::where('status', 'active')
            ->whereRaw('current_quantity <= 0')
            ->with('category')
            ->get();

        // Stock Valuation
        $stockValuation = (float)InventoryItem::where('status', 'active')
            ->where('current_quantity', '>', 0)
            ->sum(DB::raw('current_quantity * purchase_price'));

        // Recent 10 sales
        $recentSales = Sale::with(['user', 'items', 'invoice'])
            ->latest()
            ->limit(10)
            ->get();

        // Last 7 days trend
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today('Asia/Kathmandu')->subDays($i);
            $dayLabel = $date->format('D, M d');
            $dateString = $date->format('Y-m-d');

            $daySales = (float)Sale::whereDate('created_at', $dateString)
                ->where('payment_status', 'Paid')
                ->sum('total');
            $dayCount = (int)Sale::whereDate('created_at', $dateString)
                ->where('payment_status', 'Paid')
                ->count();

            $last7Days[] = [
                'date' => $dateString,
                'label' => $dayLabel,
                'total' => $daySales,
                'orders' => $dayCount,
            ];
        }

        // Payment distribution today
        $paymentDistribution = Sale::select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as amount'))
            ->whereDate('created_at', $today)
            ->where('payment_status', 'Paid')
            ->groupBy('payment_method')
            ->get();

        return [
            'today_sales' => $todaySalesTotal,
            'today_orders' => $todayOrdersCount,
            'today_profit' => $todayProfit,
            'total_items' => $totalItems,
            'low_stock_count' => $lowStockItems->count(),
            'out_of_stock_count' => $outOfStockItems->count(),
            'low_stock_items' => $lowStockItems,
            'out_of_stock_items' => $outOfStockItems,
            'stock_valuation' => round($stockValuation, 2),
            'recent_sales' => $recentSales,
            'sales_trend' => $last7Days,
            'payment_distribution' => $paymentDistribution,
        ];
    }
}
