<?php

namespace App\Services;

use App\Models\MenuItem;
use App\Models\RestaurantSetting;

class PosService
{
    /**
     * Calculate cart breakdown: subtotal, discount, tax, total, cost, profit.
     */
    public function calculateCart(array $cartItems, float $discount = 0): array
    {
        $subtotal = 0.00;
        $totalCost = 0.00;
        $processedItems = [];

        // Default to the 13% Nepal VAT standard used across the UI when the
        // setting row is missing, so the amount billed always matches the
        // VAT rate shown on the POS screen and the printed invoice.
        $taxPercentage = (float)RestaurantSetting::get('tax_percentage', 13.00);

        foreach ($cartItems as $cartItem) {
            $itemId = (int)($cartItem['item_id'] ?? 0);
            $qty = (float)($cartItem['quantity'] ?? 1);
            if ($qty <= 0) {
                continue;
            }

            $item = MenuItem::find($itemId);
            if (!$item) {
                continue;
            }

            $unitPrice = (float)$item->price;
            $unitCost = (float)$item->cost;
            $lineSubtotal = round($unitPrice * $qty, 2);
            $lineCost = round($unitCost * $qty, 2);
            $lineProfit = round($lineSubtotal - $lineCost, 2);

            $subtotal += $lineSubtotal;
            $totalCost += $lineCost;

            $processedItems[] = [
                'item' => $item,
                'menu_item_id' => $item->id,
                'item_name' => $item->name,
                'unit' => $item->unit,
                'quantity' => $qty,
                'unit_price' => $unitPrice,
                'unit_cost' => $unitCost,
                'subtotal' => $lineSubtotal,
                'total_cost' => $lineCost,
                'profit' => $lineProfit,
            ];
        }

        $discount = max(0, min($discount, $subtotal));
        $taxableAmount = max(0, $subtotal - $discount);
        $tax = round(($taxableAmount * $taxPercentage) / 100, 2);
        $grandTotal = round($taxableAmount + $tax, 2);
        $overallProfit = round(($subtotal - $discount) - $totalCost, 2);

        return [
            'items' => $processedItems,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'tax_percentage' => $taxPercentage,
            'grand_total' => $grandTotal,
            'total_cost' => $totalCost,
            'profit' => $overallProfit,
        ];
    }

    /**
     * Calculate change return for cash tender.
     */
    public function calculateChange(float $total, float $received): float
    {
        return max(0.00, round($received - $total, 2));
    }
}
