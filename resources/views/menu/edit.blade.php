@extends('layouts.app')

@section('title', 'Edit Menu Item - ' . $item->name)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Edit Menu Item: {{ $item->name }}</h1>
            <p class="text-sm text-slate-500">Update pricing, category, or POS availability. Menu items have no stock.</p>
        </div>
        <a href="{{ route('menu.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Back</span>
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form action="{{ route('menu.update', $item->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Item Name *</label>
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
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Selling Unit *</label>
                    <select name="unit" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500">
                        @foreach(['Piece', 'Plate', 'Bottle', 'Cup', 'Glass', 'Bowl', 'Packet'] as $u)
                            <option value="{{ $u }}" {{ old('unit', $item->unit) == $u ? 'selected' : '' }}>{{ $u }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Est. Food Cost (NPR)</label>
                    <input type="number" step="0.01" min="0" name="cost" value="{{ old('cost', $item->cost) }}" required
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm font-mono font-bold focus:ring-2 focus:ring-emerald-500">
                    <span class="text-[10px] text-slate-400">Used only for profit estimation reports.</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Selling Price (NPR) *</label>
                    <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $item->price) }}" required
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm font-mono font-bold text-emerald-700 focus:ring-2 focus:ring-emerald-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                        <input type="checkbox" name="is_available" value="1" {{ $item->is_available ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500">
                        <span>Available in POS (shown on the billing terminal)</span>
                    </label>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 flex items-center justify-between">
                <form action="{{ route('menu.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Delete this menu item? This is not possible if it has sales history.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-rose-600 hover:text-rose-800 font-semibold flex items-center gap-1">
                        <i data-lucide="trash" class="w-4 h-4"></i>
                        <span>Delete Item</span>
                    </button>
                </form>

                <div class="flex gap-2">
                    <a href="{{ route('menu.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg">Cancel</a>
                    <button type="submit" class="px-5 py-2 text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg shadow transition">
                        Update Menu Item
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection