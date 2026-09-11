<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\RestaurantSetting;
use App\Services\PosService;
use App\Services\SaleService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    protected PosService $posService;
    protected SaleService $saleService;

    public function __construct(PosService $posService, SaleService $saleService)
    {
        $this->posService = $posService;
        $this->saleService = $saleService;
    }

    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)
            ->whereNotIn('slug', ['raw-materials', 'packaging'])
            ->get();

        $query = InventoryItem::where('status', 'active')
            ->whereHas('category', function ($q) {
                $q->whereNotIn('slug', ['raw-materials', 'packaging']);
            })
            ->with('category');

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $items = $query->orderBy('name')->get();
        $taxPercentage = (float)RestaurantSetting::get('tax_percentage', 13.00);

        return view('pos.index', compact('categories', 'items', 'taxPercentage'));
    }

    public function quickStockCheck($id)
    {
        $item = InventoryItem::find($id);
        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        return response()->json([
            'id' => $item->id,
            'name' => $item->name,
            'sku' => $item->sku,
            'price' => (float)$item->selling_price,
            'unit' => $item->unit,
            'current_quantity' => (float)$item->current_quantity,
            'minimum_stock' => (float)$item->minimum_stock,
            'status' => $item->stock_status,
        ]);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'integer', 'exists:inventory_items,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'customer_name' => ['nullable', 'string', 'max:150'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:Cash,Online'],
            'transaction_reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $cashier = Auth::user();
            $sale = $this->saleService->processSale($request->all(), $cashier);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sale completed successfully!',
                    'sale_id' => $sale->id,
                    'invoice_id' => $sale->invoice->id,
                    'invoice_number' => $sale->invoice->invoice_number,
                    'print_url' => route('invoices.print', $sale->invoice->id),
                    'redirect_url' => route('invoices.show', $sale->invoice->id),
                ]);
            }

            return redirect()->route('invoices.show', $sale->invoice->id)
                ->with('success', 'Sale completed successfully!');
        } catch (Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                ], 422);
            }

            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
