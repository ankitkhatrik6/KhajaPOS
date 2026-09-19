@extends('layouts.app')

@section('title', 'Sales Records')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Sales Orders & Receipts</h1>
            <p class="text-sm text-slate-500">Historical register of all POS sales completed in Nepalese Rupees (NPR).</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-lg text-xs font-semibold text-emerald-800">
                Today's Paid Sales: <span class="font-mono font-bold">{{ format_npr($totalSalesToday) }}</span> ({{ $ordersCountToday }} orders)
            </div>
            <a href="{{ route('pos.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white shadow-sm transition">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>New POS Sale</span>
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('sales.index') }}" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-500 mb-1">Search</label>
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Sale # or customer..."
                           class="w-full pl-9 pr-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-1 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-500 mb-1">Payment Method</label>
                <select name="payment_method" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-1 focus:ring-emerald-500">
                    <option value="">All Payment Methods</option>
                    <option value="Cash" {{ request('payment_method') === 'Cash' ? 'selected' : '' }}>Cash</option>
                    <option value="eSewa" {{ request('payment_method') === 'eSewa' ? 'selected' : '' }}>eSewa</option>
                    <option value="Khalti" {{ request('payment_method') === 'Khalti' ? 'selected' : '' }}>Khalti</option>
                    <option value="Card" {{ request('payment_method') === 'Card' ? 'selected' : '' }}>Card</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-500 mb-1">Specific Date</label>
                <input type="date" name="date" value="{{ request('date') }}"
                       class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-1 focus:ring-emerald-500">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold">
                    Filter Sales
                </button>
                @if(request()->hasAny(['search', 'payment_method', 'date']))
                <a href="{{ route('sales.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-xs font-semibold">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Sales Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-semibold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Sale Number</th>
                        <th class="py-3 px-4">Date & Time (NPT)</th>
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Cashier</th>
                        <th class="py-3 px-4">Payment Method</th>
                        <th class="py-3 px-4 text-right">Grand Total</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sales as $sale)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="py-3 px-4 font-mono font-bold text-slate-900">
                            {{ $sale->sale_number }}
                        </td>
                        <td class="py-3 px-4 font-mono text-slate-500 whitespace-nowrap">
                            {{ $sale->created_at->setTimezone('Asia/Kathmandu')->format('M d, Y h:i A') }}
                        </td>
                        <td class="py-3 px-4 font-medium text-slate-800">
                            {{ $sale->customer_name }}
                        </td>
                        <td class="py-3 px-4 text-slate-500">
                            {{ $sale->user->name ?? '-' }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold 
                                {{ $sale->payment_method === 'Cash' ? 'bg-emerald-50 text-emerald-800' : '' }}
                                {{ $sale->payment_method === 'eSewa' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $sale->payment_method === 'Khalti' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $sale->payment_method === 'Card' ? 'bg-blue-50 text-blue-800' : '' }}">
                                {{ $sale->payment_method }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right font-mono font-bold text-sm text-slate-900">
                            {{ format_npr($sale->total) }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                {{ $sale->payment_status }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('sales.show', $sale->id) }}" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px]">
                                    Details
                                </a>
                                @if($sale->invoice)
                                <a href="{{ route('invoices.show', $sale->invoice->id) }}" class="px-2 py-1 rounded bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-semibold text-[11px] border border-emerald-200">
                                    Invoice
                                </a>
                                <a href="{{ route('invoices.receipt', $sale->invoice->id) }}?autoprint=1" target="_blank" class="p-1 text-slate-400 hover:text-slate-800" title="80mm Thermal Receipt">
                                    <i data-lucide="printer" class="w-4 h-4"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400">
                            No sales records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sales->hasPages())
        <div class="p-4 border-t border-slate-200 bg-slate-50">
            {{ $sales->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
