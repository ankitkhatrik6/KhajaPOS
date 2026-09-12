@extends('layouts.app')

@section('title', 'Add Menu Item')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Add Menu Item</h1>
            <p class="text-sm text-slate-500">Dishes, drinks and snacks served to customers. No stock is tracked for menu items.</p>
        </div>
        <a href="{{ route('menu.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Back to Menu</span>
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form action="{{ route('menu.store') }}" method="POST" class="space-y-6">
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
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Selling Unit *</label>
                    <select name="unit" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500">
                        <option value="Piece" {{ old('unit') == 'Piece' ? 'selected' : '' }}>Piece (Plate / Serving)</option>
                        <option value="Plate" {{ old('unit') == 'Plate' ? 'selected' : '' }}>Plate</option>
                        <option value="Bottle" {{ old('unit') == 'Bottle' ? 'selected' : '' }}>Bottle</option>
                        <option value="Cup" {{ old('unit') == 'Cup' ? 'selected' : '' }}>Cup</option>
                        <option value="Glass" {{ old('unit') == 'Glass' ? 'selected' : '' }}>Glass</option>
                        <option value="Bowl" {{ old('unit') == 'Bowl' ? 'selected' : '' }}>Bowl</option>
                        <option value="Packet" {{ old('unit') == 'Packet' ? 'selected' : '' }}>Packet</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Est. Food Cost (NPR)</label>
                    <input type="number" step="0.01" min="0" name="cost" value="{{ old('cost', '0.00') }}" required placeholder="0.00"
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm font-mono font-bold focus:ring-2 focus:ring-emerald-500">
                    <span class="text-[10px] text-slate-400">Used only for profit estimation reports.</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Selling Price (NPR) *</label>
                    <input type="number" step="0.01" min="0" name="price" value="{{ old('price', '0.00') }}" required placeholder="0.00"
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm font-mono font-bold text-emerald-700 focus:ring-2 focus:ring-emerald-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                        <input type="checkbox" name="is_available" value="1" checked class="rounded text-emerald-600 focus:ring-emerald-500">
                        <span>Available in POS (shown on the billing terminal)</span>
                    </label>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 flex justify-end gap-3">
                <a href="{{ route('menu.index') }}" class="px-5 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg">Cancel</a>
                <button type="submit" class="px-6 py-2.5 text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg shadow transition">
                    Save Menu Item
                </button>
            </div>
        </form>
    </div>
</div>
@endsection