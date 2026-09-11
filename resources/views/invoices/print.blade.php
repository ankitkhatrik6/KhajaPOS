<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Invoice {{ $invoice->invoice_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { padding: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 p-6 text-slate-800 font-sans">
    <div class="max-w-2xl mx-auto bg-white p-8 border border-slate-200 shadow-sm rounded-xl print:border-none print:shadow-none print:p-0">
        <!-- Print Toolbar -->
        <div class="mb-6 flex justify-between items-center no-print pb-4 border-b border-slate-200">
            <a href="{{ route('invoices.show', $invoice->id) }}" class="text-xs text-slate-600 hover:text-slate-900 font-semibold">
                &larr; Back to App
            </a>
            <button onclick="window.print()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow">
                Print Invoice Now
            </button>
        </div>

        <!-- Header -->
        <div class="text-center pb-6 border-b border-slate-300">
            <h1 class="text-xl font-extrabold uppercase tracking-tight text-slate-900">{{ $invoice->restaurant_name }}</h1>
            <p class="text-xs text-slate-600 mt-1">{{ $invoice->restaurant_address }}</p>
            <p class="text-xs text-slate-600">Tel: {{ $invoice->restaurant_phone }}</p>
            @if($invoice->restaurant_pan)
            <p class="text-xs font-mono font-bold text-slate-700 mt-1">PAN: {{ $invoice->restaurant_pan }} | {{ $invoice->restaurant_vat }}</p>
            @endif
            <div class="mt-3 inline-block px-3 py-0.5 rounded border border-slate-900 font-bold text-xs uppercase">
                Tax Invoice
            </div>
        </div>

        <!-- Meta -->
        <div class="grid grid-cols-2 text-xs py-4 border-b border-slate-200">
            <div>
                <p><span class="text-slate-500">Invoice No:</span> <strong class="font-mono">{{ $invoice->invoice_number }}</strong></p>
                <p><span class="text-slate-500">Customer:</span> <strong>{{ $invoice->customer_name }}</strong></p>
                <p><span class="text-slate-500">Payment:</span> <strong>{{ $invoice->payment_method }}</strong></p>
            </div>
            <div class="text-right">
                <p><span class="text-slate-500">Date:</span> {{ $invoice->created_at->setTimezone('Asia/Kathmandu')->format('Y-m-d h:i A') }}</p>
                <p><span class="text-slate-500">Cashier:</span> {{ $invoice->cashier_name }}</p>
                <p><span class="text-slate-500">Status:</span> <strong class="text-emerald-700">{{ $invoice->payment_status }}</strong></p>
            </div>
        </div>

        <!-- Table -->
        <table class="w-full text-left text-xs my-4">
            <thead class="border-b border-slate-300 font-bold">
                <tr>
                    <th class="py-2">Item</th>
                    <th class="py-2 text-center">Qty</th>
                    <th class="py-2 text-right">Rate (Rs.)</th>
                    <th class="py-2 text-right">Amount (Rs.)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($invoice->sale->items as $item)
                <tr>
                    <td class="py-2 font-semibold">{{ $item->item_name }}</td>
                    <td class="py-2 text-center font-mono">{{ $item->quantity }}</td>
                    <td class="py-2 text-right font-mono">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="py-2 text-right font-mono font-bold">{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="pt-3 border-t-2 border-slate-900 flex justify-end text-xs">
            <div class="w-56 space-y-1">
                <div class="flex justify-between">
                    <span>Subtotal:</span>
                    <span class="font-mono font-semibold">Rs. {{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                @if($invoice->discount > 0)
                <div class="flex justify-between">
                    <span>Discount:</span>
                    <span class="font-mono">-Rs. {{ number_format($invoice->discount, 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span>VAT (13%):</span>
                    <span class="font-mono">Rs. {{ number_format($invoice->tax, 2) }}</span>
                </div>
                <div class="pt-1 border-t border-slate-300 flex justify-between font-bold text-sm text-slate-900">
                    <span>Total (NPR):</span>
                    <span class="font-mono">Rs. {{ number_format($invoice->grand_total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 pt-4 border-t border-slate-200 text-center text-xs text-slate-600">
            <p class="font-semibold">{{ $invoice->invoice_footer }}</p>
            <p class="text-[10px] text-slate-400 mt-1">Thank you for your visit!</p>
        </div>
    </div>
</body>
</html>
