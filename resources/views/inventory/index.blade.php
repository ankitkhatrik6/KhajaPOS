@extends('layouts.app')

@section('title', 'Stock Inventory Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Raw Materials & Supplies</h1>
            <p class="text-sm text-slate-500">Track raw materials and items used in the restaurant — kitchen ingredients, drinks stock, packaging and supplies. Menu items are managed separately and are not stock tracked.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('inventory.transactions') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 transition">
                <i data-lucide="history" class="w-4 h-4 text-slate-500"></i>
                <span>Stock History Log</span>
            </a>
            <a href="{{ route('inventory.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white shadow-sm transition">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Add Raw Material</span>
            </a>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('inventory.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-500 mb-1">Search</label>
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Item name or SKU..."
                           class="w-full pl-9 pr-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-1 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-500 mb-1">Category</label>
                <select name="category" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-1 focus:ring-emerald-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-500 mb-1">Stock Level</label>
                <select name="status" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-1 focus:ring-emerald-500">
                    <option value="">All Levels</option>
                    <option value="in_stock" {{ request('status') === 'in_stock' ? 'selected' : '' }}>In Stock (Good)</option>
                    <option value="low_stock" {{ request('status') === 'low_stock' ? 'selected' : '' }}>Low Stock Alert</option>
                    <option value="out_of_stock" {{ request('status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock (0)</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold transition">
                    Filter Stock
                </button>
                @if(request()->hasAny(['search', 'category', 'status']))
                <a href="{{ route('inventory.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-xs font-semibold">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Inventory Items Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-semibold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Item Details</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4 text-right">Cost (NPR)</th>
                        <th class="py-3 px-4 text-right">Selling / Retail (NPR)</th>
                        <th class="py-3 px-4 text-right">Available Stock</th>
                        <th class="py-3 px-4 text-right">Min Level</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Stock Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($items as $item)
                    @php
                        $isOut = $item->isOutOfStock();
                        $isLow = $item->isLowStock();
                    @endphp
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="py-3 px-4">
                            <div class="font-bold text-slate-900 text-sm">{{ $item->name }}</div>
                            <div class="text-[11px] text-slate-400 font-mono">SKU: {{ $item->sku }}</div>
                            @if($item->supplier)
                            <div class="text-[10px] text-slate-500">Supplier: {{ $item->supplier }}</div>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-medium">
                                {{ $item->category->name ?? '-' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right font-mono font-medium text-slate-700">
                            {{ format_npr($item->purchase_price) }}
                        </td>
                        <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">
                            {{ format_npr($item->selling_price) }}
                        </td>
                        <td class="py-3 px-4 text-right font-mono font-extrabold text-sm {{ $isOut ? 'text-rose-600' : ($isLow ? 'text-amber-600' : 'text-emerald-700') }}">
                            {{ $item->current_quantity }} <span class="text-xs font-normal text-slate-500">{{ $item->unit }}</span>
                        </td>
                        <td class="py-3 px-4 text-right font-mono text-slate-500">
                            {{ $item->minimum_stock }} {{ $item->unit }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if($isOut)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                    OUT OF STOCK
                                </span>
                            @elseif($isLow)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                    LOW STOCK
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700">
                                    In Stock
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <!-- Stock In Button -->
                                <button type="button" class="btn-stock-in px-2.5 py-1 rounded bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-semibold text-[11px] border border-emerald-200 transition"
                                        data-id="{{ $item->id }}" data-name="{{ $item->name }}" data-unit="{{ $item->unit }}" data-cost="{{ $item->purchase_price }}">
                                    + Stock In
                                </button>

                                <!-- Adjust Button -->
                                <button type="button" class="btn-adjust px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px] border border-slate-200 transition"
                                        data-id="{{ $item->id }}" data-name="{{ $item->name }}" data-unit="{{ $item->unit }}" data-qty="{{ $item->current_quantity }}">
                                    Adjust
                                </button>

                                <!-- Damage Button -->
                                <button type="button" class="btn-damage px-2.5 py-1 rounded bg-rose-50 hover:bg-rose-100 text-rose-800 font-semibold text-[11px] border border-rose-200 transition"
                                        data-id="{{ $item->id }}" data-name="{{ $item->name }}" data-unit="{{ $item->unit }}" data-qty="{{ $item->current_quantity }}">
                                    Damage
                                </button>

                                <!-- Edit Link -->
                                <a href="{{ route('inventory.edit', $item->id) }}" class="p-1 text-slate-400 hover:text-slate-700">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400">
                            No inventory items found matching the selected criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
        <div class="p-4 border-t border-slate-200 bg-slate-50">
            {{ $items->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal: Stock In (Restock) -->
<div id="modal-stock-in" class="fixed inset-0 z-50 bg-black/60 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-slate-200 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-5 h-5 text-emerald-600"></i>
                <span>Add Stock (Restock)</span>
            </h3>
            <button type="button" class="modal-close text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="form-stock-in" method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Item Name</label>
                <input type="text" id="si-item-name" readonly class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-lg text-xs font-bold text-slate-800">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">
                        Quantity to Add (<span id="si-unit-label">Piece</span>) *
                    </label>
                    <input type="number" step="0.01" min="0.01" name="quantity" required placeholder="e.g. 20"
                           class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs font-bold font-mono focus:ring-1 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Purchase Cost / Unit (NPR)</label>
                    <input type="number" step="0.01" min="0" id="si-cost" name="purchase_price" placeholder="Cost"
                           class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs font-mono focus:ring-1 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Reason / Vendor Notes</label>
                <input type="text" name="reason" placeholder="e.g. Weekly Restock from Kathmandu Fresh Meats"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs focus:ring-1 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Invoice / PO Reference (Optional)</label>
                <input type="text" name="reference_id" placeholder="e.g. PO-8921"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs font-mono focus:ring-1 focus:ring-emerald-500">
            </div>

            <div class="pt-2 flex justify-end gap-2">
                <button type="button" class="modal-close px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg shadow">Confirm Stock In</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Stock Adjustment -->
<div id="modal-adjust" class="fixed inset-0 z-50 bg-black/60 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-slate-200 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="sliders" class="w-5 h-5 text-indigo-600"></i>
                <span>Stock Audit Reconciliation</span>
            </h3>
            <button type="button" class="modal-close text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="form-adjust" method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Item Name</label>
                <input type="text" id="adj-item-name" readonly class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-lg text-xs font-bold text-slate-800">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Current Book Balance</label>
                    <input type="text" id="adj-current-qty" readonly class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-lg text-xs font-mono font-bold text-slate-600">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">
                        New Physical Count *
                    </label>
                    <input type="number" step="0.01" min="0" name="new_quantity" required placeholder="Actual count"
                           class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs font-bold font-mono focus:ring-1 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Reason for Adjustment *</label>
                <input type="text" name="reason" required placeholder="e.g. Weekly physical inventory count discrepancy"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs focus:ring-1 focus:ring-indigo-500">
            </div>

            <div class="pt-2 flex justify-end gap-2">
                <button type="button" class="modal-close px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg shadow">Apply Adjustment</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Damage / Wastage -->
<div id="modal-damage" class="fixed inset-0 z-50 bg-black/60 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-slate-200 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="trash-2" class="w-5 h-5 text-rose-600"></i>
                <span>Record Wastage / Damage</span>
            </h3>
            <button type="button" class="modal-close text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="form-damage" method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Item Name</label>
                <input type="text" id="dmg-item-name" readonly class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-lg text-xs font-bold text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">
                    Damaged Quantity (<span id="dmg-unit-label">Piece</span>) *
                </label>
                <input type="number" step="0.01" min="0.01" name="quantity" required placeholder="Quantity damaged"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs font-bold font-mono text-rose-600 focus:ring-1 focus:ring-rose-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Reason / Cause *</label>
                <input type="text" name="reason" required placeholder="e.g. Expired, dropped bottle, spoiled in fridge"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-xs focus:ring-1 focus:ring-rose-500">
            </div>

            <div class="pt-2 flex justify-end gap-2">
                <button type="button" class="modal-close px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 text-xs font-bold bg-rose-600 hover:bg-rose-500 text-white rounded-lg shadow">Deduct & Record Wastage</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Stock In Modal Handler
    document.querySelectorAll('.btn-stock-in').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            document.getElementById('si-item-name').value = this.dataset.name;
            document.getElementById('si-unit-label').textContent = this.dataset.unit;
            document.getElementById('si-cost').value = this.dataset.cost;
            document.getElementById('form-stock-in').action = `/inventory/${id}/stock-in`;
            document.getElementById('modal-stock-in').classList.remove('hidden');
        });
    });

    // Adjust Modal Handler
    document.querySelectorAll('.btn-adjust').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            document.getElementById('adj-item-name').value = this.dataset.name;
            document.getElementById('adj-current-qty').value = `${this.dataset.qty} ${this.dataset.unit}`;
            document.getElementById('form-adjust').action = `/inventory/${id}/adjust`;
            document.getElementById('modal-adjust').classList.remove('hidden');
        });
    });

    // Damage Modal Handler
    document.querySelectorAll('.btn-damage').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            document.getElementById('dmg-item-name').value = this.dataset.name;
            document.getElementById('dmg-unit-label').textContent = this.dataset.unit;
            document.getElementById('form-damage').action = `/inventory/${id}/damage`;
            document.getElementById('modal-damage').classList.remove('hidden');
        });
    });

    // Modal close buttons
    document.querySelectorAll('.modal-close').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('modal-stock-in').classList.add('hidden');
            document.getElementById('modal-adjust').classList.add('hidden');
            document.getElementById('modal-damage').classList.add('hidden');
        });
    });
});
</script>
@endpush
