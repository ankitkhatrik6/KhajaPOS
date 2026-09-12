@extends('layouts.app')

@section('title', 'Menu Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Menu Management</h1>
            <p class="text-sm text-slate-500">Manage the dishes and beverages sold at the counter. Menu items are not stock tracked.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('menu.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white shadow-sm transition">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Add Menu Item</span>
            </a>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('menu.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
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
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-500 mb-1">Availability</label>
                <select name="availability" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-1 focus:ring-emerald-500">
                    <option value="">All</option>
                    <option value="available" {{ request('availability') === 'available' ? 'selected' : '' }}>Available in POS</option>
                    <option value="hidden" {{ request('availability') === 'hidden' ? 'selected' : '' }}>Hidden from POS</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold">
                    Filter Menu
                </button>
                @if(request()->hasAny(['search', 'category', 'availability']))
                <a href="{{ route('menu.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-xs font-semibold">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Menu Items Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-semibold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Menu Item</th>
                        <th class="py-3 px-4">SKU</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4 text-center">Unit</th>
                        <th class="py-3 px-4 text-right">Est. Cost (NPR)</th>
                        <th class="py-3 px-4 text-right">Selling Price (NPR)</th>
                        <th class="py-3 px-4 text-center">Availability</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($items as $item)
                    <tr class="hover:bg-slate-50/70">
                        <td class="py-3 px-4 font-bold text-slate-900">{{ $item->name }}</td>
                        <td class="py-3 px-4 font-mono text-slate-500">{{ $item->sku }}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">{{ $item->category->name ?? '-' }}</span>
                        </td>
                        <td class="py-3 px-4 text-center text-slate-500">{{ $item->unit }}</td>
                        <td class="py-3 px-4 text-right font-mono">{{ format_npr($item->cost) }}</td>
                        <td class="py-3 px-4 text-right font-mono font-bold text-emerald-700">{{ format_npr($item->price) }}</td>
                        <td class="py-3 px-4 text-center">
                            @if($item->is_available)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Available</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500">Hidden</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('menu.edit', $item->id) }}" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px]">
                                    Edit
                                </a>
                                <form action="{{ route('menu.toggle', $item->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2 py-1 rounded font-semibold text-[11px] {{ $item->is_available ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                        {{ $item->is_available ? 'Hide' : 'Show' }}
                                    </button>
                                </form>
                                <form action="{{ route('menu.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete {{ $item->name }}? Historical sales will be kept.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 rounded text-rose-500 hover:bg-rose-50" title="Delete item">
                                        <i data-lucide="trash" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400">
                            No menu items found. Add your first dish or beverage.
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
@endsection