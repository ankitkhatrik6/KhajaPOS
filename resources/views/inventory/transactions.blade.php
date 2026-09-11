@extends('layouts.app')

@section('title', 'Stock Movement History')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Stock Movement & Audit Log</h1>
            <p class="text-sm text-slate-500">Chronological history of every stock deduction, purchase restock, and inventory adjustment.</p>
        </div>
        <a href="{{ route('inventory.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg bg-white border border-slate-300 text-slate-700 hover:bg-slate-50">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Back to Inventory</span>
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('inventory.transactions') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-500 mb-1">Transaction Type</label>
                <select name="type" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-1 focus:ring-emerald-500">
                    <option value="">All Types</option>
                    <option value="Sale" {{ request('type') === 'Sale' ? 'selected' : '' }}>Sale (POS Deduction)</option>
                    <option value="Purchase" {{ request('type') === 'Purchase' ? 'selected' : '' }}>Purchase (Restock)</option>
                    <option value="Adjustment" {{ request('type') === 'Adjustment' ? 'selected' : '' }}>Adjustment (Reconciliation)</option>
                    <option value="Damage" {{ request('type') === 'Damage' ? 'selected' : '' }}>Damage / Wastage</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-500 mb-1">Specific Item</label>
                <select name="item_id" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-1 focus:ring-emerald-500">
                    <option value="">All Items</option>
                    @foreach($items as $itm)
                        <option value="{{ $itm->id }}" {{ request('item_id') == $itm->id ? 'selected' : '' }}>{{ $itm->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold">
                    Filter Log
                </button>
                @if(request()->hasAny(['type', 'item_id']))
                <a href="{{ route('inventory.transactions') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-xs font-semibold">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Log Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-semibold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Date & Time (NPT)</th>
                        <th class="py-3 px-4">Type</th>
                        <th class="py-3 px-4">Item</th>
                        <th class="py-3 px-4 text-right">Qty Change</th>
                        <th class="py-3 px-4 text-right">Balance After</th>
                        <th class="py-3 px-4">Reason / Reference</th>
                        <th class="py-3 px-4">User</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $txn)
                    <tr class="hover:bg-slate-50/70">
                        <td class="py-3 px-4 whitespace-nowrap font-mono text-slate-500">
                            {{ $txn->created_at->setTimezone('Asia/Kathmandu')->format('M d, Y h:i A') }}
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            @if($txn->transaction_type === 'Sale')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-50 text-sky-700">Sale</span>
                            @elseif($txn->transaction_type === 'Purchase')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700">Restock In</span>
                            @elseif($txn->transaction_type === 'Damage')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700">Damage</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700">Adjustment</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 font-bold text-slate-900">
                            {{ $txn->inventoryItem->name ?? '[Deleted Item]' }}
                        </td>
                        <td class="py-3 px-4 text-right font-mono font-bold 
                            {{ in_array($txn->transaction_type, ['Sale', 'Damage']) ? 'text-rose-600' : 'text-emerald-600' }}">
                            {{ in_array($txn->transaction_type, ['Sale', 'Damage']) ? '-' : '+' }}{{ $txn->quantity }} {{ $txn->inventoryItem->unit ?? '' }}
                        </td>
                        <td class="py-3 px-4 text-right font-mono font-semibold text-slate-800">
                            {{ $txn->new_quantity }} {{ $txn->inventoryItem->unit ?? '' }}
                        </td>
                        <td class="py-3 px-4 max-w-xs truncate">
                            <span class="text-slate-800 font-medium">{{ $txn->reason }}</span>
                            @if($txn->reference_id)
                            <span class="text-[10px] font-mono text-slate-400 block">Ref: {{ $txn->reference_id }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap text-slate-500">
                            {{ $txn->user->name ?? 'System' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            No inventory transactions recorded.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
        <div class="p-4 border-t border-slate-200 bg-slate-50">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
