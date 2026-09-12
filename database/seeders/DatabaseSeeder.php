<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Invoice;
use App\Models\MenuItem;
use App\Models\Payment;
use App\Models\RestaurantSetting;
use App\Models\Role;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $adminRole = Role::create([
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'System Administrator with full access to all modules, settings, and reports.',
        ]);

        $cashierRole = Role::create([
            'name' => 'Cashier',
            'slug' => 'cashier',
            'description' => 'Point of Sale Cashier with access to billing, orders, and receipt generation.',
        ]);

        $stockRole = Role::create([
            'name' => 'Stock Manager',
            'slug' => 'stock_manager',
            'description' => 'Inventory and Stock Controller managing restocks, damages, and stock audits.',
        ]);

        // 2. Users — single administrator account
        $adminUser = User::create([
            'name' => 'Administrator',
            'email' => 'admin@khajapos.com',
            'phone' => '+977-9841234567',
            'password' => Hash::make('KhajaPOS@123'),
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);

        // 3. Restaurant Settings
        $settings = [
            'restaurant_name' => 'Himalayan Flavors Cafe & Restaurant',
            'address' => 'Thamel Marg - 29, Kathmandu, Nepal',
            'phone' => '+977-1-4412345',
            'email' => 'info@himalayanflavors.com.np',
            'pan_number' => '601234567',
            'vat_number' => '13% VAT Registered',
            'currency' => 'NPR',
            'tax_percentage' => '13.00',
            'invoice_footer' => 'Thank you for dining with us! Visit again. धन्‍यवाद!',
        ];

        foreach ($settings as $k => $v) {
            RestaurantSetting::set($k, $v);
        }

        // 4. Categories
        $categoriesData = [
            ['name' => 'Food', 'slug' => 'food', 'description' => 'Main courses, momos, noodles and traditional thalis.'],
            ['name' => 'Drinks', 'slug' => 'drinks', 'description' => 'Hot teas, local coffees, beers and soft drinks.'],
            ['name' => 'Snacks', 'slug' => 'snacks', 'description' => 'Khaja sets, fries, choila and quick bites.'],
            ['name' => 'Desserts', 'slug' => 'desserts', 'description' => 'Sweets, gulab jamun, ice creams and jalebi.'],
            ['name' => 'Raw Materials', 'slug' => 'raw-materials', 'description' => 'Kitchen bulk supplies, rice, oil and meats.'],
            ['name' => 'Packaging', 'slug' => 'packaging', 'description' => 'Takeaway parcel boxes, cups and paper bags.'],
            ['name' => 'Other', 'slug' => 'other', 'description' => 'Cleaning, disposables and general miscellaneous.'],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['slug']] = Category::create($cat);
        }

        // 5. Menu Items (dishes & beverages sold at the counter - NOT stock tracked)
        $menuItemsData = [
            // Food
            ['category_slug' => 'food', 'name' => 'Buff Steamed Momo', 'sku' => 'MMO-BUF-STM', 'unit' => 'Piece', 'cost' => 110.00, 'price' => 220.00],
            ['category_slug' => 'food', 'name' => 'Chicken Jhol Momo', 'sku' => 'MMO-CHK-JHL', 'unit' => 'Piece', 'cost' => 140.00, 'price' => 280.00],
            ['category_slug' => 'food', 'name' => 'Veg Fried Momo', 'sku' => 'MMO-VEG-FRD', 'unit' => 'Piece', 'cost' => 80.00, 'price' => 180.00],
            ['category_slug' => 'food', 'name' => 'Chicken Chowmein', 'sku' => 'CHW-CHK-01', 'unit' => 'Piece', 'cost' => 120.00, 'price' => 240.00],
            ['category_slug' => 'food', 'name' => 'Veg Chowmein', 'sku' => 'CHW-VEG-01', 'unit' => 'Piece', 'cost' => 80.00, 'price' => 170.00],
            ['category_slug' => 'food', 'name' => 'Chicken Thukpa', 'sku' => 'TKP-CHK-01', 'unit' => 'Piece', 'cost' => 130.00, 'price' => 260.00],
            ['category_slug' => 'food', 'name' => 'Nepali Thakali Dal Bhat Set (Chicken)', 'sku' => 'DBT-CHK-01', 'unit' => 'Piece', 'cost' => 240.00, 'price' => 480.00],
            ['category_slug' => 'food', 'name' => 'Nepali Thakali Dal Bhat Set (Veg)', 'sku' => 'DBT-VEG-01', 'unit' => 'Piece', 'cost' => 160.00, 'price' => 360.00],
            ['category_slug' => 'food', 'name' => 'Chicken Dum Biryani', 'sku' => 'BRY-CHK-01', 'unit' => 'Piece', 'cost' => 220.00, 'price' => 450.00],
            ['category_slug' => 'food', 'name' => 'Chicken Sekuwa Plate', 'sku' => 'SKW-CHK-01', 'unit' => 'Piece', 'cost' => 180.00, 'price' => 380.00],

            // Snacks
            ['category_slug' => 'snacks', 'name' => 'Newari Khaja Set with Choila', 'sku' => 'KHJ-NWR-01', 'unit' => 'Piece', 'cost' => 190.00, 'price' => 420.00],
            ['category_slug' => 'snacks', 'name' => 'Wai Wai Sadeko', 'sku' => 'SNK-WAI-01', 'unit' => 'Piece', 'cost' => 45.00, 'price' => 120.00],
            ['category_slug' => 'snacks', 'name' => 'Crispy French Fries', 'sku' => 'SNK-FRS-01', 'unit' => 'Piece', 'cost' => 65.00, 'price' => 180.00],
            ['category_slug' => 'snacks', 'name' => 'Peanut Sadeko (Bhatmas Sadeko)', 'sku' => 'SNK-PNT-01', 'unit' => 'Piece', 'cost' => 55.00, 'price' => 150.00],

            // Drinks
            ['category_slug' => 'drinks', 'name' => 'Nepali Milk Tea (Dudh Chiya)', 'sku' => 'DRK-MILK-TEA', 'unit' => 'Piece', 'cost' => 15.00, 'price' => 50.00],
            ['category_slug' => 'drinks', 'name' => 'Special Masala Chiya', 'sku' => 'DRK-MSL-TEA', 'unit' => 'Piece', 'cost' => 22.00, 'price' => 70.00],
            ['category_slug' => 'drinks', 'name' => 'Fresh Lemon Soda (Sweet/Salt)', 'sku' => 'DRK-LMN-SDA', 'unit' => 'Piece', 'cost' => 35.00, 'price' => 110.00],
            ['category_slug' => 'drinks', 'name' => 'Everest Premium Lager Beer 650ml', 'sku' => 'DRK-EVR-650', 'unit' => 'Bottle', 'cost' => 310.00, 'price' => 520.00],
            ['category_slug' => 'drinks', 'name' => 'Gorkha Strong Beer 650ml', 'sku' => 'DRK-GRK-650', 'unit' => 'Bottle', 'cost' => 320.00, 'price' => 540.00],
            ['category_slug' => 'drinks', 'name' => 'Coca-Cola 500ml Pet Bottle', 'sku' => 'DRK-COKE-500', 'unit' => 'Bottle', 'cost' => 60.00, 'price' => 100.00],
            ['category_slug' => 'drinks', 'name' => 'Himalayan Mineral Water 1L', 'sku' => 'DRK-WTR-1L', 'unit' => 'Bottle', 'cost' => 20.00, 'price' => 40.00],

            // Desserts
            ['category_slug' => 'desserts', 'name' => 'Warm Gulab Jamun (2 Pcs)', 'sku' => 'DST-GJM-02', 'unit' => 'Piece', 'cost' => 40.00, 'price' => 110.00],
            ['category_slug' => 'desserts', 'name' => 'Vanilla Scoop Ice Cream', 'sku' => 'DST-ICR-VAN', 'unit' => 'Piece', 'cost' => 50.00, 'price' => 140.00],
        ];

        $menuItems = [];
        foreach ($menuItemsData as $row) {
            $catSlug = $row['category_slug'];
            unset($row['category_slug']);
            $row['category_id'] = $categories[$catSlug]->id;
            $row['is_available'] = true;

            $item = MenuItem::create($row);
            $menuItems[$item->sku] = $item;
        }

        // 6. Raw Materials & Supplies (stock tracked - kitchen ingredients, packaging)
        $inventoryData = [
            // Raw Materials
            ['category_slug' => 'raw-materials', 'name' => 'Basmati Rice Premium (25kg Sack)', 'sku' => 'RAW-RCE-25K', 'unit' => 'Box', 'purchase_price' => 3200.00, 'selling_price' => 3800.00, 'current_quantity' => 6.00, 'minimum_stock' => 2.00, 'supplier' => 'Birgunj Food Wholesalers'],
            ['category_slug' => 'raw-materials', 'name' => 'Mustard Cooking Oil (5L Jar)', 'sku' => 'RAW-OIL-5L', 'unit' => 'Bottle', 'purchase_price' => 1150.00, 'selling_price' => 1400.00, 'current_quantity' => 8.00, 'minimum_stock' => 3.00, 'supplier' => 'Shree Oil Mills Biratnagar'],
            ['category_slug' => 'raw-materials', 'name' => 'Fresh Boneless Chicken (1 Kg)', 'sku' => 'RAW-CHK-1K', 'unit' => 'Kg', 'purchase_price' => 360.00, 'selling_price' => 460.00, 'current_quantity' => 12.50, 'minimum_stock' => 5.00, 'supplier' => 'Valley Poultry Suppliers'],

            // Packaging
            ['category_slug' => 'packaging', 'name' => 'Food Grade Parcel Box 750ml', 'sku' => 'PKG-BOX-750', 'unit' => 'Packet', 'purchase_price' => 8.00, 'selling_price' => 15.00, 'current_quantity' => 150.00, 'minimum_stock' => 30.00, 'supplier' => 'Everest Paper Packaging Co.'],
            ['category_slug' => 'packaging', 'name' => 'Embossed Paper Napkins (Pack of 100)', 'sku' => 'PKG-NPK-100', 'unit' => 'Packet', 'purchase_price' => 60.00, 'selling_price' => 90.00, 'current_quantity' => 4.00, 'minimum_stock' => 10.00, 'supplier' => 'Everest Paper Packaging Co.'],
        ];

        $createdItems = [];
        foreach ($inventoryData as $row) {
            $catSlug = $row['category_slug'];
            unset($row['category_slug']);
            $row['category_id'] = $categories[$catSlug]->id;
            $row['status'] = 'active';

            $item = InventoryItem::create($row);
            $createdItems[$item->sku] = $item;

            // Log opening stock transaction
            if ($item->current_quantity > 0) {
                InventoryTransaction::create([
                    'inventory_item_id' => $item->id,
                    'transaction_type' => 'Purchase',
                    'quantity' => $item->current_quantity,
                    'previous_quantity' => 0,
                    'new_quantity' => $item->current_quantity,
                    'purchase_price' => $item->purchase_price,
                    'reason' => 'Opening Inventory Balance',
                    'reference_id' => 'INIT-STOCK',
                    'user_id' => $adminUser->id,
                    'created_at' => Carbon::now('Asia/Kathmandu')->subDays(5),
                ]);
            }
        }

        // 7. Sample Realistic Completed Sales & Invoices across previous days & today
        //    Sale items reference MENU items only - no inventory stock is deducted.
        $sampleSales = [
            [
                'days_ago' => 3,
                'customer' => 'Rohan Karki',
                'method' => 'Cash',
                'items' => [
                    ['sku' => 'MMO-BUF-STM', 'qty' => 2],
                    ['sku' => 'DRK-EVR-650', 'qty' => 1],
                    ['sku' => 'DRK-MILK-TEA', 'qty' => 1],
                ],
                'discount' => 0.00,
            ],
            [
                'days_ago' => 2,
                'customer' => 'Aayush Adhikari',
                'method' => 'eSewa',
                'items' => [
                    ['sku' => 'DBT-CHK-01', 'qty' => 2],
                    ['sku' => 'DRK-COKE-500', 'qty' => 2],
                ],
                'discount' => 20.00,
            ],
            [
                'days_ago' => 1,
                'customer' => 'Sunita Rai',
                'method' => 'Card',
                'items' => [
                    ['sku' => 'BRY-CHK-01', 'qty' => 2],
                    ['sku' => 'SNK-FRS-01', 'qty' => 1],
                    ['sku' => 'DRK-LMN-SDA', 'qty' => 2],
                ],
                'discount' => 50.00,
            ],
            [
                'days_ago' => 0, // Today
                'customer' => 'Bishal Maharjan',
                'method' => 'Khalti',
                'items' => [
                    ['sku' => 'MMO-CHK-JHL', 'qty' => 2],
                    ['sku' => 'KHJ-NWR-01', 'qty' => 1],
                    ['sku' => 'DRK-GRK-650', 'qty' => 1],
                ],
                'discount' => 30.00,
            ],
            [
                'days_ago' => 0, // Today
                'customer' => 'Pramod Sharma',
                'method' => 'Cash',
                'items' => [
                    ['sku' => 'CHW-CHK-01', 'qty' => 2],
                    ['sku' => 'SNK-WAI-01', 'qty' => 1],
                    ['sku' => 'DRK-MSL-TEA', 'qty' => 2],
                ],
                'discount' => 0.00,
            ],
        ];

        $taxPercentage = 13.00;
        $orderSeq = 1;

        foreach ($sampleSales as $s) {
            $saleDate = Carbon::today('Asia/Kathmandu')->subDays($s['days_ago'])->addHours(12)->addMinutes($orderSeq * 15);
            $subtotal = 0.00;
            $totalCost = 0.00;
            $saleLines = [];

            foreach ($s['items'] as $it) {
                $itemModel = $menuItems[$it['sku']] ?? null;
                if (!$itemModel) {
                    continue;
                }

                $q = (float)$it['qty'];
                $lineSub = round((float)$itemModel->price * $q, 2);
                $lineCost = round((float)$itemModel->cost * $q, 2);
                $lineProf = round($lineSub - $lineCost, 2);

                $subtotal += $lineSub;
                $totalCost += $lineCost;

                $saleLines[] = [
                    'item' => $itemModel,
                    'quantity' => $q,
                    'unit_price' => (float)$itemModel->price,
                    'unit_cost' => (float)$itemModel->cost,
                    'subtotal' => $lineSub,
                    'total_cost' => $lineCost,
                    'profit' => $lineProf,
                ];
            }

            $discount = (float)$s['discount'];
            $taxable = max(0, $subtotal - $discount);
            $tax = round(($taxable * $taxPercentage) / 100, 2);
            $grandTotal = round($taxable + $tax, 2);
            $netProfit = round(($subtotal - $discount) - $totalCost, 2);

            $dateCode = $saleDate->format('Ymd');
            $saleNumber = sprintf('SALE-%s-%04d', $dateCode, $orderSeq);
            $invoiceNumber = sprintf('INV-%s-%04d', $dateCode, $orderSeq);

            $sale = Sale::create([
                'sale_number' => $saleNumber,
                'user_id' => $adminUser->id,
                'customer_name' => $s['customer'],
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $grandTotal,
                'cost' => $totalCost,
                'profit' => $netProfit,
                'payment_method' => $s['method'],
                'payment_status' => 'Paid',
                'notes' => 'Seeded transaction',
                'created_at' => $saleDate,
                'updated_at' => $saleDate,
            ]);

            foreach ($saleLines as $line) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'menu_item_id' => $line['item']->id,
                    'item_name' => $line['item']->name,
                    'unit' => $line['item']->unit,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'unit_cost' => $line['unit_cost'],
                    'subtotal' => $line['subtotal'],
                    'total_cost' => $line['total_cost'],
                    'profit' => $line['profit'],
                    'created_at' => $saleDate,
                    'updated_at' => $saleDate,
                ]);
            }

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'sale_id' => $sale->id,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'grand_total' => $grandTotal,
                'payment_method' => $s['method'],
                'payment_status' => 'Paid',
                'cashier_name' => $adminUser->name,
                'customer_name' => $s['customer'],
                'restaurant_name' => 'Himalayan Flavors Cafe & Restaurant',
                'restaurant_address' => 'Thamel Marg - 29, Kathmandu, Nepal',
                'restaurant_phone' => '+977-1-4412345',
                'restaurant_pan' => '601234567',
                'restaurant_vat' => '13% VAT Registered',
                'invoice_footer' => 'Thank you for dining with us! Visit again. धन्‍यवाद!',
                'created_at' => $saleDate,
                'updated_at' => $saleDate,
            ]);

            Payment::create([
                'sale_id' => $sale->id,
                'invoice_id' => $invoice->id,
                'payment_method' => $s['method'],
                'amount' => $grandTotal,
                'status' => 'Success',
                'transaction_reference' => $s['method'] === 'Cash' ? null : 'TXN-' . strtoupper(Str::random(8)),
                'user_id' => $adminUser->id,
                'created_at' => $saleDate,
                'updated_at' => $saleDate,
            ]);

            $orderSeq++;
        }
    }
}
