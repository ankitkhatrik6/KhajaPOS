<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleService
{
    protected PosService $posService;
    protected InvoiceService $invoiceService;

    public function __construct(
        PosService $posService,
        InvoiceService $invoiceService
    ) {
        $this->posService = $posService;
        $this->invoiceService = $invoiceService;
    }

    /**
     * Process a full sale transaction.
     */
    public function processSale(array $data, User $cashier): Sale
    {
        $cartItems = $data['items'] ?? [];
        if (empty($cartItems)) {
            throw new Exception('Cart is empty. Please select at least one item.');
        }

        $discount = (float)($data['discount'] ?? 0.00);
        $customerName = trim($data['customer_name'] ?? '') ?: 'Walk-in Customer';
        $paymentMethod = $data['payment_method'] ?? 'Cash';
        $notes = $data['notes'] ?? null;
        $transactionRef = $data['transaction_reference'] ?? null;

        // Calculate cart breakdown
        $calc = $this->posService->calculateCart($cartItems, $discount);
        if (empty($calc['items'])) {
            throw new Exception('No valid items found in sale order.');
        }

        // Persist the sale with retry: two staff members can submit checkouts at
        // the same moment, so a generated number may collide — retry with a fresh
        // number instead of failing the whole sale.
        $lastError = null;
        foreach ([1, 2, 3, 4, 5] as $attempt) {
            try {
                return $this->completeSale($calc, $customerName, $paymentMethod, $notes, $transactionRef, $cashier);
            } catch (Exception $e) {
                if (!str_contains($e->getMessage() ?? '', 'Duplicate entry')) {
                    throw $e;
                }
                $lastError = $e;
            }
        }

        throw $lastError;
    }

    /**
     * Persist one sale inside a single database transaction.
     */
    protected function completeSale(array $calc, string $customerName, string $paymentMethod, ?string $notes, ?string $transactionRef, User $cashier): Sale
    {
        return DB::transaction(function () use ($calc, $customerName, $paymentMethod, $notes, $transactionRef, $cashier) {
            // 1. Generate a unique sale number e.g. SALE-20260911-0006 (based on
            //    the highest number already used for today, not a simple row count —
            //    counts break when numbers are non-contiguous or rows get deleted)
            $saleNumber = $this->generateSaleNumber();

            // 2. Create Sale record
            $sale = Sale::create([
                'sale_number' => $saleNumber,
                'user_id' => $cashier->id,
                'customer_name' => $customerName,
                'subtotal' => $calc['subtotal'],
                'discount' => $calc['discount'],
                'tax' => $calc['tax'],
                'total' => $calc['grand_total'],
                'cost' => $calc['total_cost'],
                'profit' => $calc['profit'],
                'payment_method' => $paymentMethod,
                'payment_status' => 'Paid',
                'notes' => $notes,
            ]);

            // 3. Create Sale Items (menu items are not stock-tracked, so no
            //    inventory deduction happens when a dish is sold)
            foreach ($calc['items'] as $itemRow) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'menu_item_id' => $itemRow['item']->id,
                    'item_name' => $itemRow['item_name'],
                    'unit' => $itemRow['unit'],
                    'quantity' => $itemRow['quantity'],
                    'unit_price' => $itemRow['unit_price'],
                    'unit_cost' => $itemRow['unit_cost'],
                    'subtotal' => $itemRow['subtotal'],
                    'total_cost' => $itemRow['total_cost'],
                    'profit' => $itemRow['profit'],
                ]);
            }

            // 4. Generate Invoice
            $invoice = $this->invoiceService->createInvoiceForSale($sale, $cashier);

            // 5. Record Payment
            Payment::create([
                'sale_id' => $sale->id,
                'invoice_id' => $invoice->id,
                'payment_method' => $paymentMethod,
                'amount' => $sale->total,
                'status' => 'Success',
                'transaction_reference' => $transactionRef,
                'user_id' => $cashier->id,
            ]);

            return $sale->load(['items.menuItem', 'invoice', 'payments', 'user']);
        });
    }

    /**
     * Generate the next sale number for today (e.g. SALE-20260911-0006).
     *
     * The sequence is derived from the highest existing number instead of a row
     * count, so it stays unique even when older rows are deleted or the data was
     * seeded with non-contiguous numbers.
     */
    protected function generateSaleNumber(): string
    {
        $prefix = 'SALE-'.date('Ymd').'-';

        $maxSeq = 0;
        foreach (Sale::select(['sale_number'])->where('sale_number', 'like', $prefix.'%')->get() as $row) {
            $seq = (int)Str::after($row->sale_number, $prefix);
            if ($seq > $maxSeq) {
                $maxSeq = $seq;
            }
        }

        return sprintf('%s%04d', $prefix, $maxSeq + 1);
    }
}
