<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\RestaurantSetting;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['sale.user'])->latest();

        if ($request->filled('search')) {
            $query->where('invoice_number', 'like', "%{$request->search}%")
                ->orWhere('customer_name', 'like', "%{$request->search}%");
        }

        $invoices = $query->paginate(15)->withQueryString();
        return view('invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['sale.items.inventoryItem', 'sale.user', 'payments']);
        return view('invoices.show', compact('invoice'));
    }

    public function print(Invoice $invoice)
    {
        $invoice->load(['sale.items.inventoryItem', 'sale.user', 'payments']);
        return view('invoices.print', compact('invoice'));
    }

    public function receipt(Invoice $invoice)
    {
        $invoice->load(['sale.items.inventoryItem', 'sale.user', 'payments']);
        return view('invoices.receipt', compact('invoice'));
    }
}
