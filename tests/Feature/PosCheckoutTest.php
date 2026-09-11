<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Invoice;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_pos_checkout_deducts_inventory_and_generates_invoice(): void
    {
        $cashier = User::where('email', 'cashier@restaurant.com')->first();
        $item = InventoryItem::where('sku', 'MMO-BUF-STM')->first();

        $initialQty = (float)$item->current_quantity;
        $orderQty = 2.0;

        $response = $this->actingAs($cashier)->postJson('/pos/checkout', [
            'customer_name' => 'Hari Bahadur',
            'payment_method' => 'Cash',
            'discount' => 10.0,
            'items' => [
                [
                    'item_id' => $item->id,
                    'quantity' => $orderQty,
                ]
            ],
            'notes' => 'Extra spicy achar please',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        // Verify inventory deducted
        $item->refresh();
        $this->assertEquals($initialQty - $orderQty, (float)$item->current_quantity);

        // Verify sale created
        $sale = Sale::latest('id')->first();
        $this->assertEquals('Hari Bahadur', $sale->customer_name);
        $this->assertEquals('Cash', $sale->payment_method);
        $this->assertEquals('Paid', $sale->payment_status);

        // Verify invoice created
        $invoice = Invoice::where('sale_id', $sale->id)->first();
        $this->assertNotNull($invoice);
        $this->assertStringStartsWith('INV-', $invoice->invoice_number);

        // Verify inventory transaction logged
        $txn = InventoryTransaction::where('inventory_item_id', $item->id)
            ->where('transaction_type', 'Sale')
            ->latest('id')
            ->first();
        $this->assertNotNull($txn);
        $this->assertEquals($orderQty, (float)$txn->quantity);
        $this->assertEquals($initialQty, (float)$txn->previous_quantity);
        $this->assertEquals($initialQty - $orderQty, (float)$txn->new_quantity);
    }

    public function test_pos_checkout_rejects_insufficient_stock(): void
    {
        $cashier = User::where('email', 'cashier@restaurant.com')->first();
        $item = InventoryItem::where('sku', 'DRK-COKE-500')->first(); // 4 in stock

        $response = $this->actingAs($cashier)->postJson('/pos/checkout', [
            'customer_name' => 'Test Customer',
            'payment_method' => 'Cash',
            'items' => [
                [
                    'item_id' => $item->id,
                    'quantity' => 50.0, // Exceeds stock
                ]
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('success', false);
    }

    public function test_inventory_stock_in_increases_quantity(): void
    {
        $stockManager = User::where('email', 'stock@restaurant.com')->first();
        $item = InventoryItem::where('sku', 'MMO-BUF-STM')->first();
        $initialQty = (float)$item->current_quantity;

        $response = $this->actingAs($stockManager)->post("/inventory/{$item->id}/stock-in", [
            'quantity' => 20,
            'purchase_price' => 115.0,
            'supplier' => 'Kathmandu Fresh Meats',
            'reference_id' => 'PO-TEST-001',
            'reason' => 'Weekend preparation stock',
        ]);

        $response->assertRedirect();
        $item->refresh();
        $this->assertEquals($initialQty + 20, (float)$item->current_quantity);

        $txn = InventoryTransaction::where('inventory_item_id', $item->id)
            ->where('transaction_type', 'Purchase')
            ->latest('id')
            ->first();
        $this->assertNotNull($txn);
        $this->assertEquals(20, (float)$txn->quantity);
    }
}
