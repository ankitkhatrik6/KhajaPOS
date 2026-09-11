@extends('layouts.app')

@section('title', 'Dashboard - Overview')

@section('content')
<div class="space-y-6">
    <!-- Top Greeting & Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Operational Dashboard</h1>
            <p class="text-sm text-slate-500">Real-time overview of restaurant sales, inventory health, and stock valuation in Nepal (NPR).</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('inventory.index', ['status' => 'low_stock']) }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100 transition">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600"></i>
                <span>Low Stock Alerts ({{ $low_stock_count }})</span>
            </a>
            <a href="{{ route('pos.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white shadow-sm transition">
                <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                <span>Open POS Terminal</span>
            </a>
        </div>
    </div>

    <!-- 4 Key Stat Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Today Sales -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Today's Sales (NPR)</span>
                <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="banknote" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ format_npr($today_sales) }}</div>
                <div class="mt-1 flex items-center text-xs text-slate-500">
                    <span class="font-medium text-emerald-600 mr-1.5">{{ $today_orders }} orders</span>
                    <span>completed today</span>
                </div>
            </div>
        </div>

        <!-- Today Est. Profit -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Today's Gross Profit</span>
                <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-extrabold text-indigo-600 tracking-tight">{{ format_npr($today_profit) }}</div>
                <div class="mt-1 text-xs text-slate-500">
                    Revenue minus item ingredient cost
                </div>
            </div>
        </div>

        <!-- Total Stock Valuation -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Stock Valuation (Cost)</span>
                <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i data-lucide="boxes" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ format_npr($stock_valuation) }}</div>
                <div class="mt-1 text-xs text-slate-500 flex items-center gap-2">
                    <span>{{ $total_items }} active items</span>
                    @if($out_of_stock_count > 0)
                    <span class="text-rose-600 font-semibold">({{ $out_of_stock_count }} out of stock)</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Low Stock Items Warning Card -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Inventory Status</span>
                <div class="w-9 h-9 rounded-lg {{ $low_stock_count > 0 ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600' }} flex items-center justify-center">
                    <i data-lucide="{{ $low_stock_count > 0 ? 'alert-triangle' : 'check-circle-2' }}" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-extrabold {{ $low_stock_count > 0 ? 'text-rose-600' : 'text-emerald-600' }} tracking-tight">
                    {{ $low_stock_count }} <span class="text-sm font-normal text-slate-500">Low Stock Alert{{ $low_stock_count == 1 ? '' : 's' }}</span>
                </div>
                <div class="mt-1 text-xs text-slate-500">
                    Requires purchase restock
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section: 7-day Sales Trend & Payment Split -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sales Trend Line Chart (2 Cols) -->
        <div class="lg:col-span-2 bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700">7-Day Sales Trend (NPR)</h2>
                    <p class="text-xs text-slate-400">Daily revenue performance for Himalayan Flavors</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded bg-slate-100 text-slate-600">Past 7 Days</span>
            </div>
            <div class="h-64">
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>

        <!-- Payment Methods Doughnut (1 Col) -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700">Payment Breakdown</h2>
                    <p class="text-xs text-slate-400">Cash vs eSewa vs Khalti vs Card</p>
                </div>
            </div>
            <div class="h-64 flex items-center justify-center">
                <canvas id="paymentMethodChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Two Tables Grid: Low Stock Alert & Recent Sales -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Low Stock Items Warning Table -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-amber-600"></i>
                    <h2 class="text-sm font-bold text-slate-800">Items Needing Restock</h2>
                </div>
                <a href="{{ route('inventory.index', ['status' => 'low_stock']) }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                    View All Stock &rarr;
                </a>
            </div>
            <div class="flex-1 overflow-x-auto">
                @if($low_stock_items->isEmpty() && $out_of_stock_items->isEmpty())
                <div class="p-8 text-center text-slate-400 text-sm">
                    <i data-lucide="check-circle-2" class="w-8 h-8 text-emerald-500 mx-auto mb-2"></i>
                    <p>All items have sufficient inventory levels.</p>
                </div>
                @else
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-100 text-slate-500 font-semibold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-4">Item Name</th>
                            <th class="py-2.5 px-4">Category</th>
                            <th class="py-2.5 px-4 text-right">In Stock</th>
                            <th class="py-2.5 px-4 text-right">Min Stock</th>
                            <th class="py-2.5 px-4 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($out_of_stock_items as $item)
                        <tr class="hover:bg-rose-50/50">
                            <td class="py-2.5 px-4 font-semibold text-slate-900">{{ $item->name }}</td>
                            <td class="py-2.5 px-4">{{ $item->category->name ?? '-' }}</td>
                            <td class="py-2.5 px-4 text-right font-mono font-bold text-rose-600">0.00 {{ $item->unit }}</td>
                            <td class="py-2.5 px-4 text-right font-mono">{{ $item->minimum_stock }} {{ $item->unit }}</td>
                            <td class="py-2.5 px-4 text-right">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">OUT OF STOCK</span>
                            </td>
                        </tr>
                        @endforeach
                        @foreach($low_stock_items as $item)
                        <tr class="hover:bg-amber-50/50">
                            <td class="py-2.5 px-4 font-semibold text-slate-900">{{ $item->name }}</td>
                            <td class="py-2.5 px-4">{{ $item->category->name ?? '-' }}</td>
                            <td class="py-2.5 px-4 text-right font-mono font-bold text-amber-600">{{ $item->current_quantity }} {{ $item->unit }}</td>
                            <td class="py-2.5 px-4 text-right font-mono">{{ $item->minimum_stock }} {{ $item->unit }}</td>
                            <td class="py-2.5 px-4 text-right">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">LOW STOCK</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>

        <!-- Recent Sales Table -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-2">
                    <i data-lucide="receipt" class="w-4 h-4 text-sky-600"></i>
                    <h2 class="text-sm font-bold text-slate-800">Recent Sales & Invoices</h2>
                </div>
                <a href="{{ route('sales.index') }}" class="text-xs font-semibold text-sky-600 hover:text-sky-700">
                    View All Sales &rarr;
                </a>
            </div>
            <div class="flex-1 overflow-x-auto">
                @if($recent_sales->isEmpty())
                <div class="p-8 text-center text-slate-400 text-sm">
                    No sales recorded yet. Open POS Billing to start!
                </div>
                @else
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-100 text-slate-500 font-semibold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-4">Sale #</th>
                            <th class="py-2.5 px-4">Customer</th>
                            <th class="py-2.5 px-4">Method</th>
                            <th class="py-2.5 px-4 text-right">Total (NPR)</th>
                            <th class="py-2.5 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recent_sales as $sale)
                        <tr class="hover:bg-slate-50">
                            <td class="py-2.5 px-4 font-mono font-semibold text-slate-900">{{ $sale->sale_number }}</td>
                            <td class="py-2.5 px-4">{{ $sale->customer_name }}</td>
                            <td class="py-2.5 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold 
                                    {{ $sale->payment_method === 'Cash' ? 'bg-emerald-50 text-emerald-700' : '' }}
                                    {{ $sale->payment_method === 'eSewa' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $sale->payment_method === 'Khalti' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $sale->payment_method === 'Card' ? 'bg-blue-50 text-blue-700' : '' }}">
                                    {{ $sale->payment_method }}
                                </span>
                            </td>
                            <td class="py-2.5 px-4 text-right font-mono font-bold text-slate-900">{{ format_npr($sale->total) }}</td>
                            <td class="py-2.5 px-4 text-right">
                                @if($sale->invoice)
                                <a href="{{ route('invoices.show', $sale->invoice->id) }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold">
                                    Invoice
                                </a>
                                @else
                                <a href="{{ route('sales.show', $sale->id) }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold">
                                    View
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Sales Trend Chart
    const trendData = @json($sales_trend);
    const labels = trendData.map(d => d.label);
    const totals = trendData.map(d => d.total);

    const trendCtx = document.getElementById('salesTrendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Sales (NPR)',
                data: totals,
                borderColor: '#16a34a',
                backgroundColor: 'rgba(22, 163, 74, 0.1)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.3,
                pointRadius: 4,
                pointBackgroundColor: '#16a34a'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            return 'Rs. ' + Number(ctx.parsed.y).toLocaleString('en-IN', {minimumFractionDigits: 2});
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rs. ' + value.toLocaleString('en-IN');
                        }
                    },
                    grid: { color: '#f1f5f9' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // 2. Payment Method Chart
    const paymentData = @json($payment_distribution);
    let payLabels = paymentData.map(p => p.payment_method);
    let payCounts = paymentData.map(p => p.amount);

    if (payLabels.length === 0) {
        payLabels = ['Cash', 'eSewa', 'Khalti', 'Card'];
        payCounts = [1, 0, 0, 0];
    }

    const payCtx = document.getElementById('paymentMethodChart').getContext('2d');
    new Chart(payCtx, {
        type: 'doughnut',
        data: {
            labels: payLabels,
            datasets: [{
                data: payCounts,
                backgroundColor: [
                    '#16a34a', // Cash - emerald
                    '#22c55e', // eSewa - green
                    '#7c3aed', // Khalti - purple
                    '#2563eb'  // Card - blue
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, font: { size: 11 } }
                }
            },
            cutout: '65%'
        }
    });
});
</script>
@endpush
