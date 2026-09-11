@extends('layouts.app')

@section('title', 'Operational Reports & Analytics')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Operational Reports & Analytics</h1>
            <p class="text-sm text-slate-500">Analyze sales performance, inventory valuation, top-selling dishes, and payment breakdowns.</p>
        </div>
        <div class="flex items-center gap-2 no-print">
            <button onclick="window.print()" class="px-3.5 py-2 text-xs font-bold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white flex items-center gap-1.5 shadow">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Print Report</span>
            </button>
        </div>
    </div>

    <!-- Date Range Selector -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm no-print">
        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-col sm:flex-row items-end gap-3">
            <div class="flex-1">
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-500 mb-1">From Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-1 focus:ring-emerald-500">
            </div>

            <div class="flex-1">
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-500 mb-1">To Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-1 focus:ring-emerald-500">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="py-2 px-4 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-lg shadow">
                    Apply Filter
                </button>
                <a href="{{ route('reports.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-lg">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Sales Summary Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Revenue (Paid)</span>
            <div class="text-2xl font-extrabold text-slate-900 mt-2">{{ format_npr($salesSummary['total_revenue']) }}</div>
            <div class="text-xs text-slate-500 mt-1">{{ $salesSummary['total_orders'] }} orders completed</div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Estimated Cost of Goods</span>
            <div class="text-2xl font-extrabold text-slate-700 mt-2">{{ format_npr($salesSummary['total_cost']) }}</div>
            <div class="text-xs text-slate-500 mt-1">Purchase cost of items sold</div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Estimated Gross Margin</span>
            <div class="text-2xl font-extrabold text-emerald-600 mt-2">{{ format_npr($salesSummary['total_profit']) }}</div>
            <div class="text-xs text-slate-500 mt-1">
                @if($salesSummary['total_revenue'] > 0)
                {{ round(($salesSummary['total_profit'] / $salesSummary['total_revenue']) * 100, 1) }}% profit margin
                @else
                0% margin
                @endif
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Average Order Value</span>
            <div class="text-2xl font-extrabold text-indigo-600 mt-2">{{ format_npr($salesSummary['average_order_value']) }}</div>
            <div class="text-xs text-slate-500 mt-1">Per transaction</div>
        </div>
    </div>

    <!-- Stock Valuation & Payment Breakdown in 2 Columns -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Stock Valuation Report -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <i data-lucide="boxes" class="w-5 h-5 text-amber-500"></i>
                    <h2 class="text-base font-bold text-slate-900">Current Stock Valuation</h2>
                </div>
                <span class="text-xs font-semibold text-slate-400">Live Inventory Asset</span>
            </div>

            <div class="grid grid-cols-2 gap-4 text-xs">
                <div class="p-3 bg-slate-50 rounded-lg">
                    <span class="text-slate-500">Valuation at Cost (Purchase):</span>
                    <p class="text-lg font-extrabold text-slate-900 mt-1">{{ format_npr($stockValuation['total_cost_valuation']) }}</p>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg">
                    <span class="text-slate-500">Valuation at Retail (Selling):</span>
                    <p class="text-lg font-extrabold text-emerald-700 mt-1">{{ format_npr($stockValuation['total_selling_valuation']) }}</p>
                </div>
            </div>

            <div class="p-3 bg-emerald-50/60 rounded-lg border border-emerald-100 flex justify-between items-center text-xs">
                <span class="font-semibold text-emerald-900">Potential Gross Profit in Stock:</span>
                <span class="font-mono font-bold text-emerald-700 text-sm">{{ format_npr($stockValuation['potential_margin']) }}</span>
            </div>

            <div class="flex justify-between text-xs text-slate-500 pt-1">
                <span>Active Items: <strong class="text-slate-700">{{ $stockValuation['total_items'] }}</strong></span>
                <span>Low Stock: <strong class="text-amber-600">{{ $stockValuation['low_stock_count'] }}</strong></span>
                <span>Out of Stock: <strong class="text-rose-600">{{ $stockValuation['out_of_stock_count'] }}</strong></span>
            </div>
        </div>

        <!-- Payment Method Breakdown -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <i data-lucide="credit-card" class="w-5 h-5 text-indigo-500"></i>
                    <h2 class="text-base font-bold text-slate-900">Payment Breakdown</h2>
                </div>
                <span class="text-xs font-semibold text-slate-400">Selected Range</span>
            </div>

            <div class="space-y-2">
                @foreach($paymentBreakdown as $method => $data)
                <div class="flex items-center justify-between p-2.5 rounded-lg border border-slate-100 hover:bg-slate-50 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full 
                            {{ $method === 'Cash' ? 'bg-emerald-500' : '' }}
                            {{ $method === 'eSewa' ? 'bg-green-500' : '' }}
                            {{ $method === 'Khalti' ? 'bg-purple-500' : '' }}
                            {{ $method === 'Card' ? 'bg-blue-500' : '' }}"></span>
                        <span class="font-bold text-slate-900">{{ $method }}</span>
                        <span class="text-slate-400">({{ $data['count'] }} orders)</span>
                    </div>
                    <span class="font-mono font-bold text-slate-800">{{ format_npr($data['amount']) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Best Selling Items Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="flame" class="w-5 h-5 text-orange-500"></i>
                <h2 class="text-sm font-bold text-slate-800">Top 10 Best-Selling Menu Items</h2>
            </div>
            <span class="text-xs text-slate-400">Ranked by volume sold</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-100 text-slate-700 font-semibold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4"># Rank</th>
                        <th class="py-3 px-4">Menu Item</th>
                        <th class="py-3 px-4 text-center">Unit</th>
                        <th class="py-3 px-4 text-center">Quantity Sold</th>
                        <th class="py-3 px-4 text-right">Revenue (NPR)</th>
                        <th class="py-3 px-4 text-right">Gross Profit (NPR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($topSelling as $idx => $item)
                    <tr class="hover:bg-slate-50">
                        <td class="py-3 px-4 font-mono font-bold text-slate-400">#{{ $idx + 1 }}</td>
                        <td class="py-3 px-4 font-bold text-slate-900">{{ $item['item_name'] }}</td>
                        <td class="py-3 px-4 text-center text-slate-500">{{ $item['unit'] }}</td>
                        <td class="py-3 px-4 text-center font-mono font-bold text-slate-800">{{ $item['total_quantity'] }}</td>
                        <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">{{ format_npr($item['total_revenue']) }}</td>
                        <td class="py-3 px-4 text-right font-mono text-emerald-600 font-semibold">{{ format_npr($item['total_profit']) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400">
                            No sales data recorded in this period.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
