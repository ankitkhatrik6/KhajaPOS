<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->latest()->get();
        $roles = Role::all();
        return view('users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = true;

        User::create($validated);

        return back()->with('success', 'Staff member added successfully.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:50'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['nullable', 'string', 'min:6'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->has('is_active');
        $user->update($validated);

        return back()->with('success', 'Staff member updated successfully.');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own active account.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Staff account {$status}.");
    }

    /**
     * Permanently remove a staff member. Deletion is guarded so the current
     * admin cannot delete their own account, the last active admin cannot be
     * removed, and accounts with recorded sales are kept (the sales FK is
     * RESTRICT) — those should be deactivated instead.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot permanently remove your own account.');
        }

        if ($user->isAdmin()) {
            $otherActiveAdmins = User::where('id', '!=', $user->id)
                ->where('is_active', true)
                ->whereHas('role', fn ($q) => $q->where('slug', 'admin'))
                ->count();

            if ($otherActiveAdmins === 0) {
                return back()->with('error', 'Cannot permanently remove the last active administrator account.');
            }
        }

        if ($user->sales()->exists()) {
            return back()->with('error', "Cannot permanently remove '{$user->name}' because they have recorded sales history. Deactivate the account instead.");
        }

        $name = $user->name;
        $user->delete();

        return back()->with('success', "Staff member '{$name}' has been permanently removed.");
    }
}
