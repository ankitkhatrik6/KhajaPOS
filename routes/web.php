<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Home / Landing Page (public)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Dashboard (All logged in staff)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // POS Billing (Admin, Cashier)
    Route::middleware('role:admin,cashier')->group(function () {
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
        Route::get('/pos/item/{id}', [PosController::class, 'quickStockCheck'])->name('pos.item');

        Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
        Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');

        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
        Route::get('/invoices/{invoice}/receipt', [InvoiceController::class, 'receipt'])->name('invoices.receipt');
    });

    // Inventory & Stock Management (Admin, Stock Manager)
    Route::middleware('role:admin,stock_manager')->group(function () {
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('/inventory/create', [InventoryController::class, 'create'])->name('inventory.create');
        Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
        Route::get('/inventory/{item}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
        Route::put('/inventory/{item}', [InventoryController::class, 'update'])->name('inventory.update');
        Route::delete('/inventory/{item}', [InventoryController::class, 'destroy'])->name('inventory.destroy');

        Route::post('/inventory/{item}/stock-in', [InventoryController::class, 'stockIn'])->name('inventory.stock-in');
        Route::post('/inventory/{item}/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');
        Route::post('/inventory/{item}/damage', [InventoryController::class, 'damage'])->name('inventory.damage');
        Route::get('/inventory/transactions', [InventoryController::class, 'transactions'])->name('inventory.transactions');

        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });

    // Menu Management (Admin only - menu items are not stock tracked)
    Route::middleware('role:admin')->group(function () {
        Route::get('/menu', [MenuItemController::class, 'index'])->name('menu.index');
        Route::get('/menu/create', [MenuItemController::class, 'create'])->name('menu.create');
        Route::post('/menu', [MenuItemController::class, 'store'])->name('menu.store');
        Route::get('/menu/{item}/edit', [MenuItemController::class, 'edit'])->name('menu.edit');
        Route::put('/menu/{item}', [MenuItemController::class, 'update'])->name('menu.update');
        Route::post('/menu/{item}/toggle', [MenuItemController::class, 'toggle'])->name('menu.toggle');
        Route::delete('/menu/{item}', [MenuItemController::class, 'destroy'])->name('menu.destroy');
    });

    // Administration Only (Admin)
    Route::middleware('role:admin')->group(function () {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
    });
});
