<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KhajaPOS - Point of Sale & Stock Management</title>
    <link rel="icon" type="image/png" href="/logo.png">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#ecfdf5', 100: '#d1fae5', 200: '#a7f3d0', 300: '#6ee7b7',
                            500: '#10b981', 600: '#059669', 700: '#047857', 800: '#065f46', 900: '#064e3b'
                        }
                    }
                }
            }
        }
    </script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="min-h-screen font-sans antialiased text-slate-800 flex flex-col bg-gradient-to-br from-emerald-50 via-slate-50 to-white">
    <!-- Top Bar -->
    <header class="sticky top-0 z-40 bg-white/85 backdrop-blur border-b border-slate-200 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="/logo.png" alt="KhajaPOS" class="w-10 h-10 object-contain shrink-0">
                <div>
                    <span class="block text-base font-bold tracking-tight text-slate-900 leading-tight">KhajaPOS</span>
                    <span class="text-xs text-emerald-600 font-medium">Point of Sale · Billing &amp; Stock</span>
                </div>
            </a>
            <div class="flex items-center gap-2">
                @if(auth()->check())
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-sm transition">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                    <span>Open Dashboard</span>
                </a>
                @else
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-sm transition">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    <span>Staff Login</span>
                </a>
                @endif
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-20 grid lg:grid-cols-2 gap-10 items-center">
        <div>
            <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wider text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-full px-3 py-1">
                <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                Simple · Fast · Reliable
            </span>
            <h1 class="mt-5 text-4xl sm:text-5xl font-extrabold tracking-tight text-slate-900 leading-tight">
                Run your restaurant <span class="text-emerald-600">billing &amp; stock</span> from one beautiful screen.
            </h1>
            <p class="mt-5 text-base sm:text-lg text-slate-600 leading-relaxed">
                A complete Point-of-Sale, inventory and invoicing system for restaurants and cafes —
                faster checkout with digital or cash payments, raw-material stock tracking, and
                printable VAT receipts for your customers.
            </p>
            <div class="mt-8 flex flex-col sm:flex-row gap-3">
                @if(auth()->check())
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold shadow-md transition">
                    <i data-lucide="arrow-right" class="w-5 h-5"></i>
                    Go to Dashboard
                </a>
                @else
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold shadow-md transition">
                    <i data-lucide="log-in" class="w-5 h-5"></i>
                    Staff Login
                </a>
                @endif
                <a href="#features" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-white border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50 transition">
                    <i data-lucide="chevrons-down" class="w-5 h-5"></i>
                    Explore Features
                </a>
            </div>
        </div>

        <!-- Live "at a glance" card -->
        <div class="justify-self-center w-full max-w-md">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xl p-6">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">At a Glance</span>
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Live
                    </span>
                </div>
                <div class="mt-5 space-y-3.5">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500 flex items-center gap-2"><i data-lucide="package" class="w-4 h-4 text-amber-500"></i>Menu Items in Stock</span>
                        <span class="text-lg font-extrabold text-slate-900">{{ \App\Models\InventoryItem::count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500 flex items-center gap-2"><i data-lucide="tags" class="w-4 h-4 text-amber-500"></i>Menu Categories</span>
                        <span class="text-lg font-extrabold text-slate-900">{{ \App\Models\Category::count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500 flex items-center gap-2"><i data-lucide="receipt" class="w-4 h-4 text-sky-500"></i>Completed Sales</span>
                        <span class="text-lg font-extrabold text-slate-900">{{ \App\Models\Sale::count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500 flex items-center gap-2"><i data-lucide="percent" class="w-4 h-4 text-violet-500"></i>VAT Rate</span>
                        <span class="text-lg font-extrabold text-slate-900">{{ \App\Models\RestaurantSetting::get('tax_percentage', '13.00') }}%</span>
                    </div>
                </div>
                <div class="mt-5 pt-4 border-t border-slate-100 flex items-center gap-2 text-xs text-slate-500">
                    <i data-lucide="circle-check-big" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                    Real-time raw-material stock · VAT receipts · Integrated menu & billing
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-16">
        <div class="text-center max-w-2xl mx-auto">
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Everything your counter &amp; kitchen needs</h2>
            <p class="mt-3 text-slate-600">Designed for real restaurants and cafes — from family kitchens to modern bistros.</p>
        </div>
        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center"><i data-lucide="shopping-cart" class="w-6 h-6"></i></div>
                <h3 class="mt-4 font-bold text-slate-900">POS Billing Terminal</h3>
                <p class="mt-2 text-sm text-slate-500">Fast item search, category filters, discounts, and cash / eSewa / Khalti / card payments with change calculation.</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center"><i data-lucide="package" class="w-6 h-6"></i></div>
                <h3 class="mt-4 font-bold text-slate-900">Raw Material Stock &amp; Inventory</h3>
                <p class="mt-2 text-sm text-slate-500">Track raw materials and supplies with low-stock alerts, restock logs, adjustments and damage tracking — dishes on the menu are billed without stock.</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="w-11 h-11 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center"><i data-lucide="file-text" class="w-6 h-6"></i></div>
                <h3 class="mt-4 font-bold text-slate-900">Invoices &amp; Reports</h3>
                <p class="mt-2 text-sm text-slate-500">Printable VAT invoices and thermal receipts, plus daily sales, payment-method and profit reports.</p>
            </div>
        </div>
    </section>

    <!-- Get started -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-16">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm px-6 py-10 lg:px-10 text-center">
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Ready to run your counter?</h2>
            <p class="mt-3 text-slate-600">Sign in to manage your menu, raw-material stock, billing, invoices and reports.</p>
            <div class="mt-8 flex justify-center gap-3">
                @if(auth()->check())
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold shadow-md transition">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    <span>Open Dashboard</span>
                </a>
                @else
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold shadow-md transition">
                    <i data-lucide="log-in" class="w-5 h-5"></i>
                    <span>Staff Login</span>
                </a>
                @endif
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="mt-auto max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 text-center text-xs text-slate-400">
        <p>© 2026 KhajaPOS · Point of Sale &amp; Stock Management</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.lucide) {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>