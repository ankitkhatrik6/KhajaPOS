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
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required 
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('email') border-rose-500 @enderror"
                           placeholder="you@restaurant.com" autocomplete="username">
                    @error('email')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" value="" required 
                               class="w-full px-3.5 py-2.5 pr-11 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('password') border-rose-500 @enderror"
                               placeholder="Enter your password" autocomplete="current-password">
                        <button type="button" id="toggle-password" aria-label="Show password"
                                class="absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-400 hover:text-slate-600 focus:outline-none focus:text-emerald-600 transition">
                            <i data-lucide="eye" id="icon-password-eye" class="w-5 h-5"></i>
                            <i data-lucide="eye-off" id="icon-password-eye-off" class="w-5 h-5 hidden"></i>
                        </button>
                    </div>
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
        </div>

        <div class="px-6 py-3 bg-slate-50 border-t border-slate-100 text-center text-xs text-slate-500">
            Authorized staff only. Contact your administrator if you need access.
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.lucide) {
                lucide.createIcons();
            }

            // Show / hide password toggle
            const passwordInput = document.getElementById('password');
            const togglePassword = document.getElementById('toggle-password');
            const iconEye = document.getElementById('icon-password-eye');
            const iconEyeOff = document.getElementById('icon-password-eye-off');
            if (passwordInput && togglePassword && iconEye && iconEyeOff) {
                togglePassword.addEventListener('click', function() {
                    const isHidden = passwordInput.type === 'password';
                    passwordInput.type = isHidden ? 'text' : 'password';
                    iconEye.classList.toggle('hidden', !isHidden);
                    iconEyeOff.classList.toggle('hidden', isHidden);
                    togglePassword.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
                    // Keep the caret in the field so the user can keep typing.
                    passwordInput.focus();
                });
            }
        });
    </script>
</body>
</html>
