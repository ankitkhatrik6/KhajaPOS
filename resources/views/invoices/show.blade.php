@extends('layouts.app')

@section('title', 'Tax Invoice - ' . $invoice->invoice_number)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header Bar -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('invoices.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg bg-white border border-slate-300 text-slate-700 hover:bg-slate-50">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Back to Invoices</span>
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('invoices.receipt', $invoice->id) }}?autoprint=1" target="_blank" class="px-4 py-2 text-xs font-bold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white flex items-center gap-1.5 shadow">
                <i data-lucide="receipt" class="w-4 h-4"></i>
                <span>Thermal 80mm Receipt</span>
            </a>
            <a href="{{ route('invoices.print', $invoice->id) }}?autoprint=1" target="_blank" class="px-4 py-2 text-xs font-bold rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white flex items-center gap-1.5 shadow">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Print A4 Invoice</span>
            </a>
        </div>
    </div>

    <!-- The Invoice Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-md p-8 text-slate-800 space-y-6">
        <!-- Top Restaurant Info -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-6 border-b border-slate-200 gap-4">
            <div>
                <h2 class="text-xl font-black tracking-tight text-slate-900 uppercase">{{ $invoice->restaurant_name }}</h2>
                <p class="text-xs text-slate-500 mt-1">{{ $invoice->restaurant_address }}</p>
                <p class="text-xs text-slate-500">Phone: {{ $invoice->restaurant_phone }}</p>
                @if($invoice->restaurant_pan)
                <p class="text-xs font-mono font-bold text-slate-700 mt-0.5">PAN No: {{ $invoice->restaurant_pan }} | {{ $invoice->restaurant_vat }}</p>
                @endif
            </div>
            <div class="sm:text-right">
                <span class="inline-block px-3 py-1 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 font-mono font-bold text-xs">
                    TAX INVOICE
                </span>
                <p class="text-xs font-mono font-bold text-slate-800 mt-2">{{ $invoice->invoice_number }}</p>
                <p class="text-xs text-slate-500 font-mono">{{ $invoice->created_at->setTimezone('Asia/Kathmandu')->format('Y-m-d h:i A') }} NPT</p>
            </div>
        </div>

        <!-- Customer & Cashier Details -->
        <div class="grid grid-cols-2 gap-4 text-xs py-2 bg-slate-50 p-4 rounded-xl border border-slate-100">
            <div>
                <span class="text-slate-400 uppercase font-semibold text-[10px]">Billed To:</span>
                <p class="font-bold text-slate-900 mt-0.5 text-sm">{{ $invoice->customer_name }}</p>
                <p class="text-slate-500 mt-0.5">Payment Method: <span class="font-semibold text-slate-800">{{ $invoice->payment_method }}</span></p>
            </div>
            <div class="text-right">
                <span class="text-slate-400 uppercase font-semibold text-[10px]">Cashier / Server:</span>
                <p class="font-bold text-slate-900 mt-0.5 text-sm">{{ $invoice->cashier_name }}</p>
                <p class="text-slate-500 mt-0.5">Status: <span class="font-bold text-emerald-600">{{ $invoice->payment_status }}</span></p>
            </div>
        </div>

        <!-- Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="border-b-2 border-slate-900 text-slate-900 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-2.5 px-2">#</th>
                        <th class="py-2.5 px-2">Item Description</th>
                        <th class="py-2.5 px-2 text-center">Unit</th>
                        <th class="py-2.5 px-2 text-center">Qty</th>
                        <th class="py-2.5 px-2 text-right">Rate (NPR)</th>
                        <th class="py-2.5 px-2 text-right">Total (NPR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($invoice->sale->items as $idx => $item)
                    <tr>
                        <td class="py-2.5 px-2 text-slate-400 font-mono">{{ $idx + 1 }}</td>
                        <td class="py-2.5 px-2 font-bold text-slate-900">{{ $item->item_name }}</td>
                        <td class="py-2.5 px-2 text-center text-slate-500">{{ $item->unit }}</td>
                        <td class="py-2.5 px-2 text-center font-mono font-bold">{{ $item->quantity }}</td>
                        <td class="py-2.5 px-2 text-right font-mono">{{ format_npr($item->unit_price) }}</td>
                        <td class="py-2.5 px-2 text-right font-mono font-bold text-slate-900">{{ format_npr($item->subtotal) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Calculations Summary -->
        <div class="pt-4 border-t border-slate-200 flex justify-end">
            <div class="w-72 space-y-1.5 text-xs text-slate-700">
                <div class="flex justify-between">
                    <span>Taxable Subtotal:</span>
                    <span class="font-mono font-semibold">{{ format_npr($invoice->subtotal) }}</span>
                </div>
                @if($invoice->discount > 0)
                <div class="flex justify-between text-rose-600">
                    <span>Discount:</span>
                    <span class="font-mono">-{{ format_npr($invoice->discount) }}</span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span>VAT ({{ $taxPercentage }}%):</span>
                    <span class="font-mono">{{ format_npr($invoice->tax) }}</span>
                </div>
                <div class="pt-2 border-t-2 border-slate-900 flex justify-between text-base font-black text-slate-900">
                    <span>Grand Total:</span>
                    <span class="font-mono text-emerald-700">{{ format_npr($invoice->grand_total) }}</span>
                </div>
            </div>
        </div>

        <!-- Footer Nepali Greeting -->
        <div class="pt-8 border-t border-slate-200 text-center text-xs text-slate-500 space-y-1">
            <p class="font-medium text-slate-700">{{ $invoice->invoice_footer }}</p>
            <p class="text-[10px] text-slate-400">All prices in Nepalese Rupees (NPR). Computer generated bill.</p>
        </div>
    </div>
</div>
@endsection
