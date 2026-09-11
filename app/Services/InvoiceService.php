<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\RestaurantSetting;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Str;

class InvoiceService
{
    /**
     * Create and persist an invoice for a completed sale.
     */
    public function createInvoiceForSale(Sale $sale, ?User $cashier = null): Invoice
    {
        // Generate the next invoice number for today (e.g. INV-20260911-0006)
        $invoiceNumber = $this->generateInvoiceNumber();

        $cashierName = $cashier ? $cashier->name : ($sale->user ? $sale->user->name : 'Staff');

        $restaurantName = RestaurantSetting::get('restaurant_name', 'Himalayan Flavors Cafe & Restaurant');
        $restaurantAddress = RestaurantSetting::get('address', 'Thamel Marg, Kathmandu, Nepal');
        $restaurantPhone = RestaurantSetting::get('phone', '+977-1-4412345');
        $restaurantPan = RestaurantSetting::get('pan_number', '601234567');
        $restaurantVat = RestaurantSetting::get('vat_number', '13% VAT Registered');
        $invoiceFooter = RestaurantSetting::get('invoice_footer', 'Thank you for dining with us! Visit again. धन्‍यवाद!');

        return Invoice::create([
            'invoice_number' => $invoiceNumber,
            'sale_id' => $sale->id,
            'subtotal' => $sale->subtotal,
            'discount' => $sale->discount,
            'tax' => $sale->tax,
            'grand_total' => $sale->total,
            'payment_method' => $sale->payment_method,
            'payment_status' => $sale->payment_status,
            'cashier_name' => $cashierName,
            'customer_name' => $sale->customer_name,
            'restaurant_name' => $restaurantName,
            'restaurant_address' => $restaurantAddress,
            'restaurant_phone' => $restaurantPhone,
            'restaurant_pan' => $restaurantPan,
            'restaurant_vat' => $restaurantVat,
            'invoice_footer' => $invoiceFooter,
        ]);
    }

    /**
     * Generate the next invoice number for today (e.g. INV-20260911-0006).
     *
     * Derived from the highest existing number so it stays unique even when the
     * data was seeded with non-contiguous numbers or older rows are deleted.
     */
    protected function generateInvoiceNumber(): string
    {
        $prefix = 'INV-'.date('Ymd').'-';

        $maxSeq = 0;
        foreach (Invoice::select(['invoice_number'])->where('invoice_number', 'like', $prefix.'%')->get() as $row) {
            $seq = (int)Str::after($row->invoice_number, $prefix);
            if ($seq > $maxSeq) {
                $maxSeq = $seq;
            }
        }

        return sprintf('%s%04d', $prefix, $maxSeq + 1);
    }
}
