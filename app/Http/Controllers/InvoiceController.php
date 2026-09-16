<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\RestaurantSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $invoice->load(['sale.items.menuItem', 'sale.user', 'payments']);
        return view('invoices.show', compact('invoice'));
    }

    public function print(Invoice $invoice)
    {
        $invoice->load(['sale.items.menuItem', 'sale.user', 'payments']);
        return view('invoices.print', compact('invoice'));
    }

    public function receipt(Invoice $invoice)
    {
        $invoice->load(['sale.items.menuItem', 'sale.user', 'payments']);
        return view('invoices.receipt', compact('invoice'));
    }

    /**
     * Permanently delete an invoice together with its linked sale record
     * (sale_items and payments cascade automatically). Admin only — the route
     * lives in the admin role group.
     */
    public function destroy(Invoice $invoice)
    {
        $number = $invoice->invoice_number;

        DB::transaction(function () use ($invoice) {
            $invoice->load('sale');

            if ($invoice->sale) {
                $invoice->sale->delete(); // cascades invoice, sale_items and payments
            } else {
                $invoice->delete();
            }
        });

        return redirect()->route('invoices.index')
            ->with('success', "Invoice {$number} and its sale record were permanently deleted.");
    }
}
