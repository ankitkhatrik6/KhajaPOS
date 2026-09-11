<?php

namespace App\Services;

use App\Models\InventoryItem;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Get sales summary by date range.
     */
    public function getSalesSummary(?string $startDate = null, ?string $endDate = null): array
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::today('Asia/Kathmandu')->startOfDay();
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::today('Asia/Kathmandu')->endOfDay();

        $query = Sale::whereBetween('created_at', [$start, $end])
            ->where('payment_status', 'Paid');

        $totalRevenue = (float)$query->sum('total');
        $totalCost = (float)$query->sum('cost');
        $totalDiscount = (float)$query->sum('discount');
        $totalTax = (float)$query->sum('tax');
        $totalProfit = (float)$query->sum('profit');
        $totalOrders = (int)$query->count();
        $averageOrderValue = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0.00;

        return [
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
            'total_revenue' => $totalRevenue,
            'total_cost' => $totalCost,
            'total_discount' => $totalDiscount,
            'total_tax' => $totalTax,
            'total_profit' => $totalProfit,
            'total_orders' => $totalOrders,
            'average_order_value' => $averageOrderValue,
        ];
    }

    /**
     * Get stock valuation.
     */
    public function getStockValuation(): array
    {
        $items = InventoryItem::where('status', 'active')->get();

        $totalItems = $items->count();
        $totalValuation = 0.00;
        $totalExpectedRevenue = 0.00;
        $lowStockCount = 0;
        $outOfStockCount = 0;

        foreach ($items as $item) {
            $qty = (float)$item->current_quantity;
            $purchasePrice = (float)$item->purchase_price;
            $sellingPrice = (float)$item->selling_price;

            if ($qty > 0) {
                $totalValuation += ($qty * $purchasePrice);
                $totalExpectedRevenue += ($qty * $sellingPrice);
            }

            if ($item->isOutOfStock()) {
                $outOfStockCount++;
            } elseif ($item->isLowStock()) {
                $lowStockCount++;
            }
        }

        return [
            'total_items' => $totalItems,
            'total_cost_valuation' => round($totalValuation, 2),
            'total_selling_valuation' => round($totalExpectedRevenue, 2),
            'potential_margin' => round($totalExpectedRevenue - $totalValuation, 2),
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
        ];
    }

    /**
     * Top selling items by quantity and revenue.
     */
    public function getTopSellingItems(int $limit = 10, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = SaleItem::select(
            'sale_items.item_name',
            'sale_items.unit',
            DB::raw('SUM(sale_items.quantity) as total_quantity'),
            DB::raw('SUM(sale_items.subtotal) as total_revenue'),
            DB::raw('SUM(sale_items.profit) as total_profit')
        )
        ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
        ->where('sales.payment_status', 'Paid');

        if ($startDate && $endDate) {
            $query->whereBetween('sales.created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        }

        return $query->groupBy('sale_items.item_name', 'sale_items.unit')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Breakdown by payment method.
     */
    public function getPaymentMethodBreakdown(?string $startDate = null, ?string $endDate = null): array
    {
        $query = Sale::select(
            'payment_method',
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total) as total_amount')
        )
        ->where('payment_status', 'Paid');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        }

        $results = $query->groupBy('payment_method')->get();
        $formatted = [
            'Cash' => ['count' => 0, 'amount' => 0.00],
            'Card' => ['count' => 0, 'amount' => 0.00],
            'eSewa' => ['count' => 0, 'amount' => 0.00],
            'Khalti' => ['count' => 0, 'amount' => 0.00],
        ];

        foreach ($results as $row) {
            if (isset($formatted[$row->payment_method])) {
                $formatted[$row->payment_method] = [
                    'count' => (int)$row->count,
                    'amount' => (float)$row->total_amount,
                ];
            }
        }

        return $formatted;
    }
}
