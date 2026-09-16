<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - KhajaPOS</title>
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
    <style>
        [x-cloak] { display: none !important; }
        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            body { background: #fff !important; }
        }
    </style>
    @stack('styles')
</head>
<body class="h-full font-sans antialiased text-slate-800 flex flex-col bg-slate-100">
    @php
        $userRole = auth()->user();
        $isAdmin = $userRole->isAdmin();
        $isCashier = $userRole->isCashier();
        $isStockManager = $userRole->isStockManager();
        $canUsePos = $isAdmin || $isCashier;
        $canManageStock = $isAdmin || $isStockManager;
    @endphp
    <!-- Top Navbar (light) -->
    <header class="bg-white text-slate-800 sticky top-0 z-40 border-b border-slate-200 shadow-sm no-print">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Left: Brand & Menu -->
                <div class="flex items-center space-x-3">
                    <button type="button" id="mobile-menu-btn" aria-label="Open menu" class="md:hidden w-10 h-10 p-2.5 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 focus:outline-none active:scale-95">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                        <img src="/logo.png" alt="KhajaPOS" class="w-10 h-10 object-contain shrink-0">
                        <div>
                            <span class="text-base font-bold tracking-tight text-slate-900 block leading-tight">
                                KhajaPOS
                            </span>
                            <span class="text-xs text-emerald-600 font-medium flex items-center gap-1">
                                <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Point of Sale & Stock Management
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Center: POS Quick Action -->
                @if($canUsePos)
                <div class="hidden sm:flex items-center space-x-2">
                    <a href="{{ route('pos.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm transition-all">
                        <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                        <span>Open POS Billing</span>
                    </a>
                </div>
                @endif

                <!-- Right: Clock & User -->
                <div class="flex items-center space-x-3">
                    <div class="hidden lg:flex flex-col text-right">
                        <span class="text-xs font-semibold text-slate-600">
                            {{ now()->setTimezone('Asia/Kathmandu')->format('l, d M Y') }}
                        </span>
                        <span class="text-xs text-slate-400 font-mono">
                            {{ now()->setTimezone('Asia/Kathmandu')->format('h:i A') }} NPT
                        </span>
                    </div>

                    <!-- User Menu -->
                    <div class="relative inline-block text-left" id="role-switcher-dropdown">
                        <button type="button" id="role-dropdown-btn" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 transition shadow-sm">
                            <span class="w-2 h-2 rounded-full {{ auth()->user()->isAdmin() ? 'bg-indigo-500' : (auth()->user()->isCashier() ? 'bg-emerald-500' : 'bg-amber-500') }}"></span>
                            <span>{{ auth()->user()->role->name ?? 'Staff' }}</span>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
                        </button>

                        <div id="role-menu" class="hidden absolute right-0 mt-2 w-56 rounded-lg shadow-lg bg-white ring-1 ring-slate-200 border border-slate-100 z-50 py-1">
                            <div class="px-3 py-2 border-b border-slate-100">
                                <p class="text-xs font-semibold text-slate-800">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <div class="border-t border-slate-100 mt-1 pt-1">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-3 py-1.5 text-xs text-rose-600 hover:bg-rose-50 flex items-center gap-1.5">
                                        <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                                        <span>Sign Out</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="flex-1 flex overflow-hidden">
        <!-- Sidebar (light) -->
        <aside id="sidebar" class="w-64 bg-white border-r border-slate-200 flex-shrink-0 hidden md:flex flex-col justify-between overflow-y-auto no-print">
            <div class="p-4 space-y-6">
                <nav class="space-y-1">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i data-lucide="layout-dashboard" class="w-5 h-5 text-indigo-500"></i>
                        <span>Dashboard</span>
                    </a>

                    @if($canUsePos)
                    <a href="{{ route('pos.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('pos.*') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-emerald-700 hover:bg-emerald-50 hover:text-emerald-800' }}">
                        <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                        <span>POS Billing Terminal</span>
                    </a>
                    @endif

                    @if($isAdmin)
                    <a href="{{ route('menu.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('menu.*') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i data-lucide="utensils" class="w-5 h-5 text-orange-500"></i>
                        <span>Menu Management</span>
                    </a>
                    @endif

                    @if($canManageStock)
                    <div class="pt-3 pb-1">
                        <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Stock & Inventory</p>
                    </div>

                    <a href="{{ route('inventory.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('inventory.index') || request()->routeIs('inventory.create') || request()->routeIs('inventory.edit') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i data-lucide="package" class="w-5 h-5 text-amber-500"></i>
                        <span>Stock Inventory</span>
                    </a>

                    <a href="{{ route('inventory.transactions') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('inventory.transactions') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i data-lucide="history" class="w-5 h-5 text-amber-500"></i>
                        <span>Stock History & Log</span>
                    </a>

                    <a href="{{ route('categories.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('categories.*') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i data-lucide="tags" class="w-5 h-5 text-amber-500"></i>
                        <span>Categories</span>
                    </a>
                    @endif

                    @if($canUsePos)
                    <div class="pt-3 pb-1">
                        <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Billing & Reports</p>
                    </div>

                    <a href="{{ route('sales.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('sales.*') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i data-lucide="receipt" class="w-5 h-5 text-sky-500"></i>
                        <span>Sales Orders</span>
                    </a>

                    <a href="{{ route('invoices.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('invoices.*') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i data-lucide="file-text" class="w-5 h-5 text-sky-500"></i>
                        <span>Invoices & Receipts</span>
                    </a>
                    @endif

                    @if($canManageStock)
                    <div class="pt-3 pb-1">
                        <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Reports & Analytics</p>
                    </div>

                    <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('reports.*') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i data-lucide="bar-chart-2" class="w-5 h-5 text-violet-500"></i>
                        <span>Operational Reports</span>
                    </a>
                    @endif

                    @if($isAdmin)
                    <div class="pt-3 pb-1">
                        <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Administration</p>
                    </div>

                    <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('settings.*') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i data-lucide="settings" class="w-5 h-5 text-slate-500"></i>
                        <span>Restaurant Settings</span>
                    </a>

                    <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('users.*') ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i data-lucide="users" class="w-5 h-5 text-slate-500"></i>
                        <span>Staff & Roles</span>
                    </a>
                    @endif
                </nav>
            </div>

            <!-- Bottom Currency Info Box -->
            <div class="p-4 border-t border-slate-200">
                <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-3 text-xs">
                    <div class="flex items-center justify-between text-emerald-800 font-semibold mb-1">
                        <span>Currency</span>
                        <span class="text-emerald-700">NPR (Rs.)</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Timezone</span>
                        <span>Kathmandu</span>
                    </div>
                    <div class="flex items-center justify-between mt-1 text-slate-600">
                        <span>VAT Rate</span>
                        <span>{{ \App\Models\RestaurantSetting::get('tax_percentage', '13.00') }}%</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto bg-slate-50 flex flex-col">
            <!-- Flash Alerts -->
            <div class="p-4 sm:px-6 lg:px-8 pb-0 no-print">
                @if(session('success'))
                <div class="mb-4 rounded-lg bg-emerald-50 p-4 border border-emerald-200 flex items-start gap-3 text-emerald-800">
                    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                    <div class="flex-1 text-sm font-medium">{{ session('success') }}</div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                @endif

                @if(session('error'))
                <div class="mb-4 rounded-lg bg-rose-50 p-4 border border-rose-200 flex items-start gap-3 text-rose-800">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5"></i>
                    <div class="flex-1 text-sm font-medium">{{ session('error') }}</div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                @endif

                @if($errors->any())
                <div class="mb-4 rounded-lg bg-rose-50 p-4 border border-rose-200 text-rose-800">
                    <div class="flex items-center gap-2 font-semibold text-sm mb-1">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-600"></i>
                        <span>Please correct the errors below:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-0.5 ml-1">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>

            <!-- Page Body -->
            <div class="flex-1 p-4 sm:p-6 lg:p-8">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Mobile Navigation Drawer (light) -->
    <div id="mobile-drawer" class="fixed inset-0 z-50 bg-slate-900/40 hidden md:hidden no-print">
        <div class="w-72 max-w-full bg-white h-full p-4 flex flex-col justify-between overflow-y-auto border-r border-slate-200">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-slate-200">
                    <div class="flex items-center space-x-2">
                        <img src="/logo.png" alt="KhajaPOS" class="w-8 h-8 object-contain shrink-0">
                        <span class="font-bold text-slate-900 text-sm">KhajaPOS</span>
                    </div>
                    <button type="button" id="close-drawer-btn" class="p-1.5 text-slate-500 hover:text-slate-800">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <nav class="mt-4 space-y-1">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-slate-700 hover:bg-slate-100">
                        <i data-lucide="layout-dashboard" class="w-5 h-5 text-indigo-500"></i>
                        <span>Dashboard</span>
                    </a>
                    @if($canUsePos)
                    <a href="{{ route('pos.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold text-emerald-700 hover:bg-emerald-50">
                        <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                        <span>POS Billing</span>
                    </a>
                    @endif
                    @if($isAdmin)
                    <a href="{{ route('menu.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-slate-700 hover:bg-slate-100">
                        <i data-lucide="utensils" class="w-5 h-5 text-orange-500"></i>
                        <span>Menu Management</span>
                    </a>
                    @endif
                    @if($canManageStock)
                    <a href="{{ route('inventory.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-slate-700 hover:bg-slate-100">
                        <i data-lucide="package" class="w-5 h-5 text-amber-500"></i>
                        <span>Inventory</span>
                    </a>
                    @endif
                    @if($canUsePos)
                    <a href="{{ route('sales.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-slate-700 hover:bg-slate-100">
                        <i data-lucide="receipt" class="w-5 h-5 text-sky-500"></i>
                        <span>Sales</span>
                    </a>
                    <a href="{{ route('invoices.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-slate-700 hover:bg-slate-100">
                        <i data-lucide="file-text" class="w-5 h-5 text-sky-500"></i>
                        <span>Invoices</span>
                    </a>
                    @endif
                    @if($canManageStock)
                    <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-slate-700 hover:bg-slate-100">
                        <i data-lucide="bar-chart-2" class="w-5 h-5 text-violet-500"></i>
                        <span>Reports</span>
                    </a>
                    @endif
                </nav>
            </div>
            <div class="pt-4 border-t border-slate-200">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-2 text-center text-sm font-medium text-rose-600 hover:bg-rose-50 rounded-lg">
                        Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Script to initialize Lucide icons and interactivity -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.lucide) {
                lucide.createIcons();
            }

            // Role dropdown toggle
            const roleBtn = document.getElementById('role-dropdown-btn');
            const roleMenu = document.getElementById('role-menu');
            if (roleBtn && roleMenu) {
                roleBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    roleMenu.classList.toggle('hidden');
                });
                document.addEventListener('click', function() {
                    roleMenu.classList.add('hidden');
                });
            }

            // Mobile menu toggle
            const mobileBtn = document.getElementById('mobile-menu-btn');
            const drawer = document.getElementById('mobile-drawer');
            const closeBtn = document.getElementById('close-drawer-btn');
            const closeDrawer = function() { drawer.classList.add('hidden'); };
            if (mobileBtn && drawer) {
                mobileBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    drawer.classList.remove('hidden');
                });
                // Tap the dark backdrop (outside the panel) to close
                drawer.addEventListener('click', function(e) {
                    if (e.target === drawer) closeDrawer();
                });
                // Escape key closes the drawer
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') closeDrawer();
                });
                // Close the drawer when a navigation link is tapped
                drawer.querySelectorAll('a').forEach(function(a) {
                    a.addEventListener('click', closeDrawer);
                });
            }
            if (closeBtn && drawer) {
                closeBtn.addEventListener('click', closeDrawer);
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
