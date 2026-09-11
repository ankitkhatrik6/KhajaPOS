@extends('layouts.app')

@section('title', 'Add New Inventory Item')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Add New Inventory Item</h1>
            <p class="text-sm text-slate-500">Create a new restaurant menu item, beverage, or raw kitchen material.</p>
        </div>
        <a href="{{ route('inventory.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Back to Inventory</span>
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form action="{{ route('inventory.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Item Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Buff Steamed Momo"
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Category *</label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">SKU / Item Code</label>
                    <input type="text" name="sku" value="{{ old('sku') }}" placeholder="Auto-generated if left blank"
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm font-mono focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Unit of Measure *</label>
                    <select name="unit" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500">
                        <option value="Piece" {{ old('unit') == 'Piece' ? 'selected' : '' }}>Piece (Plate / Serving)</option>
                        <option value="Bottle" {{ old('unit') == 'Bottle' ? 'selected' : '' }}>Bottle (Beer / Soda)</option>
                        <option value="Kg" {{ old('unit') == 'Kg' ? 'selected' : '' }}>Kg (Kilogram)</option>
                        <option value="Liter" {{ old('unit') == 'Liter' ? 'selected' : '' }}>Liter</option>
                        <option value="Packet" {{ old('unit') == 'Packet' ? 'selected' : '' }}>Packet</option>
                        <option value="Box" {{ old('unit') == 'Box' ? 'selected' : '' }}>Box</option>
                        <option value="Dozen" {{ old('unit') == 'Dozen' ? 'selected' : '' }}>Dozen</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Supplier / Vendor</label>
                    <input type="text" name="supplier" value="{{ old('supplier') }}" placeholder="e.g. Valley Poultry Suppliers"
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Purchase Cost (NPR) *</label>
                    <input type="number" step="0.01" min="0" name="purchase_price" value="{{ old('purchase_price', '0.00') }}" required placeholder="0.00"
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm font-mono font-bold focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Selling Price (NPR) *</label>
                    <input type="number" step="0.01" min="0" name="selling_price" value="{{ old('selling_price', '0.00') }}" required placeholder="0.00"
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm font-mono font-bold text-emerald-700 focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Opening Stock Quantity *</label>
                    <input type="number" step="0.01" min="0" name="current_quantity" value="{{ old('current_quantity', '0') }}" required placeholder="0"
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm font-mono focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Minimum Stock Alert Level *</label>
                    <input type="number" step="0.01" min="0" name="minimum_stock" value="{{ old('minimum_stock', '5') }}" required placeholder="5"
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm font-mono focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Item Status</label>
                    <select name="status" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active (Available in POS)</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 flex justify-end gap-3">
                <a href="{{ route('inventory.index') }}" class="px-5 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg">Cancel</a>
                <button type="submit" class="px-6 py-2.5 text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg shadow transition">
                    Save Item to Inventory
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
