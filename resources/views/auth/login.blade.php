<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - KhajaPOS</title>
    <link rel="icon" type="image/png" href="/logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="h-full flex items-center justify-center p-4 bg-gradient-to-br from-emerald-50 via-slate-50 to-white text-slate-800">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-200">
        <!-- Header -->
        <div class="p-6 text-center bg-gradient-to-r from-emerald-50 to-white border-b border-slate-200">
            <img src="/logo.png" alt="KhajaPOS" class="w-12 h-12 object-contain mx-auto mb-3">
            <h1 class="text-xl font-bold tracking-tight text-slate-900">KhajaPOS</h1>
            <p class="text-xs text-slate-500 mt-1 font-medium">Stock Management & Billing POS</p>
        </div>

        <!-- Form Body -->
        <div class="p-6 sm:p-8 space-y-6">
            @if(session('success'))
            <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-lg flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="p-3 bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-lg flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600"></i>
                <span>{{ session('error') }}</span>
            </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email', 'admin@restaurant.com') }}" required 
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('email') border-rose-500 @enderror"
                           placeholder="staff@restaurant.com">
                    @error('email')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Password</label>
                    <input type="password" id="password" name="password" value="password" required 
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-xs text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <span>Remember this device</span>
                    </label>
                    <a href="{{ route('home') }}" class="text-xs font-medium text-slate-500 hover:text-emerald-700">← Back to home</a>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-lg shadow transition text-sm flex items-center justify-center gap-2">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    <span>Sign In to Terminal</span>
                </button>
            </form>

            <!-- 1-Click Quick Demo Logins -->
            <div class="pt-4 border-t border-slate-200">
                <p class="text-center text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">1-Click Demo Login</p>
                <div class="grid grid-cols-3 gap-2">
                    <form action="{{ route('login.quick', 'admin') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-2 px-2 border border-slate-200 hover:border-indigo-400 hover:bg-indigo-50/50 rounded-lg text-center transition group">
                            <span class="block text-xs font-bold text-slate-700 group-hover:text-indigo-600">Admin</span>
                            <span class="block text-[10px] text-slate-400">Full Access</span>
                        </button>
                    </form>

                    <form action="{{ route('login.quick', 'cashier') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-2 px-2 border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50/50 rounded-lg text-center transition group">
                            <span class="block text-xs font-bold text-slate-700 group-hover:text-emerald-600">Cashier</span>
                            <span class="block text-[10px] text-slate-400">POS & Sales</span>
                        </button>
                    </form>

                    <form action="{{ route('login.quick', 'stock') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-2 px-2 border border-slate-200 hover:border-amber-400 hover:bg-amber-50/50 rounded-lg text-center transition group">
                            <span class="block text-xs font-bold text-slate-700 group-hover:text-amber-600">Stock Mgr</span>
                            <span class="block text-[10px] text-slate-400">Inventory</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="px-6 py-3 bg-slate-50 border-t border-slate-100 text-center text-xs text-slate-500">
            Default password for all demo accounts: <code class="font-mono font-semibold text-slate-700">password</code>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.lucide) {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>
