<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Services\StockService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class InventoryController extends Controller
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)->get();

        $query = InventoryItem::with('category');

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            if ($request->status === 'low_stock') {
                $query->whereRaw('current_quantity <= minimum_stock AND current_quantity > 0');
            } elseif ($request->status === 'out_of_stock') {
                $query->whereRaw('current_quantity <= 0');
            } elseif ($request->status === 'in_stock') {
                $query->whereRaw('current_quantity > minimum_stock');
            }
        }

        $items = $query->latest()->paginate(15)->withQueryString();

        return view('inventory.index', compact('items', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('inventory.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:50', 'unique:inventory_items,sku'],
            'category_id' => ['required', 'exists:categories,id'],
            'unit' => ['required', 'in:Piece,Kg,Gram,Liter,Ml,Packet,Box,Bottle,Dozen'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'current_quantity' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'numeric', 'min:0'],
            'supplier' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        if (empty($validated['sku'])) {
            $cat = Category::find($validated['category_id']);
            $prefix = strtoupper(substr($cat->name ?? 'ITM', 0, 3));
            $validated['sku'] = sprintf('%s-%s-%04d', $prefix, date('Ymd'), rand(100, 999));
        }

        $item = InventoryItem::create($validated);

        // Record opening stock transaction if quantity > 0
        if ((float)$item->current_quantity > 0) {
            InventoryTransaction::create([
                'inventory_item_id' => $item->id,
                'transaction_type' => 'Purchase',
                'quantity' => $item->current_quantity,
                'previous_quantity' => 0,
                'new_quantity' => $item->current_quantity,
                'purchase_price' => $item->purchase_price,
                'reason' => 'Opening Stock Balance',
                'reference_id' => 'INIT-' . $item->sku,
                'user_id' => Auth::id(),
            ]);
        }

        return redirect()->route('inventory.index')->with('success', "Item '{$item->name}' added successfully.");
    }

    public function edit(InventoryItem $item)
    {
        $categories = Category::where('is_active', true)->get();
        return view('inventory.edit', compact('item', 'categories'));
    }

    public function update(Request $request, InventoryItem $item)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:50', 'unique:inventory_items,sku,' . $item->id],
            'category_id' => ['required', 'exists:categories,id'],
            'unit' => ['required', 'in:Piece,Kg,Gram,Liter,Ml,Packet,Box,Bottle,Dozen'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'numeric', 'min:0'],
            'supplier' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $item->update($validated);

        return redirect()->route('inventory.index')->with('success', "Item '{$item->name}' updated successfully.");
    }

    public function stockIn(Request $request, InventoryItem $item)
    {
        $request->validate([
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'reason' => ['nullable', 'string', 'max:255'],
            'reference_id' => ['nullable', 'string', 'max:100'],
        ]);

        try {
            $this->stockService->addStock(
                $item,
                (float)$request->quantity,
                $request->purchase_price ? (float)$request->purchase_price : null,
                $request->reason ?: 'Purchase Restock',
                $request->reference_id,
                Auth::id()
            );

            return back()->with('success', "Added {$request->quantity} {$item->unit} to '{$item->name}'. New balance: {$item->fresh()->current_quantity} {$item->unit}");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function adjust(Request $request, InventoryItem $item)
    {
        $request->validate([
            'new_quantity' => ['required', 'numeric', 'min:0'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        try {
            $this->stockService->adjustStock(
                $item,
                (float)$request->new_quantity,
                $request->reason,
                Auth::id()
            );

            return back()->with('success', "Stock adjusted for '{$item->name}' to {$request->new_quantity} {$item->unit}.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function damage(Request $request, InventoryItem $item)
    {
        $request->validate([
            'quantity' => ['required', 'numeric', 'min:0.01', 'max:' . $item->current_quantity],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        try {
            $this->stockService->recordDamage(
                $item,
                (float)$request->quantity,
                $request->reason,
                Auth::id()
            );

            return back()->with('success', "Recorded damage of {$request->quantity} {$item->unit} for '{$item->name}'.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function transactions(Request $request)
    {
        $query = InventoryTransaction::with(['inventoryItem', 'user'])->latest();

        if ($request->filled('type')) {
            $query->where('transaction_type', $request->type);
        }

        if ($request->filled('item_id')) {
            $query->where('inventory_item_id', $request->item_id);
        }

        $transactions = $query->paginate(20)->withQueryString();
        $items = InventoryItem::orderBy('name')->get();

        return view('inventory.transactions', compact('transactions', 'items'));
    }

    public function destroy(InventoryItem $item)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        // Admin permanent delete: stock history rows cascade with the item.
        $item->delete();
        return redirect()->route('inventory.index')->with('success', "Item deleted successfully.");
    }
}
