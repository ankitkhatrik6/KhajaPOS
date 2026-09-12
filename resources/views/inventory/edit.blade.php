@extends('layouts.app')

@section('title', 'Edit Inventory Item')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Edit Raw Material: {{ $item->name }}</h1>
            <p class="text-sm text-slate-500">Update pricing, supplier, minimum alert threshold, or status. Menu items are managed under Menu Management.</p>
        </div>
        <a href="{{ route('inventory.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Back</span>
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form action="{{ route('inventory.update', $item->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Material Name *</label>
                    <input type="text" name="name" value="{{ old('name', $item->name) }}" required
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Category *</label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $item->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">SKU / Item Code *</label>
                    <input type="text" name="sku" value="{{ old('sku', $item->sku) }}" required
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm font-mono focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Unit of Measure *</label>
                    <select name="unit" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500">
                        @foreach(['Piece', 'Bottle', 'Kg', 'Liter', 'Packet', 'Box', 'Dozen'] as $u)
                            <option value="{{ $u }}" {{ old('unit', $item->unit) == $u ? 'selected' : '' }}>{{ $u }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Supplier / Vendor</label>
                    <input type="text" name="supplier" value="{{ old('supplier', $item->supplier) }}"
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Purchase Cost (NPR) *</label>
                    <input type="number" step="0.01" min="0" name="purchase_price" value="{{ old('purchase_price', $item->purchase_price) }}" required
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm font-mono font-bold focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Selling / Retail Price (NPR) *</label>
                    <input type="number" step="0.01" min="0" name="selling_price" value="{{ old('selling_price', $item->selling_price) }}" required
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm font-mono font-bold text-emerald-700 focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Current Stock Level</label>
                    <input type="text" readonly value="{{ $item->current_quantity }} {{ $item->unit }}"
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-200 bg-slate-100 text-sm font-mono font-bold text-slate-700">
                    <span class="text-[10px] text-slate-400">To alter current stock quantity, use Stock In, Adjust, or Damage actions on the main table.</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Minimum Stock Alert Level *</label>
                    <input type="number" step="0.01" min="0" name="minimum_stock" value="{{ old('minimum_stock', $item->minimum_stock) }}" required
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm font-mono focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Item Status</label>
                    <select name="status" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500">
                        <option value="active" {{ old('status', $item->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $item->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 flex items-center justify-between">
                <form action="{{ route('inventory.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this item? If sales exist, it will be rejected.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-rose-600 hover:text-rose-800 font-semibold flex items-center gap-1">
                        <i data-lucide="trash" class="w-4 h-4"></i>
                        <span>Delete Material</span>
                    </button>
                </form>

                <div class="flex gap-2">
                    <a href="{{ route('inventory.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg">Cancel</a>
                    <button type="submit" class="px-5 py-2 text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg shadow transition">
                        Update Item
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
