<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Invoice;
use App\Models\MenuItem;
use App\Models\Sale;
use App\Models\SaleItem;
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

    protected function getAdminUser(): User
    {
        return User::where('email', 'admin@khajapos.com')->first();
    }

    public function test_pos_checkout_generates_sale_invoice_and_does_not_deduct_inventory(): void
    {
        $admin = $this->getAdminUser();
        $menuItem = MenuItem::where('sku', 'MMO-BUF-STM')->first();
        $rawItem = InventoryItem::where('sku', 'RAW-CHK-1K')->first();

        $rawQtyBefore = (float)$rawItem->current_quantity;
        $orderQty = 2.0;

        $response = $this->actingAs($admin)->postJson('/pos/checkout', [
            'customer_name' => 'Hari Bahadur',
            'payment_method' => 'Cash',
            'discount' => 10.0,
            'items' => [
                [
                    'item_id' => $menuItem->id,
                    'quantity' => $orderQty,
                ]
            ],
            'notes' => 'Extra spicy achar please',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        // Verify sale created
        $sale = Sale::latest('id')->first();
        $this->assertEquals('Hari Bahadur', $sale->customer_name);
        $this->assertEquals('Cash', $sale->payment_method);
        $this->assertEquals('Paid', $sale->payment_status);

        // Verify sale item links to the menu item (not an inventory item)
        $saleItem = SaleItem::where('sale_id', $sale->id)->first();
        $this->assertEquals($menuItem->id, $saleItem->menu_item_id);

        // Verify invoice created
        $invoice = Invoice::where('sale_id', $sale->id)->first();
        $this->assertNotNull($invoice);
        $this->assertStringStartsWith('INV-', $invoice->invoice_number);

        // Menu items are not stock tracked: raw material quantity must be unchanged
        $rawItem->refresh();
        $this->assertEquals($rawQtyBefore, (float)$rawItem->current_quantity);

        // And no 'Sale' inventory transaction should exist for the raw item
        $txn = InventoryTransaction::where('inventory_item_id', $rawItem->id)
            ->where('transaction_type', 'Sale')
            ->first();
        $this->assertNull($txn);
    }

    public function test_pos_checkout_has_no_quantity_stock_limit(): void
    {
        $admin = $this->getAdminUser();
        $menuItem = MenuItem::where('sku', 'DRK-COKE-500')->first();

        $response = $this->actingAs($admin)->postJson('/pos/checkout', [
            'customer_name' => 'Test Customer',
            'payment_method' => 'Cash',
            'items' => [
                [
                    'item_id' => $menuItem->id,
                    'quantity' => 500.0, // No stock limit for menu items
                ]
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
    }

    public function test_inventory_stock_in_increases_quantity(): void
    {
        $admin = $this->getAdminUser();
        $item = InventoryItem::where('sku', 'RAW-CHK-1K')->first();
        $initialQty = (float)$item->current_quantity;

        $response = $this->actingAs($admin)->post("/inventory/{$item->id}/stock-in", [
            'quantity' => 20,
            'purchase_price' => 360.0,
            'reason' => 'Weekend preparation stock',
            'reference_id' => 'PO-TEST-001',
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
