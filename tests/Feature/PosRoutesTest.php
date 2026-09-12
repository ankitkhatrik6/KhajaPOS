<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\Invoice;
use App\Models\MenuItem;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosRoutesTest extends TestCase
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

    public function test_dashboard_renders_for_authenticated_user(): void
    {
        $admin = $this->getAdminUser();
        $response = $this->actingAs($admin)->get('/');
        $response->assertStatus(200);
    }

    public function test_pos_terminal_renders(): void
    {
        $admin = $this->getAdminUser();
        $response = $this->actingAs($admin)->get('/pos');
        $response->assertStatus(200);
    }

    public function test_inventory_pages_render(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin)->get('/inventory')->assertStatus(200);
        $this->actingAs($admin)->get('/inventory/create')->assertStatus(200);
        $this->actingAs($admin)->get('/inventory/transactions')->assertStatus(200);

        $item = InventoryItem::first();
        if ($item) {
            $this->actingAs($admin)->get("/inventory/{$item->id}/edit")->assertStatus(200);
        }
    }

    public function test_categories_page_renders(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin)->get('/categories')->assertStatus(200);
    }

    public function test_menu_management_pages_render(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin)->get('/menu')->assertStatus(200);
        $this->actingAs($admin)->get('/menu/create')->assertStatus(200);

        $item = MenuItem::first();
        if ($item) {
            $this->actingAs($admin)->get("/menu/{$item->id}/edit")->assertStatus(200);
        }
    }

    public function test_sales_and_invoices_pages_render(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin)->get('/sales')->assertStatus(200);
        $this->actingAs($admin)->get('/invoices')->assertStatus(200);

        $invoice = Invoice::first();
        if ($invoice) {
            $this->actingAs($admin)->get("/invoices/{$invoice->id}")->assertStatus(200);
            $this->actingAs($admin)->get("/invoices/{$invoice->id}/print")->assertStatus(200);
            $this->actingAs($admin)->get("/invoices/{$invoice->id}/receipt")->assertStatus(200);
        }

        $sale = Sale::first();
        if ($sale) {
            $this->actingAs($admin)->get("/sales/{$sale->id}")->assertStatus(200);
        }
    }

    public function test_reports_page_renders(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin)->get('/reports')->assertStatus(200);
    }

    public function test_settings_page_renders(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin)->get('/settings')->assertStatus(200);
    }

    public function test_users_page_renders(): void
    {
        $admin = $this->getAdminUser();
        $this->actingAs($admin)->get('/users')->assertStatus(200);
    }
}
