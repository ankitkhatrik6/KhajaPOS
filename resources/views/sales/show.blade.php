@extends('layouts.app')

@section('title', 'Sale Details - ' . $sale->sale_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Order #{{ $sale->sale_number }}</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                    {{ $sale->payment_status }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Recorded on {{ $sale->created_at->setTimezone('Asia/Kathmandu')->format('l, d F Y, h:i A') }} (NPT)</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('sales.index') }}" class="px-3 py-2 rounded-lg text-xs font-semibold bg-white border border-slate-300 text-slate-700 hover:bg-slate-50">
                &larr; Back to Sales
            </a>
            @if($sale->invoice)
            <a href="{{ route('invoices.receipt', $sale->invoice->id) }}?autoprint=1" target="_blank" class="px-3.5 py-2 rounded-lg text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white flex items-center gap-1.5 shadow">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Print Thermal Receipt</span>
            </a>
            <a href="{{ route('invoices.show', $sale->invoice->id) }}" class="px-3.5 py-2 rounded-lg text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white flex items-center gap-1.5 shadow">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                <span>View Invoice</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Metadata Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-4 border border-slate-200">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Customer</span>
            <div class="text-sm font-bold text-slate-900 mt-1">{{ $sale->customer_name }}</div>
            <div class="text-xs text-slate-500 mt-0.5">Cashier: {{ $sale->user->name ?? 'Staff' }}</div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Payment</span>
            <div class="text-sm font-bold text-slate-900 mt-1">{{ $sale->payment_method }}</div>
            <div class="text-xs text-slate-500 mt-0.5">Status: {{ $sale->payment_status }}</div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Invoice Number</span>
            <div class="text-sm font-bold font-mono text-emerald-700 mt-1">
                {{ $sale->invoice->invoice_number ?? 'Not Generated' }}
            </div>
            <div class="text-xs text-slate-500 mt-0.5">Currency: Nepalese Rupee (NPR)</div>
        </div>
    </div>

    <!-- Items List Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50 font-bold text-sm text-slate-800">
            Itemized Order Breakdown
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-100 text-slate-700 font-semibold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Item Name</th>
                        <th class="py-3 px-4 text-center">Unit</th>
                        <th class="py-3 px-4 text-center">Quantity</th>
                        <th class="py-3 px-4 text-right">Unit Price (NPR)</th>
                        <th class="py-3 px-4 text-right">Line Subtotal</th>
                        <th class="py-3 px-4 text-right">Profit Est.</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($sale->items as $item)
                    <tr>
                        <td class="py-3 px-4 font-bold text-slate-900">
                            {{ $item->item_name }}
                        </td>
                        <td class="py-3 px-4 text-center text-slate-500">
                            {{ $item->unit }}
                        </td>
                        <td class="py-3 px-4 text-center font-mono font-bold text-slate-800">
                            {{ $item->quantity }}
                        </td>
                        <td class="py-3 px-4 text-right font-mono">
                            {{ format_npr($item->unit_price) }}
                        </td>
                        <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">
                            {{ format_npr($item->subtotal) }}
                        </td>
                        <td class="py-3 px-4 text-right font-mono text-emerald-600">
                            {{ format_npr($item->profit) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Financial Summary -->
        <div class="p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row sm:justify-between items-start sm:items-center gap-4">
            <div class="text-xs text-slate-500 space-y-1">
                <div>Total Estimated Food Cost: <span class="font-mono font-bold text-slate-700">{{ format_npr($sale->cost) }}</span></div>
                <div>Net Margin on this order: <span class="font-mono font-bold text-emerald-600">{{ format_npr($sale->profit) }}</span></div>
            </div>

            <div class="w-full sm:w-64 space-y-1.5 text-xs text-slate-700">
                <div class="flex justify-between">
                    <span>Subtotal:</span>
                    <span class="font-mono font-semibold">{{ format_npr($sale->subtotal) }}</span>
                </div>
                @if($sale->discount > 0)
                <div class="flex justify-between text-rose-600">
                    <span>Discount:</span>
                    <span class="font-mono">-{{ format_npr($sale->discount) }}</span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span>VAT Tax:</span>
                    <span class="font-mono">{{ format_npr($sale->tax) }}</span>
                </div>
                <div class="pt-2 border-t border-slate-200 flex justify-between text-base font-extrabold text-slate-900">
                    <span>Grand Total:</span>
                    <span class="font-mono text-emerald-700">{{ format_npr($sale->total) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
