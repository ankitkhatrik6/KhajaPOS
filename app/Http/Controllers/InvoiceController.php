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
            // Grouped: without the closure the OR would escape any other
            // constraint (role scoping, date filters) added to this query.
            $query->where(function ($q) use ($request) {
                $q->where('invoice_number', 'like', "%{$request->search}%")
                    ->orWhere('customer_name', 'like', "%{$request->search}%");
            });
        }

        $invoices = $query->paginate(15)->withQueryString();
        return view('invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['sale.items.menuItem', 'sale.user', 'payments']);
        $taxPercentage = $this->taxPercentage();
        return view('invoices.show', compact('invoice', 'taxPercentage'));
    }

    public function print(Invoice $invoice)
    {
        $invoice->load(['sale.items.menuItem', 'sale.user', 'payments']);
        $taxPercentage = $this->taxPercentage();
        return view('invoices.print', compact('invoice', 'taxPercentage'));
    }

    public function receipt(Invoice $invoice)
    {
        $invoice->load(['sale.items.menuItem', 'sale.user', 'payments']);
        $taxPercentage = $this->taxPercentage();
        return view('invoices.receipt', compact('invoice', 'taxPercentage'));
    }

    /**
     * VAT rate shown on the invoice screens and the printed tax invoice /
     * receipt. This is the same `tax_percentage` setting PosService bills
     * with, so the printed rate can never drift from the charged one.
     */
    private function taxPercentage(): float
    {
        return (float) RestaurantSetting::get('tax_percentage', 13.00);
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
