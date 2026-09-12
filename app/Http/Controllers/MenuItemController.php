<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuItemController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)->get();

        $query = MenuItem::with('category');

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('availability')) {
            $query->where('is_available', $request->availability === 'available');
        }

        $items = $query->latest()->paginate(15)->withQueryString();

        return view('menu.index', compact('items', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('menu.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:50', 'unique:menu_items,sku'],
            'category_id' => ['required', 'exists:categories,id'],
            'unit' => ['required', 'in:Piece,Kg,Gram,Liter,Ml,Packet,Box,Bottle,Dozen,Plate,Cup,Glass,Bowl'],
            'cost' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        if (empty($validated['sku'])) {
            $cat = Category::find($validated['category_id']);
            $prefix = strtoupper(substr($cat->name ?? 'MN', 0, 3));
            $validated['sku'] = sprintf('%s-%s-%04d', $prefix, date('Ymd'), rand(100, 999));
        }

        $validated['is_available'] = $request->has('is_available');
        MenuItem::create($validated);

        return redirect()->route('menu.index')->with('success', "Menu item '{$validated['name']}' added successfully.");
    }

    public function edit(MenuItem $item)
    {
        $categories = Category::where('is_active', true)->get();
        return view('menu.edit', compact('item', 'categories'));
    }

    public function update(Request $request, MenuItem $item)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:50', 'unique:menu_items,sku,' . $item->id],
            'category_id' => ['required', 'exists:categories,id'],
            'unit' => ['required', 'in:Piece,Kg,Gram,Liter,Ml,Packet,Box,Bottle,Dozen,Plate,Cup,Glass,Bowl'],
            'cost' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_available' => ['nullable', 'boolean'],
        ]);

        $validated['is_available'] = $request->has('is_available');
        $item->update($validated);

        return redirect()->route('menu.index')->with('success', "Menu item '{$item->name}' updated successfully.");
    }

    public function toggle(MenuItem $item)
    {
        $item->is_available = !$item->is_available;
        $item->save();

        return back()->with('success', "Menu item '{$item->name}' is now " . ($item->is_available ? 'available' : 'hidden') . " in the POS.");
    }

    public function destroy(MenuItem $item)
    {
        if ($item->saleItems()->count() > 0) {
            return back()->with('error', "Cannot delete '{$item->name}' because historical sales exist. You can hide it from the menu instead.");
        }

        $item->delete();
        return redirect()->route('menu.index')->with('success', "Menu item deleted successfully.");
    }
}