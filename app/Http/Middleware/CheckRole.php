<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account has been deactivated.');
        }

        if (empty($roles)) {
            return $next($request);
        }

        $userRoleSlug = $user->role ? $user->role->slug : '';

        // Admin has super access
        if ($userRoleSlug === 'admin') {
            return $next($request);
        }

        if (in_array($userRoleSlug, $roles)) {
            return $next($request);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['error' => 'Unauthorized access.'], 403);
        }

        return redirect()->back()->with('error', 'You do not have permission to access that section.');
    }
}
