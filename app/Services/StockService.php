<?php

namespace App\Services;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use Exception;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Check if item has enough stock.
     */
    public function checkStockAvailability(int $itemId, float $requestedQuantity): bool
    {
        $item = InventoryItem::find($itemId);
        if (!$item) {
            return false;
        }
        return (float)$item->current_quantity >= (float)$requestedQuantity;
    }

    /**
     * Deduct stock during a sale.
     * Throws exception if stock is insufficient.
     */
    public function deductStock(InventoryItem $item, float $quantity, string $reason = 'Sale', ?string $refId = null, ?int $userId = null): InventoryTransaction
    {
        if ((float)$item->current_quantity < (float)$quantity) {
            throw new Exception("Insufficient stock for item: {$item->name}. Available: {$item->current_quantity} {$item->unit}, Requested: {$quantity} {$item->unit}");
        }

        $prevQty = (float)$item->current_quantity;
        $newQty = $prevQty - (float)$quantity;

        $item->current_quantity = $newQty;
        $item->save();

        return InventoryTransaction::create([
            'inventory_item_id' => $item->id,
            'transaction_type' => 'Sale',
            'quantity' => $quantity,
            'previous_quantity' => $prevQty,
            'new_quantity' => $newQty,
            'purchase_price' => $item->purchase_price,
            'reason' => $reason,
            'reference_id' => $refId,
            'user_id' => $userId,
        ]);
    }

    /**
     * Add stock (Purchase / Restock).
     */
    public function addStock(InventoryItem $item, float $quantity, ?float $purchasePrice = null, ?string $reason = 'Purchase Restock', ?string $refId = null, ?int $userId = null): InventoryTransaction
    {
        $prevQty = (float)$item->current_quantity;
        $newQty = $prevQty + (float)$quantity;

        $item->current_quantity = $newQty;
        if ($purchasePrice !== null && $purchasePrice > 0) {
            $item->purchase_price = $purchasePrice;
        }
        $item->save();

        return InventoryTransaction::create([
            'inventory_item_id' => $item->id,
            'transaction_type' => 'Purchase',
            'quantity' => $quantity,
            'previous_quantity' => $prevQty,
            'new_quantity' => $newQty,
            'purchase_price' => $purchasePrice ?? $item->purchase_price,
            'reason' => $reason,
            'reference_id' => $refId,
            'user_id' => $userId,
        ]);
    }

    /**
     * Manual adjustment (audit reconciliation).
     */
    public function adjustStock(InventoryItem $item, float $newQuantity, string $reason = 'Audit Adjustment', ?int $userId = null): InventoryTransaction
    {
        $prevQty = (float)$item->current_quantity;
        $diff = (float)$newQuantity - $prevQty;
        $type = $diff >= 0 ? 'Adjustment' : 'Adjustment';

        $item->current_quantity = $newQuantity;
        $item->save();

        return InventoryTransaction::create([
            'inventory_item_id' => $item->id,
            'transaction_type' => 'Adjustment',
            'quantity' => abs($diff),
            'previous_quantity' => $prevQty,
            'new_quantity' => $newQuantity,
            'purchase_price' => $item->purchase_price,
            'reason' => $reason,
            'reference_id' => 'ADJ-' . time(),
            'user_id' => $userId,
        ]);
    }

    /**
     * Record damage / wastage.
     */
    public function recordDamage(InventoryItem $item, float $quantity, string $reason = 'Damaged / Expired', ?int $userId = null): InventoryTransaction
    {
        $prevQty = (float)$item->current_quantity;
        $newQty = max(0, $prevQty - (float)$quantity);

        $item->current_quantity = $newQty;
        $item->save();

        return InventoryTransaction::create([
            'inventory_item_id' => $item->id,
            'transaction_type' => 'Damage',
            'quantity' => $quantity,
            'previous_quantity' => $prevQty,
            'new_quantity' => $newQty,
            'purchase_price' => $item->purchase_price,
            'reason' => $reason,
            'reference_id' => 'DMG-' . time(),
            'user_id' => $userId,
        ]);
    }
}
