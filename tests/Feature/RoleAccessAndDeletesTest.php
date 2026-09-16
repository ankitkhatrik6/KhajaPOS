<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Invoice;
use App\Models\Role;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class RoleAccessAndDeletesTest extends TestCase
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

    protected function createUserWithRole(string $slug): User
    {
        $role = Role::where('slug', $slug)->first();

        return User::create([
            'name' => 'Test ' . Str::title($slug),
            'email' => $slug . rand(1000, 9999) . '@khajapos.com',
            'phone' => null,
            'password' => Hash::make('Password@123'),
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    // ------------------------------------------------------------------ //
    // Role based navigation visibility
    // ------------------------------------------------------------------ //

    public function test_cashier_sidebar_shows_only_billing_sections(): void
    {
        $cashier = $this->createUserWithRole('cashier');
        $response = $this->actingAs($cashier)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('POS Billing Terminal');
        $response->assertSee('Sales Orders');
        $response->assertSee('Invoices & Receipts', false);

        $response->assertDontSee('Stock Inventory');
        $response->assertDontSee('Stock History & Log', false);
        $response->assertDontSee('Categories');
        $response->assertDontSee('Operational Reports');
        $response->assertDontSee('Menu Management');
        $response->assertDontSee('Restaurant Settings');
        $response->assertDontSee('Staff & Roles', false);
    }

    public function test_stock_manager_sidebar_shows_only_stock_sections(): void
    {
        $manager = $this->createUserWithRole('stock_manager');
        $response = $this->actingAs($manager)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Stock Inventory');
        $response->assertSee('Stock History & Log', false);
        $response->assertSee('Categories');
        $response->assertSee('Operational Reports');

        $response->assertDontSee('POS Billing Terminal');
        $response->assertDontSee('Sales Orders');
        $response->assertDontSee('Invoices & Receipts', false);
        $response->assertDontSee('Menu Management');
        $response->assertDontSee('Restaurant Settings');
        $response->assertDontSee('Staff & Roles', false);
    }

    public function test_admin_sidebar_shows_all_sections(): void
    {
        $response = $this->actingAs($this->getAdminUser())->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('POS Billing Terminal');
        $response->assertSee('Stock Inventory');
        $response->assertSee('Sales Orders');
        $response->assertSee('Invoices & Receipts', false);
        $response->assertSee('Operational Reports');
        $response->assertSee('Restaurant Settings');
        $response->assertSee('Staff & Roles', false);
    }

    // ------------------------------------------------------------------ //
    // Route level access control (server side)
    // ------------------------------------------------------------------ //

    public function test_non_admin_roles_are_redirected_from_admin_pages(): void
    {
        $cashier = $this->createUserWithRole('cashier');
        $manager = $this->createUserWithRole('stock_manager');

        $this->actingAs($cashier)->get('/users')->assertStatus(302);
        $this->actingAs($cashier)->get('/settings')->assertStatus(302);
        $this->actingAs($cashier)->get('/inventory')->assertStatus(302);
        $this->actingAs($manager)->get('/pos')->assertStatus(302);
        $this->actingAs($manager)->get('/sales')->assertStatus(302);
        $this->actingAs($manager)->get('/invoices')->assertStatus(302);
    }

    // ------------------------------------------------------------------ //
    // Permanent user removal (admin only)
    // ------------------------------------------------------------------ //

    public function test_admin_can_permanently_remove_a_user(): void
    {
        $admin = $this->getAdminUser();
        $target = $this->createUserWithRole('cashier');

        $response = $this->actingAs($admin)->delete("/users/{$target->id}");

        $response->assertRedirect();
        $this->assertNull(User::find($target->id));
    }

    public function test_admin_cannot_permanently_remove_their_own_account(): void
    {
        $admin = $this->getAdminUser();

        $this->actingAs($admin)->delete("/users/{$admin->id}")->assertRedirect();

        $this->assertNotNull(User::find($admin->id));
    }

    public function test_last_active_admin_cannot_be_permanently_removed(): void
    {
        $admin = $this->getAdminUser();

        $this->actingAs($admin)->delete("/users/{$admin->id}")->assertRedirect();

        $this->assertNotNull(User::find($admin->id));
    }

    public function test_user_with_sales_history_cannot_be_permanently_removed(): void
    {
        $sale = Sale::first();
        if (!$sale) {
            $this->markTestSkipped('No seeded sales available.');
            return;
        }

        $admin = $this->getAdminUser();

        $this->actingAs($admin)->delete("/users/{$sale->user_id}")->assertRedirect();

        $this->assertNotNull(User::find($sale->user_id));
    }

    public function test_non_admin_cannot_delete_users(): void
    {
        $cashier = $this->createUserWithRole('cashier');
        $target = $this->createUserWithRole('cashier');

        $this->actingAs($cashier)->delete("/users/{$target->id}")->assertStatus(302);

        $this->assertNotNull(User::find($target->id));
    }

    // ------------------------------------------------------------------ //
    // Invoice deletion (admin only)
    // ------------------------------------------------------------------ //

    public function test_admin_can_delete_invoice_and_its_linked_sale(): void
    {
        $invoice = Invoice::first();
        if (!$invoice) {
            $this->markTestSkipped('No seeded invoices available.');
            return;
        }

        $invoiceId = $invoice->id;
        $saleId = $invoice->sale_id;

        $this->actingAs($this->getAdminUser())->delete("/invoices/{$invoiceId}")->assertRedirect();

        $this->assertNull(Invoice::find($invoiceId));
        $this->assertNull(Sale::find($saleId));
    }

    public function test_cashier_cannot_delete_invoices(): void
    {
        $invoice = Invoice::first();
        if (!$invoice) {
            $this->markTestSkipped('No seeded invoices available.');
            return;
        }

        $cashier = $this->createUserWithRole('cashier');

        $this->actingAs($cashier)->delete("/invoices/{$invoice->id}")->assertStatus(302);

        $this->assertNotNull(Invoice::find($invoice->id));
    }

    // ------------------------------------------------------------------ //
    // Stock item & category deletion (admin only)
    // ------------------------------------------------------------------ //

    public function test_admin_can_delete_inventory_item_with_stock_history(): void
    {
        $admin = $this->getAdminUser();
        $category = Category::first();

        $item = InventoryItem::create([
            'name' => 'Test Spice Mix',
            'sku' => 'SPC-TST-' . rand(1000, 9999),
            'category_id' => $category->id,
            'unit' => 'Packet',
            'purchase_price' => 150.00,
            'selling_price' => 220.00,
            'current_quantity' => 10.00,
            'minimum_stock' => 2.00,
            'supplier' => 'Test Supplier',
            'status' => 'active',
        ]);

        InventoryTransaction::create([
            'inventory_item_id' => $item->id,
            'transaction_type' => 'Purchase',
            'quantity' => 10.00,
            'previous_quantity' => 0.00,
            'new_quantity' => 10.00,
            'purchase_price' => 150.00,
            'reason' => 'Test opening balance',
            'reference_id' => 'TST-0001',
            'user_id' => $admin->id,
        ]);

        $this->actingAs($admin)->delete("/inventory/{$item->id}")->assertRedirect();

        $this->assertNull(InventoryItem::find($item->id));
        $this->assertSame(0, InventoryTransaction::where('inventory_item_id', $item->id)->count());
    }

    public function test_stock_manager_cannot_delete_inventory_items(): void
    {
        $manager = $this->createUserWithRole('stock_manager');
        $item = InventoryItem::first();

        $this->actingAs($manager)->delete("/inventory/{$item->id}")->assertStatus(302);

        $this->assertNotNull(InventoryItem::find($item->id));
    }

    public function test_admin_can_delete_empty_category(): void
    {
        $category = Category::create([
            'name' => 'Test Only Category',
            'slug' => 'test-only-category',
            'description' => 'Empty category for testing hard deletion.',
            'is_active' => true,
        ]);

        $this->actingAs($this->getAdminUser())->delete("/categories/{$category->id}")->assertRedirect();

        $this->assertNull(Category::find($category->id));
    }

    public function test_stock_manager_cannot_delete_categories(): void
    {
        $manager = $this->createUserWithRole('stock_manager');
        $category = Category::create([
            'name' => 'Protected Category',
            'slug' => 'protected-category',
            'description' => null,
            'is_active' => true,
        ]);

        $this->actingAs($manager)->delete("/categories/{$category->id}")->assertStatus(302);

        $this->assertNotNull(Category::find($category->id));
    }
}